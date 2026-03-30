<?php

namespace App\Models;

use App\Enums\BillingInterval;
use App\Enums\Currency;
use App\Enums\SubscriptionEventType;
use App\Enums\SubscriptionStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscription extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'currency' => Currency::class,
            'billing_interval' => BillingInterval::class,
            'status' => SubscriptionStatus::class,
            'start_date' => 'date',
            'next_billing_date' => 'date',
            'ended_at' => 'date',
            'cancelled_at' => 'datetime',
            'paused_at' => 'datetime',
            'resumed_at' => 'datetime',
            'auto_renew' => 'boolean',
            'reminders_enabled' => 'boolean',
        ];
    }

    public function events(): HasMany
    {
        return $this->hasMany(SubscriptionEvent::class)->orderByDesc('occurred_at');
    }

    public function reminders(): HasMany
    {
        return $this->hasMany(SubscriptionReminder::class);
    }

    public function expenseCategory(): BelongsTo
    {
        return $this->belongsTo(ExpenseCategory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', SubscriptionStatus::Active);
    }

    public function scopePaused($query)
    {
        return $query->where('status', SubscriptionStatus::Paused);
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', SubscriptionStatus::Cancelled);
    }

    public function scopeUpcomingRenewal($query, int $days = 7)
    {
        return $query->active()
            ->whereNotNull('next_billing_date')
            ->whereBetween('next_billing_date', [now(), now()->addDays($days)]);
    }

    public function getMonthlyAmountAttribute(): float
    {
        return match ($this->billing_interval) {
            BillingInterval::Weekly => (float) $this->amount * 4.33,
            BillingInterval::Monthly => (float) $this->amount,
            BillingInterval::Quarterly => (float) $this->amount / 3,
            BillingInterval::Yearly => (float) $this->amount / 12,
            default => (float) $this->amount,
        };
    }

    public function getYearlyAmountAttribute(): float
    {
        return $this->monthly_amount * 12;
    }

    public function pause(): void
    {
        $this->update([
            'status' => SubscriptionStatus::Paused,
            'paused_at' => now(),
        ]);

        $this->recordEvent(SubscriptionEventType::Paused);
    }

    public function resume(): void
    {
        $this->update([
            'status' => SubscriptionStatus::Active,
            'resumed_at' => now(),
        ]);

        $this->recordEvent(SubscriptionEventType::Resumed);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => SubscriptionStatus::Cancelled,
            'cancelled_at' => now(),
        ]);

        $this->recordEvent(SubscriptionEventType::Cancelled);
    }

    public function recordEvent(SubscriptionEventType $type, ?string $note = null, ?array $metadata = null): SubscriptionEvent
    {
        return $this->events()->create([
            'event_type' => $type,
            'note' => $note,
            'metadata' => $metadata,
            'occurred_at' => now(),
        ]);
    }
}

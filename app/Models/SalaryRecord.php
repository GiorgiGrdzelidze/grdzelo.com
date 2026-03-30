<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\PayFrequency;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryRecord extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'tax_percentage' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'currency' => Currency::class,
            'pay_frequency' => PayFrequency::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function incomeSource(): BelongsTo
    {
        return $this->belongsTo(IncomeSource::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public static function calculateTax(float $gross, float $taxPercentage): array
    {
        $taxAmount = round($gross * ($taxPercentage / 100), 2);
        $netAmount = round($gross - $taxAmount, 2);

        return [
            'tax_amount' => $taxAmount,
            'net_amount' => $netAmount,
        ];
    }

    public function getAnnualGrossAttribute(): float
    {
        return match ($this->pay_frequency) {
            PayFrequency::Weekly => (float) $this->gross_amount * 52,
            PayFrequency::BiWeekly => (float) $this->gross_amount * 26,
            PayFrequency::Monthly => (float) $this->gross_amount * 12,
            PayFrequency::Quarterly => (float) $this->gross_amount * 4,
            default => (float) $this->gross_amount * 12,
        };
    }

    public function getAnnualTaxAttribute(): float
    {
        return match ($this->pay_frequency) {
            PayFrequency::Weekly => (float) $this->tax_amount * 52,
            PayFrequency::BiWeekly => (float) $this->tax_amount * 26,
            PayFrequency::Monthly => (float) $this->tax_amount * 12,
            PayFrequency::Quarterly => (float) $this->tax_amount * 4,
            default => (float) $this->tax_amount * 12,
        };
    }

    public function getAnnualNetAttribute(): float
    {
        return $this->annual_gross - $this->annual_tax;
    }
}

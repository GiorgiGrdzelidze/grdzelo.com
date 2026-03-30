<?php

namespace App\Models;

use App\Enums\Currency;
use App\Enums\IncomeType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncomeSource extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'currency' => Currency::class,
            'type' => IncomeType::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'is_recurring' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function entries(): HasMany
    {
        return $this->hasMany(IncomeEntry::class);
    }

    public function salaryRecords(): HasMany
    {
        return $this->hasMany(SalaryRecord::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRecurring($query)
    {
        return $query->where('is_recurring', true);
    }

    public function getTotalReceivedAttribute(): float
    {
        return (float) $this->entries()->where('is_received', true)->sum('amount');
    }

    public function getTotalExpectedAttribute(): float
    {
        return (float) $this->entries()->sum('amount');
    }
}

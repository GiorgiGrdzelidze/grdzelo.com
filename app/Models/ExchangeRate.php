<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'rate' => 'decimal:8',
            'rate_date' => 'date',
        ];
    }

    public function scopeForPair($query, string $from, string $to)
    {
        return $query->where('from_currency', $from)->where('to_currency', $to);
    }

    public function scopeOnDate($query, string $date)
    {
        return $query->where('rate_date', $date);
    }

    public function scopeLatestRate($query, string $from, string $to)
    {
        return $query->forPair($from, $to)->orderByDesc('rate_date');
    }

    public static function getRate(string $from, string $to, ?string $date = null): ?float
    {
        if ($from === $to) {
            return 1.0;
        }

        $query = static::forPair($from, $to);

        if ($date) {
            $rate = $query->onDate($date)->first();
            if ($rate) {
                return (float) $rate->rate;
            }
        }

        $rate = static::latestRate($from, $to)->first();

        return $rate ? (float) $rate->rate : null;
    }

    public static function convert(float $amount, string $from, string $to, ?string $date = null): ?array
    {
        $rate = static::getRate($from, $to, $date);

        if ($rate === null) {
            return null;
        }

        return [
            'amount' => round($amount * $rate, 2),
            'rate' => $rate,
        ];
    }
}

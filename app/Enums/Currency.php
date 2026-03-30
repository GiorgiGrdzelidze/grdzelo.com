<?php

namespace App\Enums;

enum Currency: string
{
    case GEL = 'GEL';
    case USD = 'USD';
    case EUR = 'EUR';

    public function label(): string
    {
        return match ($this) {
            self::GEL => 'Georgian Lari (₾)',
            self::USD => 'US Dollar ($)',
            self::EUR => 'Euro (€)',
        };
    }

    public function symbol(): string
    {
        return match ($this) {
            self::GEL => '₾',
            self::USD => '$',
            self::EUR => '€',
        };
    }

    public function symbolPosition(): string
    {
        return match ($this) {
            self::GEL => 'right',
            self::USD => 'left',
            self::EUR => 'left',
        };
    }

    public function format(float $amount, int $decimals = 2): string
    {
        $formatted = number_format($amount, $decimals);

        return match ($this->symbolPosition()) {
            'right' => $formatted . ' ' . $this->symbol(),
            default => $this->symbol() . $formatted,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}

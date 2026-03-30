<?php

namespace App\Enums;

enum PayFrequency: string
{
    case Weekly = 'weekly';
    case BiWeekly = 'bi_weekly';
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';

    public function label(): string
    {
        return match ($this) {
            self::Weekly => 'Weekly',
            self::BiWeekly => 'Bi-Weekly',
            self::Monthly => 'Monthly',
            self::Quarterly => 'Quarterly',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}

<?php

namespace App\Enums;

enum IncomeType: string
{
    case Salary = 'salary';
    case Rent = 'rent';
    case Freelance = 'freelance';
    case Project = 'project';
    case Passive = 'passive';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Salary => 'Salary',
            self::Rent => 'Rent',
            self::Freelance => 'Freelance',
            self::Project => 'Project',
            self::Passive => 'Passive',
            self::Other => 'Other',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}

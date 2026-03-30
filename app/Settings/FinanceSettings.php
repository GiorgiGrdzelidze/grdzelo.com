<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class FinanceSettings extends Settings
{
    public string $base_currency;

    public ?string $statistics_currency;

    public array $supported_currencies;

    public float $default_tax_percentage;

    public int $fiscal_year_start_month;

    public int $default_reminder_days_before;

    public bool $reminders_enabled;

    public static function group(): string
    {
        return 'finance';
    }
}

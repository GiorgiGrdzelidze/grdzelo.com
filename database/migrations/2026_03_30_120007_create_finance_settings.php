<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('finance.base_currency', 'GEL');
        $this->migrator->add('finance.supported_currencies', ['GEL', 'USD', 'EUR']);
        $this->migrator->add('finance.default_tax_percentage', 20.00);
        $this->migrator->add('finance.fiscal_year_start_month', 1);
        $this->migrator->add('finance.default_reminder_days_before', 3);
        $this->migrator->add('finance.reminders_enabled', true);
    }
};

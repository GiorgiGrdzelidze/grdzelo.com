<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.logo', '');
        $this->migrator->add('general.logo_dark', '');
        $this->migrator->add('general.logo_icon', '');
        $this->migrator->add('general.favicon', '');
    }
};

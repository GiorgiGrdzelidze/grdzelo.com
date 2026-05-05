<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->delete('general.about_image');
    }

    public function down(): void
    {
        $this->migrator->add('general.about_image', null);
    }
};

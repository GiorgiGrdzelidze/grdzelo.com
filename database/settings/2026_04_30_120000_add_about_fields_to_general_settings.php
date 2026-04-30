<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.about_image', null);
        $this->migrator->add('general.about_intro', null);
    }
};

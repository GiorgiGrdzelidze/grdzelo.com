<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('seo.default_title', 'grdzelo.com — Product-Minded Software Engineer');
        $this->migrator->add('seo.title_template', '%s — grdzelo.com');
        $this->migrator->add('seo.default_description', 'Product-minded software engineer specializing in Laravel, Filament, and scalable web applications. Building elegant solutions for complex problems.');
        $this->migrator->add('seo.canonical_base', 'https://grdzelo.com');
        $this->migrator->add('seo.default_robots', 'index, follow');
        $this->migrator->add('seo.default_og_title', 'grdzelo.com — Product-Minded Software Engineer');
        $this->migrator->add('seo.default_og_description', 'Product-minded software engineer specializing in Laravel, Filament, and scalable web applications.');
        $this->migrator->add('seo.default_og_image', '');
        $this->migrator->add('seo.default_twitter_title', '');
        $this->migrator->add('seo.default_twitter_description', '');
        $this->migrator->add('seo.default_twitter_image', '');
        $this->migrator->add('seo.twitter_handle', '');
        $this->migrator->add('seo.social_profiles', []);
        $this->migrator->add('seo.schema_person', [
            'name' => 'Grdzelo',
            'url' => 'https://grdzelo.com',
            'jobTitle' => 'Software Engineer',
        ]);
        $this->migrator->add('seo.sitemap_enabled', true);
        $this->migrator->add('seo.indexing_enabled', true);
    }
};

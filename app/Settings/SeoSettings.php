<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SeoSettings extends Settings
{
    public string $default_title;

    public string $title_template;

    public string $default_description;

    public string $canonical_base;

    public string $default_robots;

    public string $default_og_title;

    public string $default_og_description;

    public string $default_og_image;

    public string $default_twitter_title;

    public string $default_twitter_description;

    public string $default_twitter_image;

    public string $twitter_handle;

    public array $social_profiles;

    public array $schema_person;

    public bool $sitemap_enabled;

    public bool $indexing_enabled;

    public static function group(): string
    {
        return 'seo';
    }
}

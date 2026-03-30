<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class GeneralSettings extends Settings
{
    public ?string $site_name;

    public ?string $brand_name;

    public ?string $tagline;

    public ?string $email;

    public ?string $phone;

    public ?string $location;

    public ?string $footer_text;

    public ?string $copyright_text;

    public ?string $analytics_id;

    public ?string $verification_tags;

    public ?string $default_cta_text;

    public ?string $default_cta_url;

    public ?string $contact_email;

    public bool $contact_form_enabled;

    public array $budget_ranges;

    public ?string $logo;

    public ?string $logo_dark;

    public ?string $logo_icon;

    public ?string $favicon;

    public static function group(): string
    {
        return 'general';
    }
}

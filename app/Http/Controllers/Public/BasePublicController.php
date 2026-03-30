<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Settings\GeneralSettings;
use App\Settings\SeoSettings;

abstract class BasePublicController extends Controller
{
    protected function sharedProps(): array
    {
        $general = app(GeneralSettings::class);
        $seo = app(SeoSettings::class);

        return [
            'settings' => [
                'site_name' => $general->site_name,
                'brand_name' => $general->brand_name,
                'tagline' => $general->tagline,
                'email' => $general->email,
                'phone' => $general->phone,
                'location' => $general->location,
                'footer_text' => $general->footer_text,
                'copyright_text' => $general->copyright_text,
                'default_cta_text' => $general->default_cta_text,
                'default_cta_url' => $general->default_cta_url,
                'contact_form_enabled' => $general->contact_form_enabled,
                'logo' => $general->logo,
                'logo_dark' => $general->logo_dark,
                'logo_icon' => $general->logo_icon,
            ],
            'socialLinks' => SocialLink::visible()->ordered()->get(['platform', 'label', 'url', 'username', 'icon']),
            'seoDefaults' => [
                'title' => $seo->default_title,
                'title_template' => $seo->title_template,
                'description' => $seo->default_description,
                'canonical_base' => $seo->canonical_base,
                'og_image' => $seo->default_og_image,
                'twitter_handle' => $seo->twitter_handle,
            ],
        ];
    }

    protected function seoFor($model, string $fallbackTitle = ''): array
    {
        if ($model && method_exists($model, 'toSeoArray')) {
            return $model->toSeoArray();
        }

        $seo = app(SeoSettings::class);

        return [
            'title' => $fallbackTitle ?: $seo->default_title,
            'description' => $seo->default_description,
            'og' => [
                'title' => $fallbackTitle ?: $seo->default_og_title,
                'description' => $seo->default_og_description,
                'image' => $seo->default_og_image,
                'type' => 'website',
            ],
            'twitter' => [
                'title' => $fallbackTitle ?: ($seo->default_twitter_title ?: $seo->default_og_title),
                'description' => $seo->default_twitter_description ?: $seo->default_og_description,
                'image' => $seo->default_twitter_image ?: $seo->default_og_image,
                'card' => 'summary_large_image',
            ],
        ];
    }
}

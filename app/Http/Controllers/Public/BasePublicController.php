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
                'about_image' => $general->about_image,
                'about_intro' => $general->about_intro,
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
        $seo = app(SeoSettings::class);
        $canonical = $this->canonicalForCurrentRequest();

        if ($model && method_exists($model, 'toSeoArray')) {
            $payload = $model->toSeoArray();
            $existing = $payload['canonical'] ?? null;

            // Self-canonical wins over admin override when the override points
            // at our own domain — otherwise /ka/foo and /ru/foo would all
            // collapse to the en URL the admin pasted in. External overrides
            // (different host) are preserved: that's the "this content lives
            // canonically elsewhere" use case.
            if (empty($existing) || str_starts_with((string) $existing, $seo->canonicalBase())) {
                $payload['canonical'] = $canonical;
            }

            return $payload;
        }

        return [
            'title' => $fallbackTitle ?: $seo->default_title,
            'description' => $seo->default_description,
            'canonical' => $canonical,
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

    /**
     * Build the self-canonical URL for the current request.
     * Root '/' keeps its trailing slash to stay consistent with the en hreflang
     * self-reference; deeper paths drop the trailing slash so the canonical
     * matches Laravel's route normalization.
     */
    protected function canonicalForCurrentRequest(): string
    {
        $base = app(SeoSettings::class)->canonicalBase();
        $path = request()->getPathInfo();

        if ($path === '' || $path === '/') {
            return $base.'/';
        }

        return rtrim($base.$path, '/');
    }
}

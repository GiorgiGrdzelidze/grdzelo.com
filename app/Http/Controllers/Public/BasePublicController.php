<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\SocialLink;
use App\Settings\GeneralSettings;
use App\Settings\SeoSettings;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;

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

            // JSON-LD `url` should match the resolved canonical so crawlers
            // can tie the structured data back to the rendered page. Models'
            // `defaultJsonLd()` doesn't know the request URL, so we inject
            // it here whenever the payload omits one (or carries an empty).
            if (isset($payload['jsonld']) && is_array($payload['jsonld']) && empty($payload['jsonld']['url'])) {
                $payload['jsonld']['url'] = $payload['canonical'];
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
     * 301 to the locale-canonical URL when route binding resolved by
     * default-locale slug fallback AND a localized slug now exists for the
     * active locale. Returns null when no redirect is needed (active-locale
     * slug not yet saved, or binding hit on the active locale already).
     *
     * `$segment` is the path segment after the locale prefix —
     * e.g. 'projects' produces /{locale}/projects/{localized_slug}.
     */
    protected function localizedSlugRedirect(Model $model, string $segment): ?RedirectResponse
    {
        if (! method_exists($model, 'wasResolvedByFallback') || ! $model->wasResolvedByFallback()) {
            return null;
        }

        if (! method_exists($model, 'getTranslations')) {
            return null;
        }

        $locale = app()->getLocale();
        $slugs = $model->getTranslations('slug');
        $localized = $slugs[$locale] ?? null;

        if (! is_string($localized) || $localized === '') {
            return null;
        }

        return redirect("/{$locale}/{$segment}/{$localized}", 301);
    }

    /**
     * Build the self-canonical URL for the current request.
     *
     * Every public URL is /{locale}/... post-prefix-mandate, so the canonical
     * is just `canonical_base + request_path` with any trailing slash trimmed
     * — there is no longer a bare `/` to special-case.
     */
    protected function canonicalForCurrentRequest(): string
    {
        $base = app(SeoSettings::class)->canonicalBase();
        $path = request()->getPathInfo();

        return rtrim($base.$path, '/');
    }
}

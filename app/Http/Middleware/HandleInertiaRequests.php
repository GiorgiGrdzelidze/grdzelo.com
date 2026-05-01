<?php

namespace App\Http\Middleware;

use App\Settings\SeoSettings;
use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /** @var array<string, array<string, string>> */
    private static array $translationCache = [];

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            // Lazy: resolved at response-build time, after SetLocale middleware has run.
            // Capturing eagerly here would race with middleware ordering and read the wrong value.
            'locale' => fn () => App::getLocale(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'translations' => fn () => $this->loadTranslations(App::getLocale()),
            'availableLocales' => [
                ['code' => 'en', 'label' => 'EN', 'name' => 'English'],
                ['code' => 'ka', 'label' => 'KA', 'name' => 'ქართული'],
                ['code' => 'ru', 'label' => 'RU', 'name' => 'Русский'],
            ],
            'hreflang' => fn () => $this->hreflangAlternates($request),
        ];
    }

    /**
     * Build the hreflang alternate URLs for the current request.
     * Every public URL is /{locale}/...; alternates emit the same path under
     * each supported locale. x-default points at the default-locale URL.
     *
     * Gated to the public.* route group: locale-switch redirects, the sitemap,
     * and any future non-public Inertia surface get an empty array, so we don't
     * compute (or leak) hreflang for routes that aren't part of the localized set.
     *
     * @return array<int, array{hreflang: string, href: string}>
     */
    private function hreflangAlternates(Request $request): array
    {
        if (! $request->routeIs('public.*') || $request->routeIs('public.locale.switch')) {
            return [];
        }

        $base = app(SeoSettings::class)->canonicalBase();
        $path = '/'.ltrim($request->getPathInfo(), '/');

        // Strip the leading /{locale} segment so we have the locale-agnostic
        // remainder ('/about', '/projects/foo', or '' for the locale root).
        $pattern = '#^/('.Locale::pattern().')(?=/|$)#';
        $stripped = preg_replace($pattern, '', $path) ?? '';

        $alternates = [];
        foreach (Locale::SUPPORTED as $locale) {
            $alternates[] = [
                'hreflang' => $locale,
                'href' => $base.'/'.$locale.$stripped,
            ];
        }

        $alternates[] = [
            'hreflang' => 'x-default',
            'href' => $base.'/'.Locale::default().$stripped,
        ];

        return $alternates;
    }

    /**
     * Load the JSON file for the active locale (lang/{locale}.json).
     * English fills any gaps so partial translations degrade gracefully.
     *
     * Memoized per-process: i18n keys are read on every Inertia request,
     * and the JSON files are only updated by deploys, not at runtime.
     *
     * @return array<string, string>
     */
    private function loadTranslations(string $locale): array
    {
        if (isset(self::$translationCache[$locale])) {
            return self::$translationCache[$locale];
        }

        $fallbackPath = base_path('lang/en.json');
        $base = File::exists($fallbackPath)
            ? json_decode(File::get($fallbackPath), true) ?? []
            : [];

        if ($locale === 'en') {
            return self::$translationCache[$locale] = $base;
        }

        $path = base_path("lang/{$locale}.json");
        if (! File::exists($path)) {
            return self::$translationCache[$locale] = $base;
        }

        $localized = json_decode(File::get($path), true) ?? [];

        return self::$translationCache[$locale] = array_merge($base, $localized);
    }
}

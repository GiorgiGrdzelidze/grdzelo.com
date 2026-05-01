<?php

namespace App\Http\Middleware;

use App\Settings\SeoSettings;
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
     * "/" + "/about" is the en canonical; "/ka" + "/ka/about" is ka; same for ru.
     * x-default points at the unprefixed (default-locale) URL.
     *
     * Returns an empty array for non-Inertia surfaces (sitemap, admin, etc.) which
     * don't render the public Blade root, so this list never reaches them anyway.
     *
     * @return array<int, array{hreflang: string, href: string}>
     */
    private function hreflangAlternates(Request $request): array
    {
        $base = app(SeoSettings::class)->canonicalBase();
        $path = '/'.ltrim($request->getPathInfo(), '/');

        $stripped = preg_replace('#^/(ka|ru)(?=/|$)#', '', $path) ?: '/';
        if ($stripped === '') {
            $stripped = '/';
        }

        $unprefixed = $base.($stripped === '/' ? '' : $stripped);
        $kaPath = $stripped === '/' ? '/ka' : '/ka'.$stripped;
        $ruPath = $stripped === '/' ? '/ru' : '/ru'.$stripped;

        return [
            ['hreflang' => 'en', 'href' => $unprefixed === $base ? $base.'/' : $unprefixed],
            ['hreflang' => 'ka', 'href' => $base.$kaPath],
            ['hreflang' => 'ru', 'href' => $base.$ruPath],
            ['hreflang' => 'x-default', 'href' => $unprefixed === $base ? $base.'/' : $unprefixed],
        ];
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

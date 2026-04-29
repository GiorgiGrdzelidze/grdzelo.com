<?php

namespace App\Http\Middleware;

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
        $locale = App::getLocale();

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'locale' => $locale,
            'translations' => fn () => $this->loadTranslations($locale),
            'availableLocales' => [
                ['code' => 'en', 'label' => 'EN', 'name' => 'English'],
                ['code' => 'ka', 'label' => 'KA', 'name' => 'ქართული'],
                ['code' => 'ru', 'label' => 'RU', 'name' => 'Русский'],
            ],
        ];
    }

    /**
     * Load the JSON file for the active locale (lang/{locale}.json).
     * English fills any gaps so partial translations degrade gracefully.
     *
     * @return array<string, string>
     */
    private function loadTranslations(string $locale): array
    {
        $fallbackPath = base_path('lang/en.json');
        $base = File::exists($fallbackPath)
            ? json_decode(File::get($fallbackPath), true) ?? []
            : [];

        if ($locale === 'en') {
            return $base;
        }

        $path = base_path("lang/{$locale}.json");
        if (! File::exists($path)) {
            return $base;
        }

        $localized = json_decode(File::get($path), true) ?? [];

        return array_merge($base, $localized);
    }
}

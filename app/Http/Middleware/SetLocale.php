<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocale — picks the active locale for the request.
 *
 * Order of precedence:
 *   1. Session value (last explicit choice)
 *   2. Cookie value (cross-session memory)
 *   3. Accept-Language header (best-fit match)
 *   4. App default ("en")
 *
 * Once resolved, the locale is set on the App, persisted to session,
 * and dropped into a 1-year cookie so navigation feels sticky.
 */
class SetLocale
{
    /** @var array<int, string> */
    private const SUPPORTED = ['en', 'ka', 'ru'];

    private const COOKIE = 'locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        $response = $next($request);

        if (method_exists($response, 'cookie')) {
            $response->cookie(self::COOKIE, $locale, 60 * 24 * 365); // 1 year
        }

        return $response;
    }

    private function resolveLocale(Request $request): string
    {
        // 1. Session
        $session = $request->session()->get('locale');
        if (in_array($session, self::SUPPORTED, true)) {
            return $session;
        }

        // 2. Cookie
        $cookie = $request->cookie(self::COOKIE);
        if (in_array($cookie, self::SUPPORTED, true)) {
            return $cookie;
        }

        // 3. Accept-Language header
        $best = $request->getPreferredLanguage(self::SUPPORTED);
        if (in_array($best, self::SUPPORTED, true)) {
            return $best;
        }

        // 4. Default
        return config('app.fallback_locale', 'en');
    }
}

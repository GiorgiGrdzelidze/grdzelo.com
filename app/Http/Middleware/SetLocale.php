<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Support\Locale;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetLocale — picks the active locale for the request.
 *
 * Order of precedence:
 *   1. URL prefix segment, e.g. /ka/about — read from the path itself so
 *      it works whether the route param is named {locale} or absent (the
 *      sitemap, robots, and locale-switch routes have no {locale}).
 *   2. Session value (last explicit choice on an unprefixed URL).
 *   3. Cookie value (cross-session memory).
 *   4. Accept-Language header (best-fit match).
 *   5. App default from config('app.locale').
 *
 * Once resolved, the locale is set on the App, registered as a default
 * route parameter (so route('public.home') auto-fills /{locale}/...),
 * and persisted to session + cookie ONLY when the value changed —
 * unconditional writes spammed the session and broke caching strategies.
 */
class SetLocale
{
    private const SESSION_KEY = 'locale';

    private const COOKIE = 'locale';

    private const COOKIE_LIFETIME_MINUTES = 60 * 24 * 365;

    public function handle(Request $request, Closure $next): Response
    {
        $resolved = $this->resolveLocale($request);

        App::setLocale($resolved);
        URL::defaults(['locale' => $resolved]);

        $sessionLocale = $request->session()->get(self::SESSION_KEY);
        if ($sessionLocale !== $resolved) {
            $request->session()->put(self::SESSION_KEY, $resolved);
        }

        $response = $next($request);

        if (method_exists($response, 'cookie')) {
            // Re-read in case LocaleController switched mid-request.
            $final = $request->session()->get(self::SESSION_KEY, $resolved);
            if (! Locale::isSupported($final)) {
                $final = $resolved;
            }

            if ($request->cookie(self::COOKIE) !== $final) {
                $response->cookie(self::COOKIE, $final, self::COOKIE_LIFETIME_MINUTES);
            }
        }

        return $response;
    }

    private function resolveLocale(Request $request): string
    {
        // 1. URL prefix segment — first path segment if it's a supported locale.
        //    Read from the path (not a route param) because the sitemap, robots,
        //    and locale-switch routes don't carry a {locale} parameter.
        $first = explode('/', trim($request->getPathInfo(), '/'), 2)[0] ?? '';
        if ($first !== '' && Locale::isSupported($first)) {
            return $first;
        }

        // 2. Session
        $session = $request->session()->get(self::SESSION_KEY);
        if (is_string($session) && Locale::isSupported($session)) {
            return $session;
        }

        // 3. Cookie
        $cookie = $request->cookie(self::COOKIE);
        if (is_string($cookie) && Locale::isSupported($cookie)) {
            return $cookie;
        }

        // 4. Accept-Language header
        $best = $request->getPreferredLanguage(Locale::SUPPORTED);
        if (is_string($best) && Locale::isSupported($best)) {
            return $best;
        }

        // 5. Default
        return Locale::default();
    }
}

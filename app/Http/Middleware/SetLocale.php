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
 *   1. URL prefix segment, e.g. /ka/about — read from the path, not from
 *      a route parameter, because the route uses literal /ka and /ru
 *      prefixes (a {locale} param would land positionally in controller
 *      actions like show(Project) and shadow the bound model).
 *   2. Session value (last explicit choice on an unprefixed URL)
 *   3. Cookie value (cross-session memory)
 *   4. Accept-Language header (best-fit match)
 *   5. App default ("en")
 *
 * Once resolved, the locale is set on the App, persisted to session,
 * and dropped into a 1-year cookie so navigation feels sticky.
 */
class SetLocale
{
    /** @var array<int, string> */
    public const SUPPORTED = ['en', 'ka', 'ru'];

    /**
     * Locales that may appear as a URL prefix segment. 'en' is canonical
     * at the unprefixed root, so it must NEVER be accepted from /{locale}/...
     *
     * @var array<int, string>
     */
    private const PREFIX_LOCALES = ['ka', 'ru'];

    private const COOKIE = 'locale';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        App::setLocale($locale);
        $request->session()->put('locale', $locale);

        $response = $next($request);

        if (method_exists($response, 'cookie')) {
            // Re-read session so an explicit switch by LocaleController during
            // this request wins over the value resolved at handle() time.
            $final = $request->session()->get('locale', $locale);
            if (! in_array($final, self::SUPPORTED, true)) {
                $final = $locale;
            }
            $response->cookie(self::COOKIE, $final, 60 * 24 * 365); // 1 year
        }

        return $response;
    }

    private function resolveLocale(Request $request): string
    {
        // 0. URL-prefixed locale segment wins outright — the URL is canonical.
        //    Read from the path (not a route param) because routes use literal
        //    /ka and /ru prefixes; a {locale} param would be passed positionally
        //    by Laravel's dispatcher and break model-bound controller actions.
        //    Restricted to PREFIX_LOCALES so 'en' can't sneak in via /en/foo
        //    (which would create a duplicate-content variant of /foo).
        $first = explode('/', trim($request->getPathInfo(), '/'), 2)[0] ?? '';
        if (in_array($first, self::PREFIX_LOCALES, true)) {
            return $first;
        }

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

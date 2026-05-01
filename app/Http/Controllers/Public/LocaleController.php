<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    private const COOKIE = 'locale';

    private const COOKIE_LIFETIME_MINUTES = 60 * 24 * 365;

    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(Locale::isSupported($locale), 404);

        $request->session()->put('locale', $locale);

        $return = $this->safeReturnPath($request->query('return'), $locale);

        return redirect($return)->cookie(
            self::COOKIE,
            $locale,
            self::COOKIE_LIFETIME_MINUTES,
        );
    }

    /**
     * Sanitize ?return= and rewrite its leading locale segment to the new one.
     *
     * Every public URL is /{locale}/... post-prefix-mandate, so switching from
     * /ka/projects to ru must redirect to /ru/projects, not /ka/projects.
     * If the return path has no recognizable locale segment, prepend the new
     * locale to it. Open-redirect safety: only paths starting with a single
     * "/" are accepted; anything else falls back to /{new_locale}.
     */
    private function safeReturnPath(?string $candidate, string $newLocale): string
    {
        $localeRoot = '/'.$newLocale;

        if (! is_string($candidate) || $candidate === '') {
            return $localeRoot;
        }

        if (! str_starts_with($candidate, '/') || str_starts_with($candidate, '//')) {
            return $localeRoot;
        }

        $first = explode('/', trim($candidate, '/'), 2)[0] ?? '';
        if (Locale::isSupported($first)) {
            $rest = substr($candidate, strlen('/'.$first));

            return $localeRoot.$rest;
        }

        return $localeRoot.$candidate;
    }
}

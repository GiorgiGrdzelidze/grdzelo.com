<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    /** @var array<int, string> */
    private const SUPPORTED = ['en', 'ka', 'ru'];

    private const COOKIE = 'locale';

    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(in_array($locale, self::SUPPORTED, true), 404);

        $request->session()->put('locale', $locale);

        $return = $this->safeReturnPath($request->query('return'));

        return redirect($return)->cookie(
            self::COOKIE,
            $locale,
            60 * 24 * 365,
        );
    }

    private function safeReturnPath(?string $candidate): string
    {
        if (! is_string($candidate) || $candidate === '') {
            return '/';
        }

        if (! str_starts_with($candidate, '/') || str_starts_with($candidate, '//')) {
            return '/';
        }

        return $candidate;
    }
}

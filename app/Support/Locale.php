<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Single source of truth for the supported locale set.
 *
 * Middleware, route registration, sitemap, language switcher, and Filament
 * panel all read from this class. Adding or removing a locale is a one-line
 * change here — never duplicate the list elsewhere.
 */
final class Locale
{
    /** @var array<int, string> */
    public const SUPPORTED = ['en', 'ka', 'ru'];

    public static function default(): string
    {
        // config('app.default_locale') is a stable mirror of APP_LOCALE.
        // We deliberately don't read 'app.locale' here — App::setLocale()
        // mutates that at runtime to track the active request locale.
        $configured = config('app.default_locale');

        return is_string($configured) && self::isSupported($configured)
            ? $configured
            : 'en';
    }

    public static function isSupported(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED, true);
    }

    /**
     * URL prefix segment for a locale, including the leading slash.
     * `Locale::prefix()` returns the prefix for the active locale.
     */
    public static function prefix(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        return self::isSupported($locale) ? '/'.$locale : '/'.self::default();
    }

    /**
     * Pipe-joined regex alternation of supported locales — for route `where`
     * constraints: `->where('locale', Locale::pattern())`.
     */
    public static function pattern(): string
    {
        return implode('|', self::SUPPORTED);
    }
}

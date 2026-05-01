<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Support\Locale;
use Closure;
use Filament\Schemas\Components\Tabs;

/**
 * Builds the per-locale tab strip used by every translatable resource.
 *
 * The caller supplies a single closure that returns the field schema for one
 * locale; we render that closure once per supported locale, passing the locale
 * code and an `$isDefault` flag so the resource can mark `required()` only on
 * the default-locale tab. Field names use dot-notation (`title.en`) which the
 * `HandlesTranslatableForm` page trait expands/collapses around fill + save.
 *
 * Hand-rolled because no Filament-v5-compatible Spatie translatable plugin
 * exists yet — the Resource API is simple enough that rolling our own keeps
 * us off an external dep that may never ship for our Filament major.
 */
final class TranslatableSchema
{
    /**
     * @param  Closure(string $locale, bool $isDefault): array<int, mixed>  $fieldsBuilder
     */
    public static function tabs(Closure $fieldsBuilder): Tabs
    {
        $defaultLocale = Locale::default();

        $tabs = [];
        foreach (Locale::SUPPORTED as $locale) {
            $tabs[] = Tabs\Tab::make(strtoupper($locale))
                ->schema($fieldsBuilder($locale, $locale === $defaultLocale));
        }

        return Tabs::make('Translations')->tabs($tabs);
    }
}

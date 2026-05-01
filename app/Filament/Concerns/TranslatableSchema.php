<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Support\Locale;
use Closure;
use Filament\Forms;
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

    /**
     * Standard per-locale SEO tab strip — every HasSeoFields resource embeds
     * this inside its top-level "SEO" tab. The 10 translatable SEO string
     * fields appear once per supported locale; non-translatable booleans
     * (noindex, nofollow), structural enums (og_type, twitter_card), media
     * uploads (og_image, twitter_image), and schema_json stay outside this
     * helper, in the resource's own SEO tab body.
     */
    public static function seoTabs(): Tabs
    {
        return self::tabs(fn (string $locale, bool $isDefault) => [
            Forms\Components\TextInput::make("meta_title.{$locale}")
                ->label('Meta Title')->maxLength(255),
            Forms\Components\Textarea::make("meta_description.{$locale}")
                ->label('Meta Description')->maxLength(500)->rows(3),
            Forms\Components\TextInput::make("canonical_url.{$locale}")
                ->label('Canonical URL')->url()->maxLength(255),
            Forms\Components\TextInput::make("robots.{$locale}")
                ->label('Robots')->maxLength(255)->placeholder('index, follow'),
            Forms\Components\TextInput::make("og_title.{$locale}")
                ->label('OG Title')->maxLength(255),
            Forms\Components\Textarea::make("og_description.{$locale}")
                ->label('OG Description')->maxLength(500)->rows(2),
            Forms\Components\TextInput::make("og_image_alt.{$locale}")
                ->label('OG Image Alt')->maxLength(255),
            Forms\Components\TextInput::make("twitter_title.{$locale}")
                ->label('Twitter Title')->maxLength(255),
            Forms\Components\Textarea::make("twitter_description.{$locale}")
                ->label('Twitter Description')->maxLength(500)->rows(2),
            Forms\Components\TextInput::make("twitter_image_alt.{$locale}")
                ->label('Twitter Image Alt')->maxLength(255),
        ]);
    }

    /**
     * Per-locale JSON-LD editor — one Textarea tab per locale, JSON-validated.
     * Hand-rolled in lieu of Filament's Code component (not in v5 forms).
     * Empty values fall through to the model's `defaultJsonLd()` at render time;
     * a custom payload here overrides the default for that locale only.
     */
    public static function jsonLdTabs(): Tabs
    {
        return self::tabs(fn (string $locale, bool $isDefault) => [
            Forms\Components\Textarea::make("jsonld.{$locale}")
                ->label('JSON-LD ('.strtoupper($locale).')')
                ->rows(8)
                ->dehydrateStateUsing(fn ($state) => is_string($state) && trim($state) !== ''
                    ? json_decode($state, true)
                    : null)
                ->formatStateUsing(fn ($state) => is_array($state)
                    ? json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
                    : $state)
                ->rule('json')
                ->helperText('Leave blank to fall back to the auto-generated default. Custom JSON-LD overrides per locale.'),
        ]);
    }
}

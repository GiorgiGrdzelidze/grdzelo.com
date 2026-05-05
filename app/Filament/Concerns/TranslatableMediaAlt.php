<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Support\Locale;
use Filament\Forms;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Per-locale alt-text inputs for `SpatieMediaLibraryFileUpload` collections.
 *
 * Filament v5's spatie-media-library plugin doesn't expose per-file metadata
 * fields out of the box, so we render a sibling Tabs strip — one Tab per
 * supported locale — and sync the values to `media.custom_properties->alt`
 * via the EditRecord page's `mutateFormDataBeforeFill` / `afterSave`
 * lifecycle hooks (see `HandlesMediaAltState`).
 *
 * Single-file collections only. Multi-file collections (gallery / photos /
 * screenshots) defer per-image alt to a future arc — controllers fall back
 * to a synthesized title-based alt at render time.
 *
 * The state-path convention is `media_alt.<collection>.<locale>` so multiple
 * collections per resource don't collide.
 */
trait TranslatableMediaAlt
{
    public static function mediaAltField(string $collection, ?string $label = null): Tabs
    {
        $tabs = [];

        foreach (Locale::SUPPORTED as $locale) {
            $tabs[] = Tab::make(strtoupper($locale))->schema([
                Forms\Components\TextInput::make("media_alt.{$collection}.{$locale}")
                    ->label($label ?? 'Alt text')
                    ->maxLength(255)
                    ->helperText($locale === Locale::default()
                        ? 'Required at the default locale; other locales fall back to it when blank.'
                        : 'Optional. Falls back to the default-locale alt when blank.'
                    ),
            ]);
        }

        return Tabs::make("media_alt_{$collection}")->tabs($tabs);
    }

    /**
     * Resolve the active-locale alt with default-locale fallback.
     * Accepts the raw `custom_properties->alt` value (typically an array
     * shape `['en' => '…', 'ka' => '…']`); returns null when no string lands.
     */
    public static function resolveAlt(mixed $altMap): ?string
    {
        if (is_string($altMap) && $altMap !== '') {
            return $altMap;
        }

        if (! is_array($altMap)) {
            return null;
        }

        $active = $altMap[app()->getLocale()] ?? null;

        if (is_string($active) && $active !== '') {
            return $active;
        }

        $default = $altMap[Locale::default()] ?? null;

        return is_string($default) && $default !== '' ? $default : null;
    }

    public static function mediaAlt(?Media $media): ?string
    {
        return $media ? self::resolveAlt($media->getCustomProperty('alt')) : null;
    }
}

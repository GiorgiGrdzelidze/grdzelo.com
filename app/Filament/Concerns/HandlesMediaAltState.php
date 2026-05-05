<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use Filament\Resources\Pages\EditRecord;
use Spatie\MediaLibrary\HasMedia;

/**
 * EditRecord-page side of the per-locale alt contract. Pages that mix in
 * `TranslatableMediaAlt::mediaAltField()` somewhere in their resource form
 * also mix in this trait on the EditRecord page; together they round-trip
 * `media.custom_properties->alt = {en, ka, ru}` through the form state path
 * `media_alt.<collection>.<locale>`.
 *
 * Each consumer overrides `mediaAltCollections()` to declare which
 * single-file collections carry editable alts.
 *
 * @phpstan-require-extends EditRecord
 */
trait HandlesMediaAltState
{
    /**
     * @return array<int, string>
     */
    protected function mediaAltCollections(): array
    {
        return [];
    }

    protected function fillMediaAltState(array $data): array
    {
        $record = $this->getRecord();

        if (! $record instanceof HasMedia) {
            return $data;
        }

        $state = $data['media_alt'] ?? [];

        foreach ($this->mediaAltCollections() as $collection) {
            $media = $record->getFirstMedia($collection);
            $alt = $media?->getCustomProperty('alt');

            $state[$collection] = is_array($alt) ? $alt : [];
        }

        $data['media_alt'] = $state;

        return $data;
    }

    protected function persistMediaAltState(): void
    {
        $record = $this->getRecord();

        if (! $record instanceof HasMedia) {
            return;
        }

        $state = $this->data['media_alt'] ?? [];

        foreach ($this->mediaAltCollections() as $collection) {
            $media = $record->getFirstMedia($collection);

            if (! $media) {
                continue;
            }

            $alts = is_array($state[$collection] ?? null)
                ? array_filter($state[$collection], fn ($v) => is_string($v) && $v !== '')
                : [];

            $media->setCustomProperty('alt', $alts);
            $media->save();
        }
    }

    /**
     * Strips the virtual `media_alt` key before save so it doesn't reach
     * the model. The actual persistence happens in `afterSave`, after media
     * rows have been written.
     */
    protected function stripMediaAltFromSave(array $data): array
    {
        unset($data['media_alt']);

        return $data;
    }
}

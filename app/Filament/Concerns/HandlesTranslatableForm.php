<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Support\Locale;

/**
 * Lifecycle hooks every translatable Resource's Create/Edit pages mix in.
 *
 *   - `mutateFormDataBeforeFill` (EditRecord) reads `getTranslations()` so each
 *     locale's value lands under its own dot-notation key (`title.en`,
 *     `title.ka`, ...). The form schema's per-locale TextInputs read those
 *     keys directly — no per-input formatStateUsing needed.
 *
 *   - `mutateFormDataBeforeSave` / `mutateFormDataBeforeCreate` strip out any
 *     locale keys outside the supported set (a defensive scrub in case the
 *     supported list ever shrinks) and pass the per-field arrays through to
 *     Spatie's HasTranslations, which handles JSON storage.
 *
 * Pages opt in with `use HandlesTranslatableForm;` — applies to both
 * EditRecord and CreateRecord; methods that don't apply on a given page are
 * ignored by Filament's lifecycle dispatcher.
 */
trait HandlesTranslatableForm
{
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();

        if (! method_exists($record, 'getTranslations')) {
            return $data;
        }

        foreach ($this->translatableFields() as $field) {
            $data[$field] = $record->getTranslations($field);
        }

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->filterTranslatableLocales($data);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->filterTranslatableLocales($data);
    }

    /**
     * @return array<int, string>
     */
    private function translatableFields(): array
    {
        $modelClass = static::getResource()::getModel();
        $sample = new $modelClass;

        return is_array($sample->translatable ?? null) ? $sample->translatable : [];
    }

    private function filterTranslatableLocales(array $data): array
    {
        $supported = Locale::SUPPORTED;

        foreach ($this->translatableFields() as $field) {
            if (! isset($data[$field]) || ! is_array($data[$field])) {
                continue;
            }

            $data[$field] = array_intersect_key($data[$field], array_flip($supported));
        }

        return $data;
    }
}

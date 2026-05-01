<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Support\Locale;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

/**
 * `EN · KA · RU` style completeness badge column for any translatable list view.
 *
 * Reads `$record->getTranslations($field)` for the supplied "representative"
 * field (typically `title` or `name`) and styles each locale code based on
 * whether that locale's value is set. Lets the admin spot rows missing a
 * translation at a glance — independent of any plugin.
 */
final class TranslationCompleteness
{
    public static function column(string $field = 'title'): Tables\Columns\TextColumn
    {
        return Tables\Columns\TextColumn::make('translations_completeness')
            ->label('Translations')
            ->state(function (Model $record) use ($field) {
                if (! method_exists($record, 'getTranslations')) {
                    return '';
                }

                $translations = $record->getTranslations($field);

                $segments = [];
                foreach (Locale::SUPPORTED as $locale) {
                    $value = $translations[$locale] ?? null;
                    $present = is_string($value) && $value !== '';
                    $segments[] = $present
                        ? '<span style="color:var(--success-600)">'.strtoupper($locale).'</span>'
                        : '<span style="color:var(--gray-400)">'.strtoupper($locale).'</span>';
                }

                return implode(' · ', $segments);
            })
            ->html()
            ->toggleable();
    }
}

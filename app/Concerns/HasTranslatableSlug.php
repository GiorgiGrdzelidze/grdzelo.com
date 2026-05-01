<?php

declare(strict_types=1);

namespace App\Concerns;

use App\Support\Locale;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Adds per-locale slug behavior to a model that already uses Spatie's
 * `HasTranslations` trait.
 *
 *   - Route model binding resolves by the active locale's slug first, falling
 *     back to the default-locale slug so older `/ka/photography` URLs still
 *     work until an admin saves a Georgian slug. Controllers can call
 *     `wasResolvedByFallback()` to decide whether to 301 to the canonical
 *     locale URL.
 *
 *   - On save, any locale whose slug is blank gets auto-generated from the
 *     same locale's title (or `name`, per `translatableSlugSource()`),
 *     transliterated to ASCII via `Any-Latin; Latin-ASCII; Lower()`.
 *
 * The model must declare `'slug'` and the slug-source field in its
 * `$translatable` array (Spatie's contract for per-locale columns).
 */
trait HasTranslatableSlug
{
    private bool $resolvedByFallback = false;

    public function resolveRouteBinding($value, $field = null): ?Model
    {
        $field = $field ?? $this->getRouteKeyName();
        $active = app()->getLocale();
        $default = Locale::default();

        $found = $this->newQuery()->where($field.'->'.$active, $value)->first();
        if ($found instanceof Model) {
            return $found;
        }

        if ($active === $default) {
            return null;
        }

        $found = $this->newQuery()->where($field.'->'.$default, $value)->first();
        if ($found instanceof Model) {
            $found->resolvedByFallback = true;

            return $found;
        }

        return null;
    }

    public function wasResolvedByFallback(): bool
    {
        return $this->resolvedByFallback;
    }

    public static function bootHasTranslatableSlug(): void
    {
        static::saving(function (Model $model): void {
            if (! method_exists($model, 'getTranslations') || ! method_exists($model, 'setTranslations')) {
                return;
            }

            $sourceField = static::translatableSlugSource();
            if ($sourceField === null) {
                return;
            }

            /** @var array<string, ?string> $slugs */
            $slugs = $model->getTranslations('slug');
            /** @var array<string, ?string> $sources */
            $sources = $model->getTranslations($sourceField);

            $changed = false;
            foreach (Locale::SUPPORTED as $locale) {
                $existing = $slugs[$locale] ?? null;
                if ($existing !== null && $existing !== '') {
                    continue;
                }

                $sourceValue = $sources[$locale] ?? null;
                if (! is_string($sourceValue) || $sourceValue === '') {
                    continue;
                }

                $slugs[$locale] = self::transliterateToSlug($sourceValue);
                $changed = true;
            }

            if ($changed) {
                $model->setTranslations('slug', $slugs);
            }
        });
    }

    /**
     * The translatable field whose value seeds slug auto-generation when a
     * locale's slug is left blank. Models override to point at `name` etc.
     */
    protected static function translatableSlugSource(): ?string
    {
        return 'title';
    }

    private static function transliterateToSlug(string $value): string
    {
        if (function_exists('transliterator_transliterate')) {
            $transliterated = transliterator_transliterate(
                'Any-Latin; Latin-ASCII; Lower()',
                $value,
            );
            if (is_string($transliterated) && $transliterated !== '') {
                return Str::slug($transliterated);
            }
        }

        return Str::slug($value);
    }
}

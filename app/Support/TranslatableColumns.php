<?php

declare(strict_types=1);

namespace App\Support;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migration helpers for converting plain string/text columns into per-locale
 * JSON shapes consumed by spatie/laravel-translatable.
 *
 * The existing scalar value is preserved by writing it under the configured
 * default locale key — `null` and empty strings are stored as `{}` so the
 * model's `getTranslation()` returns null/empty for every locale rather than
 * a fake default-locale value.
 */
final class TranslatableColumns
{
    /**
     * Convert a list of columns on `$table` from string/text to JSON, copying
     * each existing value into `{ "<default_locale>": "<old>" }`.
     *
     * `$schemaOverrides` lets the caller add new constraints (e.g. recreating
     * an index after the column type changes). The closure runs inside the
     * second `Schema::table` call, after the new JSON columns are in place.
     *
     * @param  array<int, string>  $fields
     * @param  array<int, string>  $textFields  Columns that were originally text/longText (no length)
     */
    public static function convertToJson(
        string $table,
        array $fields,
        array $textFields = [],
        ?Closure $schemaOverrides = null,
    ): void {
        $defaultLocale = Locale::default();

        // Snapshot existing values into memory before column dropping.
        $rows = DB::table($table)
            ->select(array_merge(['id'], $fields))
            ->get();

        // Drop indexes referencing these columns before the column itself —
        // SQLite's "alter table drop column" refuses while an index still
        // names the column, and MySQL's auto-drop is fine but redundant.
        Schema::table($table, function (Blueprint $blueprint) use ($table, $fields): void {
            foreach ($fields as $field) {
                if (Schema::hasIndex($table, "{$table}_{$field}_unique")) {
                    $blueprint->dropUnique([$field]);
                }
            }
        });

        Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
            foreach ($fields as $field) {
                $blueprint->dropColumn($field);
            }
        });

        Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
            foreach ($fields as $field) {
                $blueprint->json($field)->nullable();
            }
        });

        // Backfill: every previously-set value moves to the default locale key.
        foreach ($rows as $row) {
            $payload = [];
            foreach ($fields as $field) {
                $existing = $row->{$field} ?? null;
                $payload[$field] = $existing === null || $existing === ''
                    ? json_encode((object) [], JSON_UNESCAPED_UNICODE)
                    : json_encode([$defaultLocale => $existing], JSON_UNESCAPED_UNICODE);
            }

            DB::table($table)->where('id', $row->id)->update($payload);
        }

        if ($schemaOverrides !== null) {
            Schema::table($table, $schemaOverrides);
        }
    }

    /**
     * Reverse `convertToJson`: collapse each JSON column back to its
     * default-locale string. Falls back to the first available locale value
     * when the default is missing — losing data is worse than picking the
     * wrong locale on a roll-back.
     *
     * @param  array<int, string>  $fields
     */
    public static function revertToString(
        string $table,
        array $fields,
        ?Closure $schemaOverrides = null,
    ): void {
        $defaultLocale = Locale::default();

        $rows = DB::table($table)
            ->select(array_merge(['id'], $fields))
            ->get();

        Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
            foreach ($fields as $field) {
                $blueprint->dropColumn($field);
            }
        });

        Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
            foreach ($fields as $field) {
                $blueprint->string($field)->nullable();
            }
        });

        foreach ($rows as $row) {
            $payload = [];
            foreach ($fields as $field) {
                $decoded = json_decode((string) ($row->{$field} ?? ''), true) ?: [];
                $payload[$field] = $decoded[$defaultLocale] ?? (array_values($decoded)[0] ?? null);
            }

            DB::table($table)->where('id', $row->id)->update($payload);
        }

        if ($schemaOverrides !== null) {
            Schema::table($table, $schemaOverrides);
        }
    }

    /**
     * Add brand-new nullable JSON columns. Used by Task 3 to bring SEO
     * coverage to spec on models that were missing fields like `og_image_alt`
     * before becoming translatable.
     *
     * @param  array<int, string>  $fields
     */
    public static function addJson(string $table, array $fields): void
    {
        Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
            foreach ($fields as $field) {
                $blueprint->json($field)->nullable();
            }
        });
    }

    /**
     * @param  array<int, string>  $fields
     */
    public static function dropJson(string $table, array $fields): void
    {
        Schema::table($table, function (Blueprint $blueprint) use ($fields): void {
            foreach ($fields as $field) {
                $blueprint->dropColumn($field);
            }
        });
    }

    /**
     * MySQL-only per-locale unique indexes on a JSON slug column. SQLite (CI)
     * doesn't support functional indexes; we let the application layer enforce
     * uniqueness in tests there.
     */
    public static function addPerLocaleSlugUniques(string $table, string $column = 'slug'): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach (Locale::SUPPORTED as $locale) {
            $indexName = "{$table}_{$column}_{$locale}_unique";
            DB::statement(
                "CREATE UNIQUE INDEX {$indexName} ".
                "ON {$table} ((CAST({$column}->'$.{$locale}' AS CHAR(255))))"
            );
        }
    }

    public static function dropPerLocaleSlugUniques(string $table, string $column = 'slug'): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        foreach (Locale::SUPPORTED as $locale) {
            $indexName = "{$table}_{$column}_{$locale}_unique";
            DB::statement("DROP INDEX {$indexName} ON {$table}");
        }
    }
}

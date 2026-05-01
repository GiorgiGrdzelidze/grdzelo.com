<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;

/**
 * Adds a per-locale `jsonld` JSON column to every model that emits structured
 * data. Starts empty — admin can either save custom JSON-LD per locale or
 * fall through to the model's `defaultJsonLd()` programmatic generator.
 *
 * Models without a public detail page (Service, Skill) are intentionally
 * excluded: they don't render their own JSON-LD blocks today.
 */
return new class extends Migration
{
    /** @var array<int, string> */
    private const TABLES = ['projects', 'articles', 'repositories', 'albums', 'hobbies', 'pages'];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            TranslatableColumns::addJson($table, ['jsonld']);
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            TranslatableColumns::dropJson($table, ['jsonld']);
        }
    }
};

<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /** @var array<int, string> */
    private const FIELDS = ['title', 'summary', 'body', 'excerpt', 'slug'];

    public function up(): void
    {
        TranslatableColumns::convertToJson('pages', self::FIELDS);
        TranslatableColumns::addPerLocaleSlugUniques('pages');
    }

    public function down(): void
    {
        TranslatableColumns::dropPerLocaleSlugUniques('pages');
        TranslatableColumns::revertToString('pages', self::FIELDS, function (Blueprint $blueprint): void {
            $blueprint->unique('slug');
        });
    }
};

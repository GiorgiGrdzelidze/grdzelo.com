<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /** @var array<int, string> */
    private const FIELDS = ['title', 'excerpt', 'body', 'slug'];

    public function up(): void
    {
        TranslatableColumns::convertToJson('articles', self::FIELDS);
        TranslatableColumns::addPerLocaleSlugUniques('articles');
    }

    public function down(): void
    {
        TranslatableColumns::dropPerLocaleSlugUniques('articles');
        TranslatableColumns::revertToString('articles', self::FIELDS, function (Blueprint $blueprint): void {
            $blueprint->unique('slug');
        });
    }
};

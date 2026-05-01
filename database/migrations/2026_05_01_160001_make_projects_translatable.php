<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /** @var array<int, string> */
    private const FIELDS = ['title', 'summary', 'description', 'challenge', 'solution', 'process', 'slug'];

    public function up(): void
    {
        TranslatableColumns::convertToJson('projects', self::FIELDS);
        TranslatableColumns::addPerLocaleSlugUniques('projects');
    }

    public function down(): void
    {
        TranslatableColumns::dropPerLocaleSlugUniques('projects');
        TranslatableColumns::revertToString('projects', self::FIELDS, function (Blueprint $blueprint): void {
            $blueprint->unique('slug');
        });
    }
};

<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /** @var array<int, string> */
    private const FIELDS = ['title', 'summary', 'description', 'slug'];

    public function up(): void
    {
        TranslatableColumns::convertToJson('hobbies', self::FIELDS);
        TranslatableColumns::addPerLocaleSlugUniques('hobbies');
    }

    public function down(): void
    {
        TranslatableColumns::dropPerLocaleSlugUniques('hobbies');
        TranslatableColumns::revertToString('hobbies', self::FIELDS, function (Blueprint $blueprint): void {
            $blueprint->unique('slug');
        });
    }
};

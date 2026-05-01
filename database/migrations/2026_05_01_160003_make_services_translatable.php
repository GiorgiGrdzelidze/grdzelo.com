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
        TranslatableColumns::convertToJson('services', self::FIELDS);
        TranslatableColumns::addPerLocaleSlugUniques('services');
    }

    public function down(): void
    {
        TranslatableColumns::dropPerLocaleSlugUniques('services');
        TranslatableColumns::revertToString('services', self::FIELDS, function (Blueprint $blueprint): void {
            $blueprint->unique('slug');
        });
    }
};

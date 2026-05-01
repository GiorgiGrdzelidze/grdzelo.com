<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration
{
    /** @var array<int, string> */
    private const FIELDS = ['name', 'summary', 'description', 'slug'];

    public function up(): void
    {
        TranslatableColumns::convertToJson('repositories', self::FIELDS);
        TranslatableColumns::addPerLocaleSlugUniques('repositories');
    }

    public function down(): void
    {
        TranslatableColumns::dropPerLocaleSlugUniques('repositories');
        TranslatableColumns::revertToString('repositories', self::FIELDS, function (Blueprint $blueprint): void {
            $blueprint->unique('slug');
        });
    }
};

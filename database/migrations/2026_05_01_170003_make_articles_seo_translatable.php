<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** @var array<int, string> */
    private const EXISTING = ['meta_title', 'meta_description', 'canonical_url', 'robots', 'og_title', 'og_description', 'twitter_title', 'twitter_description'];

    /** @var array<int, string> */
    private const NEW = ['og_image_alt', 'twitter_image_alt'];

    public function up(): void
    {
        TranslatableColumns::convertToJson('articles', self::EXISTING);
        TranslatableColumns::addJson('articles', self::NEW);
    }

    public function down(): void
    {
        TranslatableColumns::dropJson('articles', self::NEW);
        TranslatableColumns::revertToString('articles', self::EXISTING);
    }
};

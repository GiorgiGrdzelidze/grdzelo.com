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
        TranslatableColumns::convertToJson('pages', self::EXISTING);
        TranslatableColumns::addJson('pages', self::NEW);
    }

    public function down(): void
    {
        TranslatableColumns::dropJson('pages', self::NEW);
        TranslatableColumns::revertToString('pages', self::EXISTING);
    }
};

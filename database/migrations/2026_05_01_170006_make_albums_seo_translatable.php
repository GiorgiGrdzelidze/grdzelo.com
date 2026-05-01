<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** @var array<int, string> */
    private const EXISTING = ['meta_title', 'meta_description'];

    /** @var array<int, string> */
    private const NEW = ['canonical_url', 'robots', 'og_title', 'og_description', 'og_image_alt', 'twitter_title', 'twitter_description', 'twitter_image_alt'];

    public function up(): void
    {
        TranslatableColumns::convertToJson('albums', self::EXISTING);
        TranslatableColumns::addJson('albums', self::NEW);
    }

    public function down(): void
    {
        TranslatableColumns::dropJson('albums', self::NEW);
        TranslatableColumns::revertToString('albums', self::EXISTING);
    }
};

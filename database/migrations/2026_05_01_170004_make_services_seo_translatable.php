<?php

declare(strict_types=1);

use App\Support\TranslatableColumns;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /** @var array<int, string> */
    private const EXISTING = ['meta_title', 'meta_description', 'canonical_url', 'og_title', 'og_description'];

    /** @var array<int, string> */
    private const NEW = ['robots', 'og_image_alt', 'twitter_title', 'twitter_description', 'twitter_image_alt'];

    public function up(): void
    {
        TranslatableColumns::convertToJson('services', self::EXISTING);
        TranslatableColumns::addJson('services', self::NEW);
    }

    public function down(): void
    {
        TranslatableColumns::dropJson('services', self::NEW);
        TranslatableColumns::revertToString('services', self::EXISTING);
    }
};

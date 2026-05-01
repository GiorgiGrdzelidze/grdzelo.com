<?php

namespace App\Models;

use App\Concerns\HasSeoFields;
use App\Concerns\HasTranslatableSlug;
use App\Support\Tiptap;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasSeoFields, HasTranslatableSlug, HasTranslations;

    /** @var array<int, string> */
    public array $translatable = [
        'title', 'summary', 'description', 'slug',
        'meta_title', 'meta_description', 'canonical_url', 'robots',
        'og_title', 'og_description', 'og_image_alt',
        'twitter_title', 'twitter_description', 'twitter_image_alt',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'schema_json' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function description(): Attribute
    {
        return Attribute::get(fn (?string $value) => Tiptap::toHtml($value))->shouldCache();
    }
}

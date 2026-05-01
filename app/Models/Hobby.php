<?php

namespace App\Models;

use App\Concerns\HasSeoFields;
use App\Concerns\HasTranslatableSlug;
use App\Support\Tiptap;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Hobby extends Model implements HasMedia
{
    use HasFactory, HasSeoFields, HasTranslatableSlug, HasTranslations, InteractsWithMedia;

    /** @var array<int, string> */
    public array $translatable = [
        'title', 'summary', 'description', 'slug',
        'meta_title', 'meta_description', 'canonical_url', 'robots',
        'og_title', 'og_description', 'og_image_alt',
        'twitter_title', 'twitter_description', 'twitter_image_alt',
        'jsonld',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'gallery' => 'array',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function description(): Attribute
    {
        return Attribute::get(fn (?string $value) => Tiptap::toHtml($value))->shouldCache();
    }

    public function defaultJsonLd(): ?array
    {
        if (! $this->title) {
            return null;
        }

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'Thing',
            'name' => (string) $this->title,
            'description' => $this->summary !== null ? strip_tags((string) $this->summary) : null,
            'inLanguage' => app()->getLocale(),
        ], fn ($v) => $v !== null && $v !== '');
    }
}

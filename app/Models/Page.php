<?php

namespace App\Models;

use App\Concerns\HasPublishState;
use App\Concerns\HasSeoFields;
use App\Concerns\HasTranslatableSlug;
use App\Support\Tiptap;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Page extends Model implements HasMedia
{
    use HasFactory, HasPublishState, HasSeoFields, HasTranslatableSlug, HasTranslations, InteractsWithMedia;

    /** @var array<int, string> */
    public array $translatable = [
        'title', 'summary', 'body', 'excerpt', 'slug',
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
            'schema_json' => 'array',
            'publish_at' => 'datetime',
            'nav_visible' => 'boolean',
            'noindex' => 'boolean',
            'nofollow' => 'boolean',
        ];
    }

    public function scopeNavigable($query)
    {
        return $query->where('nav_visible', true)->orderBy('sort_order');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function body(): Attribute
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
            '@type' => 'WebPage',
            'name' => (string) $this->title,
            'description' => $this->summary !== null ? strip_tags((string) $this->summary) : null,
            'inLanguage' => app()->getLocale(),
            'datePublished' => $this->publish_at?->toAtomString(),
            'dateModified' => $this->updated_at?->toAtomString(),
        ], fn ($v) => $v !== null && $v !== '');
    }
}

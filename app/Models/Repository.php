<?php

namespace App\Models;

use App\Concerns\HasSeoFields;
use App\Concerns\HasTranslatableSlug;
use App\Support\Tiptap;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Translatable\HasTranslations;

class Repository extends Model implements HasMedia
{
    use HasFactory, HasSeoFields, HasTranslatableSlug, HasTranslations, InteractsWithMedia;

    /** @var array<int, string> */
    public array $translatable = [
        'name', 'summary', 'description', 'slug',
        'meta_title', 'meta_description', 'canonical_url', 'robots',
        'og_title', 'og_description', 'og_image_alt',
        'twitter_title', 'twitter_description', 'twitter_image_alt',
        'jsonld',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'technologies' => 'array',
            'stars' => 'integer',
            'forks' => 'integer',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
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
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('thumbnail')->singleFile();
        $this->addMediaCollection('screenshots');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getSeoTitle(): string
    {
        return $this->meta_title ?? $this->name ?? '';
    }

    protected function description(): Attribute
    {
        return Attribute::get(fn (?string $value) => Tiptap::toHtml($value))->shouldCache();
    }

    protected static function translatableSlugSource(): ?string
    {
        return 'name';
    }

    public function defaultJsonLd(): ?array
    {
        if (! $this->name) {
            return null;
        }

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareSourceCode',
            'name' => (string) $this->name,
            'description' => $this->summary !== null ? strip_tags((string) $this->summary) : null,
            'inLanguage' => app()->getLocale(),
            'codeRepository' => $this->url ?: null,
            'programmingLanguage' => $this->language ?: null,
        ], fn ($v) => $v !== null && $v !== '');
    }
}

<?php

namespace App\Models;

use App\Concerns\HasPublishState;
use App\Concerns\HasSeoFields;
use App\Concerns\HasTranslatableSlug;
use App\Support\Tiptap;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class Article extends Model implements HasMedia
{
    use HasFactory, HasPublishState, HasSeoFields, HasTags, HasTranslatableSlug, HasTranslations, InteractsWithMedia;

    /** @var array<int, string> */
    public array $translatable = [
        'title', 'excerpt', 'body', 'slug',
        'meta_title', 'meta_description', 'canonical_url', 'robots',
        'og_title', 'og_description', 'og_image_alt',
        'twitter_title', 'twitter_description', 'twitter_image_alt',
        'jsonld',
    ];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'schema_json' => 'array',
            'publish_at' => 'datetime',
            'is_featured' => 'boolean',
            'noindex' => 'boolean',
            'nofollow' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover_image')->singleFile();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getReadingTimeAttribute($value): int
    {
        if ($value) {
            return $value;
        }

        $wordCount = str_word_count(strip_tags($this->body ?? ''));

        return max(1, (int) ceil($wordCount / 200));
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
            '@type' => 'BlogPosting',
            'headline' => (string) $this->title,
            'description' => $this->excerpt !== null ? strip_tags((string) $this->excerpt) : null,
            'inLanguage' => app()->getLocale(),
            'url' => $this->canonical_url ?: null,
            'datePublished' => $this->publish_at?->toAtomString(),
            'dateModified' => $this->updated_at?->toAtomString(),
            'author' => $this->author ? [
                '@type' => 'Person',
                'name' => $this->author->name,
            ] : null,
        ], fn ($v) => $v !== null && $v !== '');
    }
}

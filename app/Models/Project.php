<?php

namespace App\Models;

use App\Concerns\HasPublishState;
use App\Concerns\HasSeoFields;
use App\Concerns\HasTranslatableSlug;
use App\Support\Tiptap;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Tags\HasTags;
use Spatie\Translatable\HasTranslations;

class Project extends Model implements HasMedia
{
    use HasFactory, HasPublishState, HasSeoFields, HasTags, HasTranslatableSlug, HasTranslations, InteractsWithMedia;

    /** @var array<int, string> */
    public array $translatable = ['title', 'summary', 'description', 'challenge', 'solution', 'process', 'slug'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'tech_stack' => 'array',
            'gallery' => 'array',
            'case_study_blocks' => 'array',
            'metrics' => 'array',
            'schema_json' => 'array',
            'publish_at' => 'datetime',
            'date_start' => 'date',
            'date_end' => 'date',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
            'noindex' => 'boolean',
            'nofollow' => 'boolean',
        ];
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    public function testimonials(): BelongsToMany
    {
        return $this->belongsToMany(Testimonial::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('is_visible', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover_image')->singleFile();
        $this->addMediaCollection('logo')->singleFile();
        $this->addMediaCollection('gallery');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Long-form content fields are read either as raw HTML (legacy) or as TipTap JSON
    // documents (legacy, when admin used RichEditor). Render to HTML on read so the
    // public surface can drop the value straight into v-html.
    protected function description(): Attribute
    {
        return Attribute::get(fn (?string $value) => Tiptap::toHtml($value))->shouldCache();
    }

    protected function challenge(): Attribute
    {
        return Attribute::get(fn (?string $value) => Tiptap::toHtml($value))->shouldCache();
    }

    protected function solution(): Attribute
    {
        return Attribute::get(fn (?string $value) => Tiptap::toHtml($value))->shouldCache();
    }

    protected function process(): Attribute
    {
        return Attribute::get(fn (?string $value) => Tiptap::toHtml($value))->shouldCache();
    }
}

<?php

namespace App\Models;

use App\Concerns\HasPublishState;
use App\Concerns\HasSeoFields;
use App\Concerns\HasTranslatableSlug;
use App\Support\Tiptap;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Album extends Model
{
    use HasFactory, HasPublishState, HasSeoFields, HasTranslatableSlug, HasTranslations;

    /** @var array<int, string> */
    public array $translatable = ['title', 'summary', 'description', 'slug'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'photos' => 'array',
            'publish_at' => 'datetime',
            'taken_at' => 'date',
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
        return $query->orderBy('sort_order')->orderByDesc('taken_at');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getPhotoCountAttribute(): int
    {
        return count($this->photos ?? []);
    }

    protected function description(): Attribute
    {
        return Attribute::get(fn (?string $value) => Tiptap::toHtml($value))->shouldCache();
    }
}

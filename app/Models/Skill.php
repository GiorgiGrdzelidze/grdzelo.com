<?php

namespace App\Models;

use App\Concerns\HasTranslatableSlug;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Skill extends Model
{
    use HasFactory, HasTranslatableSlug, HasTranslations;

    /** @var array<int, string> */
    public array $translatable = ['name', 'slug'];

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    public function certifications(): BelongsToMany
    {
        return $this->belongsToMany(Certification::class);
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

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function translatableSlugSource(): ?string
    {
        return 'name';
    }
}

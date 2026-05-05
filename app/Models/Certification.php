<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Certification extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
            'no_expiry' => 'boolean',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
        ];
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
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
        return $query->orderBy('sort_order')->orderByDesc('issue_date');
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->where('no_expiry', true)
                ->orWhere('expiry_date', '>=', now());
        });
    }

    public function scopeExpired($query)
    {
        return $query->where('no_expiry', false)
            ->where('expiry_date', '<', now());
    }

    public function getIsExpiredAttribute(): bool
    {
        if ($this->no_expiry) {
            return false;
        }

        return $this->expiry_date && $this->expiry_date->isPast();
    }

    public function getStatusAttribute(): string
    {
        if ($this->no_expiry) {
            return 'No Expiry';
        }

        return $this->is_expired ? 'Expired' : 'Active';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('badge')->singleFile();
    }
}

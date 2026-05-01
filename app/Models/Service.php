<?php

namespace App\Models;

use App\Concerns\HasSeoFields;
use App\Support\Tiptap;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory, HasSeoFields;

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

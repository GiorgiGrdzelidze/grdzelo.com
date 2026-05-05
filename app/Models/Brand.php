<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Brand extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $guarded = ['id'];

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('about')->singleFile();
    }
}

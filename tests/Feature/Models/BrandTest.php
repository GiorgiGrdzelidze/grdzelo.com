<?php

declare(strict_types=1);

use App\Models\Brand;
use Illuminate\Support\Facades\Storage;

it('Brand::current returns the same singleton row on repeated calls', function (): void {
    $first = Brand::current();
    $second = Brand::current();

    expect($first->id)->toBe($second->id);
    expect(Brand::count())->toBe(1);
});

it('Brand::current self-heals when the row is missing', function (): void {
    Brand::query()->delete();
    expect(Brand::count())->toBe(0);

    $brand = Brand::current();

    expect($brand->id)->toBe(1);
    expect(Brand::count())->toBe(1);
});

it('registers the about media collection as singleFile', function (): void {
    $brand = Brand::current();
    $brand->registerMediaCollections();

    $registered = $brand->getRegisteredMediaCollections()->keyBy('name');

    expect($registered)->toHaveKey('about');
    expect($registered->get('about')->singleFile)->toBeTrue();
});

it('Brand::current can host an upload on the about collection', function (): void {
    Storage::fake('public');

    $brand = Brand::current();
    $brand->addMediaFromString('binary')->usingFileName('portrait.jpg')->toMediaCollection('about');

    $brand->refresh();

    expect($brand->getMedia('about'))->toHaveCount(1);
    expect($brand->getFirstMediaUrl('about'))->not->toBeEmpty();
});

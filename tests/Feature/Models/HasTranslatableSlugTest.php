<?php

declare(strict_types=1);

use App\Models\Hobby;
use App\Models\Repository;

it('auto-generates a slug for the default locale from the title on save when blank', function () {
    $hobby = Hobby::create([
        'title' => 'Photography Adventures',
        'is_visible' => true,
    ]);

    expect($hobby->getTranslation('slug', 'en', false))->toBe('photography-adventures');
});

it('auto-generates per-locale slugs from per-locale titles, transliterating to ASCII', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', [
        'en' => 'Photography',
        'ka' => 'ფოტოგრაფია',
        'ru' => 'Фотография',
    ]);
    $hobby->is_visible = true;
    $hobby->save();

    $slugs = $hobby->getTranslations('slug');

    expect($slugs['en'])->toBe('photography');
    expect($slugs['ka'])->toBe('potograpia');
    expect($slugs['ru'])->toBe('fotografia');
});

it('does not overwrite an explicitly-set slug for a given locale', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography']);
    $hobby->setTranslations('slug', ['en' => 'custom-photo-slug']);
    $hobby->is_visible = true;
    $hobby->save();

    expect($hobby->getTranslation('slug', 'en', false))->toBe('custom-photo-slug');
});

it('uses the model-defined slug source field (Repository → name)', function () {
    $repo = Repository::create([
        'name' => 'Inertia Toolkit',
        'url' => 'https://github.com/example/toolkit',
        'is_visible' => true,
    ]);

    expect($repo->getTranslation('slug', 'en', false))->toBe('inertia-toolkit');
});

it('marks wasResolvedByFallback when active-locale slug is missing but default-locale slug matches', function () {
    $hobby = Hobby::create([
        'title' => 'Photography',
        'is_visible' => true,
    ]);

    app()->setLocale('ka');

    $resolved = (new Hobby)->resolveRouteBinding('photography');

    expect($resolved)->not->toBeNull();
    expect($resolved->id)->toBe($hobby->id);
    expect($resolved->wasResolvedByFallback())->toBeTrue();
});

it('does not mark fallback when active-locale slug matches directly', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography', 'ka' => 'ფოტოგრაფია']);
    $hobby->setTranslations('slug', ['en' => 'photography', 'ka' => 'potograpia']);
    $hobby->is_visible = true;
    $hobby->save();

    app()->setLocale('ka');

    $resolved = (new Hobby)->resolveRouteBinding('potograpia');

    expect($resolved)->not->toBeNull();
    expect($resolved->wasResolvedByFallback())->toBeFalse();
});

it('returns null when no slug matches in the active locale or the default locale', function () {
    Hobby::create(['title' => 'Photography', 'is_visible' => true]);

    $resolved = (new Hobby)->resolveRouteBinding('does-not-exist');

    expect($resolved)->toBeNull();
});

it('resolves via Filament-style resolveRouteBindingQuery using active-locale slug', function () {
    Hobby::create(['title' => 'Photography', 'is_visible' => true]);

    $found = (new Hobby)
        ->resolveRouteBindingQuery(Hobby::query(), 'photography')
        ->first();

    expect($found)->not->toBeNull();
    expect($found->getRouteKey())->toBe('photography');
});

it('resolves via Filament-style resolveRouteBindingQuery using default-locale fallback when active is non-default', function () {
    Hobby::create(['title' => 'Photography', 'is_visible' => true]);

    app()->setLocale('ka');

    $found = (new Hobby)
        ->resolveRouteBindingQuery(Hobby::query(), 'photography')
        ->first();

    expect($found)->not->toBeNull();
});

it('serializes translatable attributes to the active-locale string on toArray()', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography', 'ka' => 'ფოტოგრაფია']);
    $hobby->setTranslations('slug', ['en' => 'photography', 'ka' => 'potograpia']);
    $hobby->is_visible = true;
    $hobby->save();

    $arr = $hobby->fresh()->toArray();
    expect($arr['title'])->toBe('Photography');
    expect($arr['slug'])->toBe('photography');

    app()->setLocale('ka');
    $arr = $hobby->fresh()->toArray();
    expect($arr['title'])->toBe('ფოტოგრაფია');
    expect($arr['slug'])->toBe('potograpia');
});

it('falls back to default-locale value when toArray serializes a missing locale', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography']);
    $hobby->is_visible = true;
    $hobby->save();

    app()->setLocale('ru');
    $arr = $hobby->fresh()->toArray();
    expect($arr['title'])->toBe('Photography');
});

<?php

declare(strict_types=1);

use App\Filament\Concerns\TranslatableMediaAlt;
use App\Models\Project;
use App\Support\Locale;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('public');
});

it('round-trips a per-locale alt JSON shape on media custom_properties', function (): void {
    $project = Project::create([
        'title' => ['en' => 'Alt Round Trip'],
        'slug' => ['en' => 'alt-round-trip'],
    ]);

    $project->addMediaFromString('cover')->usingFileName('c.jpg')->toMediaCollection('cover');

    $media = $project->fresh()->getFirstMedia('cover');

    $media->setCustomProperty('alt', [
        'en' => 'English alt',
        'ka' => 'ქართული ალტი',
        'ru' => 'Русский альт',
    ]);
    $media->save();

    $stored = $project->fresh()->getFirstMedia('cover')->getCustomProperty('alt');

    expect($stored)->toBe([
        'en' => 'English alt',
        'ka' => 'ქართული ალტი',
        'ru' => 'Русский альт',
    ]);
});

it('resolves the active-locale alt when present', function (): void {
    app()->setLocale('ka');

    $alt = TranslatableMediaAlt::resolveAlt([
        'en' => 'English',
        'ka' => 'Georgian',
        'ru' => 'Russian',
    ]);

    expect($alt)->toBe('Georgian');
});

it('falls back to the default-locale alt when the active-locale value is blank', function (): void {
    app()->setLocale('ka');

    $alt = TranslatableMediaAlt::resolveAlt([
        'en' => 'English fallback',
        'ka' => '',
    ]);

    expect($alt)->toBe('English fallback');
});

it('returns null when both active-locale and default-locale alts are blank', function (): void {
    app()->setLocale('ru');

    expect(TranslatableMediaAlt::resolveAlt(['en' => '', 'ru' => '']))->toBeNull();
    expect(TranslatableMediaAlt::resolveAlt([]))->toBeNull();
    expect(TranslatableMediaAlt::resolveAlt(null))->toBeNull();
});

it('treats a plain string alt as a single-locale fallback (legacy media custom_properties shape)', function (): void {
    expect(TranslatableMediaAlt::resolveAlt('Plain alt'))->toBe('Plain alt');
});

it('mediaAlt() helper resolves the alt from a Media instance', function (): void {
    app()->setLocale(Locale::default());

    $project = Project::create([
        'title' => ['en' => 'Helper Test'],
        'slug' => ['en' => 'helper-test'],
    ]);

    $project->addMediaFromString('h')->usingFileName('h.jpg')->toMediaCollection('cover');

    $media = $project->fresh()->getFirstMedia('cover');
    $media->setCustomProperty('alt', ['en' => 'A photo of something', 'ka' => 'ფოტო']);
    $media->save();

    expect(TranslatableMediaAlt::mediaAlt($media))->toBe('A photo of something');

    app()->setLocale('ka');
    expect(TranslatableMediaAlt::mediaAlt($project->fresh()->getFirstMedia('cover')))->toBe('ფოტო');
});

it('mediaAlt() returns null when given null', function (): void {
    expect(TranslatableMediaAlt::mediaAlt(null))->toBeNull();
});

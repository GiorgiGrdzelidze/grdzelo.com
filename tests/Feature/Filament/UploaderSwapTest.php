<?php

declare(strict_types=1);

use App\Filament\Resources\AlbumResource;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\CertificationResource;
use App\Filament\Resources\HobbyResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\RepositoryResource;
use App\Models\Album;
use App\Models\Article;
use App\Models\Certification;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\Repository;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\HasMedia;

/**
 * Invariant: every Filament resource managing a public-image field uses
 * SpatieMediaLibraryFileUpload (NOT plain FileUpload) bound to the
 * canonical media collection. Source-file regex is decisive — a
 * misconfigured uploader silently regresses to legacy string-column
 * writes that the public read path will not see post-Task-3.
 */
dataset('public_image_uploaders', [
    'Project::cover' => [ProjectResource::class, 'cover'],
    'Project::logo' => [ProjectResource::class, 'logo'],
    'Project::gallery' => [ProjectResource::class, 'gallery'],
    'Article::cover' => [ArticleResource::class, 'cover'],
    'Hobby::cover' => [HobbyResource::class, 'cover'],
    'Hobby::gallery' => [HobbyResource::class, 'gallery'],
    'Album::cover' => [AlbumResource::class, 'cover'],
    'Album::photos' => [AlbumResource::class, 'photos'],
    'Certification::badge' => [CertificationResource::class, 'badge'],
    'Repository::cover' => [RepositoryResource::class, 'cover'],
    'Repository::screenshots' => [RepositoryResource::class, 'screenshots'],
]);

it('binds SpatieMediaLibraryFileUpload to the canonical collection', function (string $resourceClass, string $collection): void {
    $source = file_get_contents((new ReflectionClass($resourceClass))->getFileName());

    $pattern = '/SpatieMediaLibraryFileUpload::make\(\s*[\'"]'.preg_quote($collection, '/').'[\'"]\s*\)\s*->collection\(\s*[\'"]'.preg_quote($collection, '/').'[\'"]\s*\)/s';

    expect(preg_match($pattern, $source))->toBe(
        1,
        "{$resourceClass} must call SpatieMediaLibraryFileUpload::make('{$collection}')->collection('{$collection}')."
    );
})->with('public_image_uploaders');

it('removed the legacy column-bound FileUpload fields from each resource', function (): void {
    $legacyByResource = [
        ProjectResource::class => ['cover_image', 'gallery'],
        ArticleResource::class => ['cover_image'],
        HobbyResource::class => ['image', 'gallery'],
        AlbumResource::class => ['cover_image', 'photos'],
        CertificationResource::class => ['badge_image'],
        RepositoryResource::class => ['thumbnail'],
    ];

    foreach ($legacyByResource as $resourceClass => $legacyFields) {
        $source = file_get_contents((new ReflectionClass($resourceClass))->getFileName());

        foreach ($legacyFields as $field) {
            $pattern = '/Forms\\\\Components\\\\FileUpload::make\(\s*[\'"]'.preg_quote($field, '/').'[\'"]\s*\)/';

            expect(preg_match($pattern, $source))->toBe(
                0,
                "{$resourceClass} still wires plain FileUpload::make('{$field}') — should be SpatieMediaLibraryFileUpload."
            );
        }
    }
});

it('registers the canonical media collections on every in-scope model', function (): void {
    $expected = [
        Project::class => ['cover', 'logo', 'gallery'],
        Article::class => ['cover'],
        Hobby::class => ['cover', 'gallery'],
        Album::class => ['cover', 'photos'],
        Certification::class => ['badge'],
        Repository::class => ['cover', 'screenshots'],
    ];

    foreach ($expected as $modelClass => $collections) {
        $instance = new $modelClass;
        $registered = array_keys($instance->getRegisteredMediaCollections()->keyBy('name')->all());

        expect($registered)->toEqualCanonicalizing(
            $collections,
            "{$modelClass} should register collections: ".implode(', ', $collections)
        );
    }
});

it('Album and Certification gain HasMedia from scratch', function (): void {
    expect(new Album)->toBeInstanceOf(HasMedia::class);
    expect(new Certification)->toBeInstanceOf(HasMedia::class);
});

it('flags single-image collections with singleFile()', function (): void {
    $singleFileCollections = [
        Project::class => ['cover', 'logo'],
        Article::class => ['cover'],
        Hobby::class => ['cover'],
        Album::class => ['cover'],
        Certification::class => ['badge'],
        Repository::class => ['cover'],
    ];

    foreach ($singleFileCollections as $modelClass => $collections) {
        $registered = (new $modelClass)->getRegisteredMediaCollections()->keyBy('name');

        foreach ($collections as $name) {
            expect($registered->get($name)->singleFile)->toBeTrue(
                "{$modelClass} collection '{$name}' should be singleFile()."
            );
        }
    }
});

it('Project cover collection accepts an upload and round-trips', function (): void {
    Storage::fake('public');

    $project = Project::create([
        'title' => ['en' => 'Cover Test'],
        'slug' => ['en' => 'cover-test'],
    ]);

    $project->addMediaFromString('binary')
        ->usingFileName('cover.jpg')
        ->toMediaCollection('cover');

    $project->refresh();

    expect($project->getMedia('cover'))->toHaveCount(1);
    expect($project->getFirstMediaUrl('cover'))->not->toBeEmpty();
});

it('Project gallery collection accepts multiple uploads', function (): void {
    Storage::fake('public');

    $project = Project::create([
        'title' => ['en' => 'Gallery'],
        'slug' => ['en' => 'gallery-multi'],
    ]);

    $project->addMediaFromString('a')->usingFileName('a.jpg')->toMediaCollection('gallery');
    $project->addMediaFromString('b')->usingFileName('b.jpg')->toMediaCollection('gallery');

    $project->refresh();

    expect($project->getMedia('gallery'))->toHaveCount(2);
});

it('Project cover singleFile collection replaces on second upload', function (): void {
    Storage::fake('public');

    $project = Project::create([
        'title' => ['en' => 'Single File Test'],
        'slug' => ['en' => 'singlefile-test'],
    ]);

    $project->addMediaFromString('one')->usingFileName('one.jpg')->toMediaCollection('cover');
    $firstId = $project->fresh()->getFirstMedia('cover')->id;

    $project->addMediaFromString('two')->usingFileName('two.jpg')->toMediaCollection('cover');

    $project->refresh();

    expect($project->getMedia('cover'))->toHaveCount(1);
    expect($project->getFirstMedia('cover')->id)->not->toBe($firstId);
});

it('Album photos collection accepts multiple uploads (gain-from-scratch)', function (): void {
    Storage::fake('public');

    $album = Album::create([
        'title' => ['en' => 'Trip'],
        'slug' => ['en' => 'trip-album'],
    ]);

    $album->addMediaFromString('a')->usingFileName('a.jpg')->toMediaCollection('photos');
    $album->addMediaFromString('b')->usingFileName('b.jpg')->toMediaCollection('photos');
    $album->addMediaFromString('c')->usingFileName('c.jpg')->toMediaCollection('photos');

    $album->refresh();

    expect($album->getMedia('photos'))->toHaveCount(3);
});

it('Certification badge singleFile collection accepts an upload (gain-from-scratch)', function (): void {
    Storage::fake('public');

    $cert = Certification::create([
        'title' => 'Test Cert',
        'issuing_organization' => 'Acme',
        'issue_date' => '2025-01-01',
        'no_expiry' => true,
    ]);

    $cert->addMediaFromString('b')->usingFileName('badge.png')->toMediaCollection('badge');

    $cert->refresh();

    expect($cert->getMedia('badge'))->toHaveCount(1);
    expect($cert->getFirstMediaUrl('badge'))->not->toBeEmpty();
});

it('Repository cover and screenshots collections coexist', function (): void {
    Storage::fake('public');

    $repo = Repository::create([
        'name' => ['en' => 'Test Repo'],
        'slug' => ['en' => 'test-repo'],
        'url' => 'https://github.com/test/repo',
    ]);

    $repo->addMediaFromString('cov')->usingFileName('cover.jpg')->toMediaCollection('cover');
    $repo->addMediaFromString('s1')->usingFileName('s1.jpg')->toMediaCollection('screenshots');
    $repo->addMediaFromString('s2')->usingFileName('s2.jpg')->toMediaCollection('screenshots');

    $repo->refresh();

    expect($repo->getMedia('cover'))->toHaveCount(1);
    expect($repo->getMedia('screenshots'))->toHaveCount(2);
});

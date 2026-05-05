<?php

declare(strict_types=1);

use App\Models\Album;
use App\Models\Article;
use App\Models\Brand;
use App\Models\Certification;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\Repository;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\get;

beforeEach(function (): void {
    Storage::fake('public');
});

it('emits a media-library URL on the cover prop when a Project has media', function (): void {
    $project = Project::create([
        'title' => ['en' => 'Has Cover'],
        'slug' => ['en' => 'has-cover'],
        'status' => 'published',
        'is_visible' => true,
    ]);

    $project->addMediaFromString('cov')->usingFileName('cov.jpg')->toMediaCollection('cover');

    $response = get('/en/projects/has-cover');

    $response->assertOk();

    $project->refresh();
    $expected = $project->getFirstMediaUrl('cover');

    expect($expected)->not->toBeEmpty();
    $response->assertInertia(fn ($page) => $page
        ->where('project.cover', $expected)
        ->whereNot('project.cover', null)
    );
});

it('emits a null cover prop when a Project has no media', function (): void {
    Project::create([
        'title' => ['en' => 'No Cover'],
        'slug' => ['en' => 'no-cover'],
        'status' => 'published',
        'is_visible' => true,
    ]);

    $response = get('/en/projects/no-cover');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('project.cover', null));
});

it('emits a gallery prop with url+alt entries from media-library', function (): void {
    $project = Project::create([
        'title' => ['en' => 'Gallery Read Test'],
        'slug' => ['en' => 'gallery-read-test'],
        'status' => 'published',
        'is_visible' => true,
    ]);

    $project->addMediaFromString('a')->usingFileName('a.jpg')->toMediaCollection('gallery');
    $project->addMediaFromString('b')->usingFileName('b.jpg')->toMediaCollection('gallery');

    $response = get('/en/projects/gallery-read-test');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page
        ->has('project.gallery', 2)
        ->has('project.gallery.0', fn ($entry) => $entry->has('url')->has('alt'))
    );
});

it('emits cover from media on Article show', function (): void {
    $article = Article::create([
        'title' => ['en' => 'Article Cover'],
        'slug' => ['en' => 'article-cover'],
        'status' => 'published',
        'publish_at' => now()->subDay(),
    ]);

    $article->addMediaFromString('c')->usingFileName('c.jpg')->toMediaCollection('cover');

    $response = get('/en/blog/article-cover');

    $response->assertOk();
    $article->refresh();
    $expected = $article->getFirstMediaUrl('cover');

    $response->assertInertia(fn ($page) => $page->where('article.cover', $expected));
});

it('emits cover and gallery from media on Hobby show (legacy column is `image`)', function (): void {
    $hobby = Hobby::create([
        'title' => ['en' => 'Photography'],
        'slug' => ['en' => 'photography'],
        'is_visible' => true,
    ]);

    $hobby->addMediaFromString('h')->usingFileName('h.jpg')->toMediaCollection('cover');
    $hobby->addMediaFromString('g1')->usingFileName('g1.jpg')->toMediaCollection('gallery');

    $response = get('/en/hobbies/photography');

    $response->assertOk();
    $hobby->refresh();

    $response->assertInertia(fn ($page) => $page
        ->where('hobby.cover', $hobby->getFirstMediaUrl('cover'))
        ->has('hobby.gallery', 1)
        ->has('hobby.gallery.0.url')
        ->has('hobby.gallery.0.alt')
    );
});

it('emits cover and photos from media on Album show', function (): void {
    $album = Album::create([
        'title' => ['en' => 'Trip'],
        'slug' => ['en' => 'trip'],
        'status' => 'published',
        'is_visible' => true,
    ]);

    $album->addMediaFromString('cov')->usingFileName('cov.jpg')->toMediaCollection('cover');
    $album->addMediaFromString('p1')->usingFileName('p1.jpg')->toMediaCollection('photos');
    $album->addMediaFromString('p2')->usingFileName('p2.jpg')->toMediaCollection('photos');

    $response = get('/en/gallery/trip');

    $response->assertOk();
    $album->refresh();

    $response->assertInertia(fn ($page) => $page
        ->where('album.cover', $album->getFirstMediaUrl('cover'))
        ->has('photos', 2)
        ->has('photos.0', fn ($entry) => $entry
            ->has('id')
            ->has('url')
            ->has('thumb')
            ->has('preview')
            ->has('alt')
            ->has('caption')
        )
    );
});

it('emits badge from media on Certifications index', function (): void {
    $cert = Certification::create([
        'title' => 'Test Cert',
        'issuing_organization' => 'Acme',
        'issue_date' => '2025-01-01',
        'no_expiry' => true,
        'is_visible' => true,
    ]);

    $cert->addMediaFromString('b')->usingFileName('b.png')->toMediaCollection('badge');

    $response = get('/en/certifications');

    $response->assertOk();
    $cert->refresh();
    $expected = $cert->getFirstMediaUrl('badge');

    $response->assertInertia(fn ($page) => $page
        ->where('certifications.0.badge', $expected)
    );
});

it('emits cover and screenshots from media on Repository show', function (): void {
    $repo = Repository::create([
        'name' => ['en' => 'Test Repo'],
        'slug' => ['en' => 'test-repo'],
        'url' => 'https://github.com/test/repo',
        'is_visible' => true,
    ]);

    $repo->addMediaFromString('c')->usingFileName('c.jpg')->toMediaCollection('cover');
    $repo->addMediaFromString('s1')->usingFileName('s1.jpg')->toMediaCollection('screenshots');

    $response = get('/en/repositories/test-repo');

    $response->assertOk();
    $repo->refresh();

    $response->assertInertia(fn ($page) => $page
        ->where('repository.cover', $repo->getFirstMediaUrl('cover'))
        ->has('repository.screenshots', 1)
        ->has('repository.screenshots.0', fn ($entry) => $entry->has('url')->has('alt'))
    );
});

it('emits the about portrait under brand.portrait shared prop, sourced from Brand::current()', function (): void {
    Brand::current()->addMediaFromString('p')->usingFileName('portrait.jpg')->toMediaCollection('about');

    $response = get('/en/about');

    $response->assertOk();
    $expected = Brand::current()->fresh()->getFirstMediaUrl('about');

    $response->assertInertia(fn ($page) => $page->where('brand.portrait', $expected));
});

it('emits brand.portrait as null when Brand has no about media', function (): void {
    $response = get('/en/about');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->where('brand.portrait', null));
});

it('drops the legacy about_image key from shared settings', function (): void {
    $response = get('/en/about');

    $response->assertOk();
    $response->assertInertia(fn ($page) => $page->missing('settings.about_image'));
});

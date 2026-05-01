<?php

declare(strict_types=1);

use App\Models\Project;
use App\Settings\SeoSettings;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->save();
});

it('emits canonical pointing at the unprefixed path for /about', function () {
    $this->get('/about')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.canonical', 'https://grdzelo.test/about')
        );
});

it('emits canonical pointing at the /ka path for /ka/about', function () {
    $this->get('/ka/about')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.canonical', 'https://grdzelo.test/ka/about')
        );
});

it('emits canonical pointing at root for /', function () {
    $this->get('/')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.canonical', 'https://grdzelo.test/')
        );
});

it('emits self-canonical for /projects/{slug} (no admin override)', function () {
    Project::create([
        'title' => 'Demo',
        'slug' => 'demo-canonical',
        'summary' => 's',
        'is_visible' => true,
        'status' => 'published',
    ]);

    $this->get('/projects/demo-canonical')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.canonical', 'https://grdzelo.test/projects/demo-canonical')
        );
});

it('emits localized self-canonical for /ka/projects/{slug} even when admin set canonical_url to en URL', function () {
    Project::create([
        'title' => 'Demo',
        'slug' => 'demo-locale-canonical',
        'summary' => 's',
        'is_visible' => true,
        'status' => 'published',
        'canonical_url' => 'https://grdzelo.test/projects/demo-locale-canonical',
    ]);

    $this->get('/ka/projects/demo-locale-canonical')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.canonical', 'https://grdzelo.test/ka/projects/demo-locale-canonical')
        );
});

it('preserves an external admin canonical_url across locales', function () {
    Project::create([
        'title' => 'Demo',
        'slug' => 'demo-external-canonical',
        'summary' => 's',
        'is_visible' => true,
        'status' => 'published',
        'canonical_url' => 'https://medium.com/@author/demo-external',
    ]);

    $this->get('/projects/demo-external-canonical')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.canonical', 'https://medium.com/@author/demo-external')
        );

    $this->get('/ka/projects/demo-external-canonical')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.canonical', 'https://medium.com/@author/demo-external')
        );
});

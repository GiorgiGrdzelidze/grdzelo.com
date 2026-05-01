<?php

declare(strict_types=1);

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
            ->where('seo.canonical', 'https://grdzelo.test')
        );
});

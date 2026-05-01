<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Inertia\Testing\AssertableInertia;

it('dispatches the home page under /ka with locale set to ka', function () {
    $this->get('/ka')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Home')
            ->where('locale', 'ka')
        );

    expect(App::getLocale())->toBe('ka');
});

it('dispatches /ka/about with locale set to ka', function () {
    $this->get('/ka/about')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/About')
            ->where('locale', 'ka')
        );
});

it('dispatches /ru/contact with locale set to ru', function () {
    $this->get('/ru/contact')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Contact')
            ->where('locale', 'ru')
        );
});

it('returns 404 for an unsupported locale prefix', function () {
    $this->get('/de/about')->assertNotFound();
    $this->get('/fr')->assertNotFound();
});

it('keeps the unprefixed root at the default locale (en)', function () {
    $this->get('/about')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/About')
            ->where('locale', 'en')
        );
});

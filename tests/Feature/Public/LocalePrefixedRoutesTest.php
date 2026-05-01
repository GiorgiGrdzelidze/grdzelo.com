<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use Inertia\Testing\AssertableInertia;

it('dispatches the home page under /en with locale set to en', function () {
    $this->get('/en')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Home')
            ->where('locale', 'en')
        );

    expect(App::getLocale())->toBe('en');
});

it('dispatches the home page under /ka with locale set to ka', function () {
    $this->get('/ka')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Home')
            ->where('locale', 'ka')
        );

    expect(App::getLocale())->toBe('ka');
});

it('dispatches /en/about with locale set to en', function () {
    $this->get('/en/about')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/About')
            ->where('locale', 'en')
        );
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

it('returns 404 for /ka/locale/en — locale switch is not nested under prefix', function () {
    $this->get('/ka/locale/en')->assertNotFound();
});

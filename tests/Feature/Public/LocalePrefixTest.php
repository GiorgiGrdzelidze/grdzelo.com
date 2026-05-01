<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia;

it('redirects / to /{default_locale} with 302', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
    $response->assertRedirect('/en');
});

it('301-redirects an unprefixed inner path to its default-locale form', function () {
    $response = $this->get('/about');

    $response->assertStatus(301);
    $response->assertRedirect('/en/about');
});

it('301-redirect preserves the query string when prepending the locale', function () {
    $response = $this->get('/blog?category=laravel');

    $response->assertStatus(301);
    $response->assertRedirect('/en/blog?category=laravel');
});

it('serves /en/about with locale set to en and html lang attribute', function () {
    $this->get('/en/about')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/About')
            ->where('locale', 'en')
        )
        ->assertSee('lang="en"', false);
});

it('serves /ka/about with locale set to ka and html lang attribute', function () {
    $this->get('/ka/about')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/About')
            ->where('locale', 'ka')
        )
        ->assertSee('lang="ka"', false);
});

it('serves /ru with locale set to ru', function () {
    $this->get('/ru')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Home')
            ->where('locale', 'ru')
        );
});

it('returns 404 for an unsupported 2-letter locale prefix without redirecting', function () {
    $this->get('/zz/about')->assertNotFound();
    $this->get('/de')->assertNotFound();
    $this->get('/fr')->assertNotFound();
});

it('does not redirect /admin', function () {
    // Filament owns /admin; the fallback handler must not intercept it.
    // Filament will 302 unauthenticated visitors to its own login page —
    // the assertion here is "does not 301 redirect to /en/admin".
    $response = $this->get('/admin');

    expect($response->headers->get('Location'))->not->toBe(url('/en/admin'));
});

it('does not redirect /sitemap.xml', function () {
    $this->get('/sitemap.xml')->assertOk();
});

it('does not redirect /robots.txt', function () {
    $this->get('/robots.txt')->assertOk();
});

it('does not redirect a missing static asset (would 404 cleanly)', function () {
    $this->get('/missing.css')->assertNotFound();
    $this->get('/missing.png')->assertNotFound();
    $this->get('/missing.js')->assertNotFound();
});

it('does not redirect /locale/{locale} switch endpoint', function () {
    $this->get('/locale/ka')->assertRedirect('/ka');
});

it('returns 404 for /en/this-doesnt-exist (no implicit fallback inside the prefix)', function () {
    $this->get('/en/this-doesnt-exist')->assertNotFound();
});

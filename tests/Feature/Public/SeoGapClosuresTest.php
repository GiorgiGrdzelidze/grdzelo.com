<?php

declare(strict_types=1);

use App\Settings\SeoSettings;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->sitemap_enabled = true;
    $seo->indexing_enabled = true;
    $seo->save();
});

it('emits og:locale matching the active locale', function () {
    $this->get('/en/about')
        ->assertSee('<meta property="og:locale" content="en_US">', false);

    $this->get('/ka/about')
        ->assertSee('<meta property="og:locale" content="ka_GE">', false);

    $this->get('/ru/about')
        ->assertSee('<meta property="og:locale" content="ru_RU">', false);
});

it('emits og:locale:alternate for every supported non-active locale', function () {
    $response = $this->get('/en/about');

    $response->assertSee('<meta property="og:locale:alternate" content="ka_GE">', false);
    $response->assertSee('<meta property="og:locale:alternate" content="ru_RU">', false);
    $response->assertDontSee('<meta property="og:locale:alternate" content="en_US">', false);
});

it('robots.txt explicitly allows each supported locale path', function () {
    $body = $this->get('/robots.txt')->assertOk()->getContent();

    expect($body)->toContain('Allow: /en/');
    expect($body)->toContain('Allow: /ka/');
    expect($body)->toContain('Allow: /ru/');
    expect($body)->toContain('Disallow: /admin');
    expect($body)->toContain('Disallow: /livewire');
    expect($body)->toContain('Sitemap: https://grdzelo.test/sitemap.xml');
});

it('includes Accept-Language in the Vary header on the root redirect', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
    expect(implode(',', $response->headers->all('Vary')))->toContain('Accept-Language');
});

it('includes Accept-Language in the Vary header on the unprefixed-path 301 redirect', function () {
    $response = $this->get('/about');

    $response->assertStatus(301);
    expect(implode(',', $response->headers->all('Vary')))->toContain('Accept-Language');
});

it('includes Accept-Language in the Vary header on a localized public page', function () {
    $response = $this->get('/en/about');

    $response->assertOk();
    expect(implode(',', $response->headers->all('Vary')))->toContain('Accept-Language');
});

it('renders the localized 404 page under each locale prefix', function () {
    foreach (['en', 'ka', 'ru'] as $locale) {
        $this->get("/{$locale}/this-page-does-not-exist")
            ->assertStatus(404);
    }
});

it('emits a self-referential hreflang on every page', function () {
    foreach (['en', 'ka', 'ru'] as $locale) {
        $response = $this->get("/{$locale}/about");
        $response->assertSee('"hreflang":"'.$locale.'"', false);
    }
});

it('locale switch cookie is SameSite=Lax', function () {
    $response = $this->get('/locale/ka?return=/en/about');

    // Laravel queues cookies on the response; pull the locale cookie back.
    $cookie = collect($response->headers->getCookies())->firstWhere(fn ($c) => $c->getName() === 'locale');

    expect($cookie)->not->toBeNull();
    expect($cookie->getSameSite())->toBe('lax');
});

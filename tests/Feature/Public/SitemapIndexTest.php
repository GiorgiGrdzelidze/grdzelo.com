<?php

declare(strict_types=1);

use App\Settings\SeoSettings;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->sitemap_enabled = true;
    $seo->save();
});

it('returns a sitemapindex with one entry per supported locale at /sitemap.xml', function () {
    $response = $this->get('/sitemap.xml')->assertOk();

    $response->assertHeader('Content-Type', 'application/xml');
    $response->assertSee('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
    $response->assertSee('<loc>https://grdzelo.test/sitemap-en.xml</loc>', false);
    $response->assertSee('<loc>https://grdzelo.test/sitemap-ka.xml</loc>', false);
    $response->assertSee('<loc>https://grdzelo.test/sitemap-ru.xml</loc>', false);
});

it('the sitemap index parses as valid XML and has three child <sitemap> entries', function () {
    $body = $this->get('/sitemap.xml')->assertOk()->getContent();

    $xml = simplexml_load_string($body);
    expect($xml)->not->toBeFalse();
    expect($xml->getName())->toBe('sitemapindex');
    expect(count($xml->sitemap))->toBe(3);
});

it('returns 404 for an unsupported per-locale sitemap', function () {
    $this->get('/sitemap-zz.xml')->assertNotFound();
    $this->get('/sitemap-de.xml')->assertNotFound();
});

it('returns 404 for the index when sitemap is disabled', function () {
    $seo = app(SeoSettings::class);
    $seo->sitemap_enabled = false;
    $seo->save();

    $this->get('/sitemap.xml')->assertNotFound();
});

it('returns 404 for a per-locale sitemap when sitemap is disabled', function () {
    $seo = app(SeoSettings::class);
    $seo->sitemap_enabled = false;
    $seo->save();

    $this->get('/sitemap-en.xml')->assertNotFound();
});

it('robots.txt points the crawler at the sitemap index', function () {
    $body = $this->get('/robots.txt')->assertOk()->getContent();

    expect($body)->toContain('Sitemap: https://grdzelo.test/sitemap.xml');
    expect($body)->not->toContain('sitemap-en.xml');
});

<?php

declare(strict_types=1);

use App\Settings\SeoSettings;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->sitemap_enabled = true;
    $seo->save();
});

it('declares the xhtml namespace on the per-locale urlset', function () {
    $this->get('/sitemap-en.xml')
        ->assertOk()
        ->assertSee('xmlns:xhtml="http://www.w3.org/1999/xhtml"', false);
});

it('emits hreflang alternates for the homepage on the en sitemap', function () {
    $response = $this->get('/sitemap-en.xml')->assertOk();

    $response->assertSee('<xhtml:link rel="alternate" hreflang="en" href="https://grdzelo.test/en"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="ka" href="https://grdzelo.test/ka"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="ru" href="https://grdzelo.test/ru"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="x-default" href="https://grdzelo.test/en"/>', false);
});

it('emits hreflang alternates for inner static pages on the ka sitemap', function () {
    $response = $this->get('/sitemap-ka.xml')->assertOk();

    $response->assertSee('<xhtml:link rel="alternate" hreflang="en" href="https://grdzelo.test/en/about"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="ka" href="https://grdzelo.test/ka/about"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="ru" href="https://grdzelo.test/ru/about"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="x-default" href="https://grdzelo.test/en/about"/>', false);
});

it('lists only the requested locale URLs on each per-locale sitemap', function () {
    $en = $this->get('/sitemap-en.xml')->assertOk();
    $en->assertSee('<loc>https://grdzelo.test/en</loc>', false);
    $en->assertDontSee('<loc>https://grdzelo.test/ka</loc>', false);
    $en->assertDontSee('<loc>https://grdzelo.test/ru</loc>', false);

    $ka = $this->get('/sitemap-ka.xml')->assertOk();
    $ka->assertSee('<loc>https://grdzelo.test/ka</loc>', false);
    $ka->assertDontSee('<loc>https://grdzelo.test/en</loc>', false);
});

it('lists the previously-missing static pages on every per-locale sitemap', function () {
    foreach (['en', 'ka', 'ru'] as $locale) {
        $response = $this->get("/sitemap-{$locale}.xml")->assertOk();

        foreach (['/gallery', '/hobbies', '/education', '/certifications'] as $path) {
            $response->assertSee('<loc>https://grdzelo.test/'.$locale.$path.'</loc>', false);
        }
    }
});

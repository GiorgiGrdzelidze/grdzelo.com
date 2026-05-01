<?php

declare(strict_types=1);

use App\Settings\SeoSettings;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->sitemap_enabled = true;
    $seo->save();
});

it('declares the xhtml namespace on the urlset', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertSee('xmlns:xhtml="http://www.w3.org/1999/xhtml"', false);
});

it('emits hreflang alternates for the homepage', function () {
    $response = $this->get('/sitemap.xml')->assertOk();

    $response->assertSee('<xhtml:link rel="alternate" hreflang="en" href="https://grdzelo.test/"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="ka" href="https://grdzelo.test/ka"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="ru" href="https://grdzelo.test/ru"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="x-default" href="https://grdzelo.test/"/>', false);
});

it('emits hreflang alternates for inner static pages', function () {
    $response = $this->get('/sitemap.xml')->assertOk();

    $response->assertSee('<xhtml:link rel="alternate" hreflang="en" href="https://grdzelo.test/about"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="ka" href="https://grdzelo.test/ka/about"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="ru" href="https://grdzelo.test/ru/about"/>', false);
    $response->assertSee('<xhtml:link rel="alternate" hreflang="x-default" href="https://grdzelo.test/about"/>', false);
});

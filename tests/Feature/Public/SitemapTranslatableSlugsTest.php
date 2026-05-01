<?php

declare(strict_types=1);

use App\Models\Hobby;
use App\Settings\SeoSettings;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->sitemap_enabled = true;
    $seo->save();
});

it('emits each model-backed URL using that locale per-locale slug', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography', 'ka' => 'ფოტოგრაფია']);
    $hobby->setTranslations('slug', ['en' => 'photography', 'ka' => 'potograpia-manual']);
    $hobby->is_visible = true;
    $hobby->save();

    $en = $this->get('/sitemap-en.xml')->assertOk();
    $en->assertSee('<loc>https://grdzelo.test/en/hobbies/photography</loc>', false);

    $ka = $this->get('/sitemap-ka.xml')->assertOk();
    $ka->assertSee('<loc>https://grdzelo.test/ka/hobbies/potograpia-manual</loc>', false);
});

it('alternates on the en sitemap point at the localized slugs', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography']);
    $hobby->setTranslations('slug', ['en' => 'photography', 'ka' => 'potograpia-manual', 'ru' => 'fotografiya']);
    $hobby->is_visible = true;
    $hobby->save();

    $en = $this->get('/sitemap-en.xml')->assertOk();
    $en->assertSee('<xhtml:link rel="alternate" hreflang="en" href="https://grdzelo.test/en/hobbies/photography"/>', false);
    $en->assertSee('<xhtml:link rel="alternate" hreflang="ka" href="https://grdzelo.test/ka/hobbies/potograpia-manual"/>', false);
    $en->assertSee('<xhtml:link rel="alternate" hreflang="ru" href="https://grdzelo.test/ru/hobbies/fotografiya"/>', false);
});

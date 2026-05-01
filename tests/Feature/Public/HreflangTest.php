<?php

declare(strict_types=1);

use App\Models\Hobby;
use App\Settings\SeoSettings;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->save();
});

it('shares hreflang alternates on the en home', function () {
    $this->get('/en')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/en'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/en'],
            ])
        );
});

it('shares hreflang alternates on an inner en page', function () {
    $this->get('/en/about')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/en/about'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka/about'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru/about'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/en/about'],
            ])
        );
});

it('shares hreflang alternates on a /ka-prefixed inner page (en alternate is /en/...)', function () {
    $this->get('/ka/about')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/en/about'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka/about'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru/about'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/en/about'],
            ])
        );
});

it('shares hreflang alternates on the /ru home', function () {
    $this->get('/ru')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/en'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/en'],
            ])
        );
});

it('translates per-locale slugs when emitting hreflang on a translated detail page', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography']);
    $hobby->setTranslations('slug', [
        'en' => 'photography',
        'ka' => 'potograpia-manual',
        'ru' => 'fotografiya',
    ]);
    $hobby->is_visible = true;
    $hobby->save();

    $this->get('/ka/hobbies/potograpia-manual')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/en/hobbies/photography'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka/hobbies/potograpia-manual'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru/hobbies/fotografiya'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/en/hobbies/photography'],
            ])
        );
});

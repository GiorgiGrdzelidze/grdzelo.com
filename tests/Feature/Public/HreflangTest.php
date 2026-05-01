<?php

declare(strict_types=1);

use App\Settings\SeoSettings;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->save();
});

it('shares hreflang alternates on the unprefixed root', function () {
    $this->get('/')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/'],
            ])
        );
});

it('shares hreflang alternates on an unprefixed inner page', function () {
    $this->get('/about')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/about'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka/about'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru/about'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/about'],
            ])
        );
});

it('shares hreflang alternates on a /ka-prefixed page (en alternate strips the prefix)', function () {
    $this->get('/ka/about')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/about'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka/about'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru/about'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/about'],
            ])
        );
});

it('shares hreflang alternates on the /ru root', function () {
    $this->get('/ru')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('hreflang', [
                ['hreflang' => 'en', 'href' => 'https://grdzelo.test/'],
                ['hreflang' => 'ka', 'href' => 'https://grdzelo.test/ka'],
                ['hreflang' => 'ru', 'href' => 'https://grdzelo.test/ru'],
                ['hreflang' => 'x-default', 'href' => 'https://grdzelo.test/'],
            ])
        );
});

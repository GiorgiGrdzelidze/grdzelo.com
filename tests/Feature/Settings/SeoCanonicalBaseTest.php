<?php

use App\Settings\SeoSettings;

it('returns canonical_base when set, trimmed', function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://example.com/';

    expect($seo->canonicalBase())->toBe('https://example.com');
});

it('falls back to config app.url when canonical_base is null', function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = null;

    config(['app.url' => 'https://from-config.test/']);

    expect($seo->canonicalBase())->toBe('https://from-config.test');
});

it('falls back to the hardcoded production base when both are empty', function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = null;

    config(['app.url' => null]);

    expect($seo->canonicalBase())->toBe('https://grdzelo.com');
});

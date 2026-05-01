<?php

declare(strict_types=1);

it('switches the locale and stores it in session + cookie', function () {
    $response = $this->get('/locale/ka?return=/en/about');

    $response->assertRedirect('/ka/about');
    expect(session('locale'))->toBe('ka');
    $response->assertCookie('locale', 'ka');
});

it('rewrites a locale-prefixed return path to use the new locale', function () {
    $response = $this->get('/locale/ru?return=/ka/projects');

    $response->assertRedirect('/ru/projects');
});

it('prepends the new locale when the return path has no locale segment', function () {
    $response = $this->get('/locale/ka?return=/about');

    $response->assertRedirect('/ka/about');
});

it('falls back to /{new_locale} when return path is missing', function () {
    $response = $this->get('/locale/ru');

    $response->assertRedirect('/ru');
});

it('rejects protocol-relative return paths to prevent open redirect', function () {
    $response = $this->get('/locale/en?return=//evil.example/x');

    $response->assertRedirect('/en');
});

it('rejects absolute URLs in the return path', function () {
    $response = $this->get('/locale/en?return=https://evil.example/x');

    $response->assertRedirect('/en');
});

it('returns 404 for an unsupported locale', function () {
    $response = $this->get('/locale/de');

    $response->assertNotFound();
});

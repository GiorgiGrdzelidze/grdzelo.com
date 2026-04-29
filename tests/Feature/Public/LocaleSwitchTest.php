<?php

declare(strict_types=1);

it('switches the locale and stores it in session + cookie', function () {
    $response = $this->get('/locale/ka?return=/about');

    $response->assertRedirect('/about');
    expect(session('locale'))->toBe('ka');
    $response->assertCookie('locale', 'ka');
});

it('falls back to root when return path is missing', function () {
    $response = $this->get('/locale/ru');

    $response->assertRedirect('/');
});

it('rejects protocol-relative return paths to prevent open redirect', function () {
    $response = $this->get('/locale/en?return=//evil.example/x');

    $response->assertRedirect('/');
});

it('rejects absolute URLs in the return path', function () {
    $response = $this->get('/locale/en?return=https://evil.example/x');

    $response->assertRedirect('/');
});

it('returns 404 for an unsupported locale', function () {
    $response = $this->get('/locale/de');

    $response->assertNotFound();
});

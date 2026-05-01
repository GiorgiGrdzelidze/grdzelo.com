<?php

declare(strict_types=1);

use Inertia\Testing\AssertableInertia;

it('renders the editorial NotFound page for unknown public routes', function () {
    $response = $this->get('/en/this-route-does-not-exist');

    $response->assertStatus(404);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Public/NotFound')
        ->where('status', 404)
    );
});

it('returns the standard 404 for admin routes', function () {
    $response = $this->get('/admin/does-not-exist');

    $response->assertStatus(404);
    expect($response->headers->get('X-Inertia'))->toBeNull();
});

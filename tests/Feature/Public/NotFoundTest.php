<?php

declare(strict_types=1);

it('renders the editorial NotFound page for unknown public routes', function () {
    $response = $this->get('/this-route-does-not-exist');

    $response->assertStatus(404);
    $response->assertSee('Public/NotFound', false);
});

it('returns the standard 404 for admin routes', function () {
    $response = $this->get('/admin/does-not-exist');

    $response->assertStatus(404);
    $response->assertDontSee('Public/NotFound', false);
});

<?php

test('returns a successful response', function () {
    $response = $this->get(route('public.home', ['locale' => 'en']));

    $response->assertOk();
});

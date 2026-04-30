<?php

declare(strict_types=1);

use App\Settings\GeneralSettings;
use Inertia\Testing\AssertableInertia;

it('renders the About page with about_image and about_intro when set', function () {
    $settings = app(GeneralSettings::class);
    $settings->about_image = 'settings/portrait.jpg';
    $settings->about_intro = 'A short editorial intro for testing.';
    $settings->save();

    $response = $this->get('/about');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Public/About')
        ->where('settings.about_image', 'settings/portrait.jpg')
        ->where('settings.about_intro', 'A short editorial intro for testing.')
    );
});

it('renders the About page when about_image and about_intro are null', function () {
    $settings = app(GeneralSettings::class);
    $settings->about_image = null;
    $settings->about_intro = null;
    $settings->save();

    $response = $this->get('/about');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Public/About')
        ->where('settings.about_image', null)
        ->where('settings.about_intro', null)
    );
});

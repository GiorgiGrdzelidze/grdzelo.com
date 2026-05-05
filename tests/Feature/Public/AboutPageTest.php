<?php

declare(strict_types=1);

use App\Models\Brand;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia;

it('renders the About page with portrait and about_intro when set', function () {
    Storage::fake('public');

    $settings = app(GeneralSettings::class);
    $settings->about_intro = 'A short editorial intro for testing.';
    $settings->save();

    Brand::current()
        ->addMediaFromString('p')
        ->usingFileName('portrait.jpg')
        ->toMediaCollection('about');

    $response = $this->get('/en/about');

    $response->assertStatus(200);
    $expectedPortrait = Brand::current()->fresh()->getFirstMediaUrl('about');

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Public/About')
        ->where('brand.portrait', $expectedPortrait)
        ->where('settings.about_intro', 'A short editorial intro for testing.')
    );
});

it('renders the About page when portrait and about_intro are null', function () {
    Storage::fake('public');

    $settings = app(GeneralSettings::class);
    $settings->about_intro = null;
    $settings->save();

    $response = $this->get('/en/about');

    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('Public/About')
        ->where('brand.portrait', null)
        ->where('settings.about_intro', null)
    );
});

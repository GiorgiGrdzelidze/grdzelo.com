<?php

declare(strict_types=1);

use App\Models\Project;
use App\Settings\SeoSettings;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->save();
});

it('emits locale-specific meta_title and meta_description on a translated project page', function () {
    $project = new Project;
    $project->setTranslations('title', ['en' => 'My Project', 'ka' => 'ჩემი პროექტი']);
    $project->setTranslations('slug', ['en' => 'my-project', 'ka' => 'chemi-proekti']);
    $project->setTranslations('meta_title', [
        'en' => 'My Project — Portfolio',
        'ka' => 'ჩემი პროექტი — პორტფოლიო',
    ]);
    $project->setTranslations('meta_description', [
        'en' => 'A polished case study in English.',
        'ka' => 'ქართული აღწერა.',
    ]);
    $project->status = 'published';
    $project->is_visible = true;
    $project->save();

    $this->get('/en/projects/my-project')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.title', 'My Project — Portfolio')
            ->where('seo.description', 'A polished case study in English.')
        );

    $this->get('/ka/projects/chemi-proekti')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.title', 'ჩემი პროექტი — პორტფოლიო')
            ->where('seo.description', 'ქართული აღწერა.')
        );
});

it('falls back to the default-locale value when a locale leaves the SEO field blank', function () {
    $project = new Project;
    $project->setTranslations('title', ['en' => 'My Project']);
    $project->setTranslations('slug', ['en' => 'my-project-fallback']);
    $project->setTranslations('meta_title', ['en' => 'English-only meta title']);
    $project->status = 'published';
    $project->is_visible = true;
    $project->save();

    $this->get('/ka/projects/my-project-fallback')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.title', 'English-only meta title')
        );
});

it('emits per-locale og_image_alt and twitter_image_alt', function () {
    $project = new Project;
    $project->setTranslations('title', ['en' => 'Alt Project']);
    $project->setTranslations('slug', ['en' => 'alt-project']);
    $project->setTranslations('og_image_alt', [
        'en' => 'English alt text',
        'ka' => 'ქართული ალტ ტექსტი',
    ]);
    $project->setTranslations('twitter_image_alt', [
        'en' => 'Tw EN alt',
        'ka' => 'Tw KA alt',
    ]);
    $project->og_image = '/storage/seo/cover.jpg';
    $project->status = 'published';
    $project->is_visible = true;
    $project->save();

    $this->get('/en/projects/alt-project')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.og.image_alt', 'English alt text')
            ->where('seo.twitter.image_alt', 'Tw EN alt')
        );

    $this->get('/ka/projects/alt-project')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.og.image_alt', 'ქართული ალტ ტექსტი')
            ->where('seo.twitter.image_alt', 'Tw KA alt')
        );
});

it('emits per-locale robots directive when the model sets one (no boolean override)', function () {
    $project = new Project;
    $project->setTranslations('title', ['en' => 'Robots Project']);
    $project->setTranslations('slug', ['en' => 'robots-project']);
    $project->setTranslations('robots', [
        'en' => 'index, follow',
        'ka' => 'noindex, nofollow',
    ]);
    $project->status = 'published';
    $project->is_visible = true;
    $project->save();

    $this->get('/en/projects/robots-project')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.robots', 'index, follow')
        );

    $this->get('/ka/projects/robots-project')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.robots', 'noindex, nofollow')
        );
});

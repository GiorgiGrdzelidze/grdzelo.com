<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\Project;
use App\Settings\SeoSettings;
use Inertia\Testing\AssertableInertia;

beforeEach(function () {
    $seo = app(SeoSettings::class);
    $seo->canonical_base = 'https://grdzelo.test';
    $seo->save();
});

it('emits a JSON-LD block on a public detail page from the model default', function () {
    $article = new Article;
    $article->setTranslations('title', ['en' => 'JSON-LD Demo']);
    $article->setTranslations('slug', ['en' => 'jsonld-demo']);
    $article->setTranslations('excerpt', ['en' => 'A short summary.']);
    $article->status = 'published';
    $article->publish_at = now()->subDay();
    $article->save();

    $response = $this->get('/en/blog/jsonld-demo');

    $response->assertOk();
    $response->assertSee('<script type="application/ld+json">', false);
    $response->assertSee('"@type":"BlogPosting"', false);
    $response->assertSee('"headline":"JSON-LD Demo"', false);
    $response->assertSee('"inLanguage":"en"', false);
});

it('uses the admin-saved JSON-LD over the default when present', function () {
    $project = new Project;
    $project->setTranslations('title', ['en' => 'Custom JSON-LD']);
    $project->setTranslations('slug', ['en' => 'custom-jsonld']);
    $project->setTranslations('jsonld', [
        'en' => [
            '@context' => 'https://schema.org',
            '@type' => 'CreativeWork',
            'name' => 'Hand-crafted override',
            'inLanguage' => 'en',
            'url' => 'https://example.test/custom',
        ],
    ]);
    $project->status = 'published';
    $project->is_visible = true;
    $project->save();

    $response = $this->get('/en/projects/custom-jsonld');

    $response->assertOk();
    $response->assertSee('"name":"Hand-crafted override"', false);
});

it('emits inLanguage matching the active locale', function () {
    $project = new Project;
    $project->setTranslations('title', ['en' => 'Locale Demo', 'ka' => 'ლოკალის დემო']);
    $project->setTranslations('slug', ['en' => 'locale-demo', 'ka' => 'lokalis-demo']);
    $project->setTranslations('summary', ['en' => 'EN summary', 'ka' => 'KA summary']);
    $project->status = 'published';
    $project->is_visible = true;
    $project->save();

    $this->get('/en/projects/locale-demo')
        ->assertSee('"inLanguage":"en"', false)
        ->assertDontSee('"inLanguage":"ka"', false);

    $this->get('/ka/projects/lokalis-demo')
        ->assertSee('"inLanguage":"ka"', false);
});

it('exposes seo.jsonld on the Inertia props with the active locale payload', function () {
    $article = new Article;
    $article->setTranslations('title', ['en' => 'Inertia Probe', 'ka' => 'ინერტიის ტესტი']);
    $article->setTranslations('slug', ['en' => 'inertia-probe', 'ka' => 'inerciis-testi']);
    $article->setTranslations('excerpt', ['en' => 'EN excerpt', 'ka' => 'KA excerpt']);
    $article->status = 'published';
    $article->publish_at = now()->subDay();
    $article->save();

    $this->get('/ka/blog/inerciis-testi')
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('seo.jsonld.@type', 'BlogPosting')
            ->where('seo.jsonld.headline', 'ინერტიის ტესტი')
            ->where('seo.jsonld.inLanguage', 'ka')
        );
});

it('returns null jsonld for a page without admin override or default generator', function () {
    $project = new Project;
    $project->save();

    expect($project->getJsonLd())->toBeNull();
});

it('parses string JSON stored under the active locale into an array', function () {
    $article = new Article;
    $article->setTranslations('title', ['en' => 'String JSON']);
    $article->setTranslations('slug', ['en' => 'string-json']);
    $article->setTranslations('jsonld', [
        'en' => json_encode(['@context' => 'https://schema.org', '@type' => 'BlogPosting', 'headline' => 'Stored as string']),
    ]);
    $article->status = 'published';
    $article->publish_at = now()->subDay();
    $article->save();

    $jsonld = $article->getJsonLd();

    expect($jsonld)->toBeArray();
    expect($jsonld['headline'])->toBe('Stored as string');
});

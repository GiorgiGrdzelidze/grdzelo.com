<?php

declare(strict_types=1);

use App\Models\Album;
use App\Models\Article;
use App\Models\Hobby;
use App\Models\Page;
use App\Models\Project;
use App\Models\Repository;
use App\Models\Service;

function paragraphDoc(string $text): string
{
    return json_encode([
        'type' => 'doc',
        'content' => [[
            'type' => 'paragraph',
            'content' => [['type' => 'text', 'text' => $text]],
        ]],
    ]);
}

it('Project::description renders TipTap JSON to HTML and passes HTML through', function () {
    $project = Project::create([
        'title' => 'Demo',
        'slug' => 'demo',
        'summary' => 's',
        'description' => paragraphDoc('Stored as JSON.'),
        'is_visible' => true,
    ]);

    expect($project->fresh()->description)->toBe('<p>Stored as JSON.</p>');

    $project->update(['description' => '<p>Already <em>HTML</em>.</p>']);
    expect($project->fresh()->description)->toBe('<p>Already <em>HTML</em>.</p>');
});

it('Project::challenge / solution / process all render TipTap JSON', function () {
    $project = Project::create([
        'title' => 'Demo',
        'slug' => 'demo-2',
        'summary' => 's',
        'challenge' => paragraphDoc('The challenge.'),
        'solution' => paragraphDoc('The solution.'),
        'process' => paragraphDoc('The process.'),
        'is_visible' => true,
    ])->fresh();

    expect($project->challenge)->toBe('<p>The challenge.</p>');
    expect($project->solution)->toBe('<p>The solution.</p>');
    expect($project->process)->toBe('<p>The process.</p>');
});

it('Article::body renders TipTap JSON to HTML', function () {
    $article = Article::create([
        'title' => 'Hello',
        'slug' => 'hello',
        'excerpt' => 'short',
        'body' => paragraphDoc('Hello body.'),
    ]);

    expect($article->fresh()->body)->toBe('<p>Hello body.</p>');
});

it('Service::description renders TipTap JSON to HTML', function () {
    $service = Service::create([
        'title' => 'Consulting',
        'slug' => 'consulting',
        'description' => paragraphDoc('What I offer.'),
    ]);

    expect($service->fresh()->description)->toBe('<p>What I offer.</p>');
});

it('Repository::description renders TipTap JSON to HTML', function () {
    $repo = Repository::create([
        'name' => 'Repo',
        'slug' => 'repo',
        'url' => 'https://github.com/example/repo',
        'description' => paragraphDoc('Open source repo.'),
        'is_visible' => true,
    ]);

    expect($repo->fresh()->description)->toBe('<p>Open source repo.</p>');
});

it('Album::description renders TipTap JSON to HTML', function () {
    $album = Album::create([
        'title' => 'Trip',
        'slug' => 'trip',
        'description' => paragraphDoc('Photos from a trip.'),
        'is_visible' => true,
    ]);

    expect($album->fresh()->description)->toBe('<p>Photos from a trip.</p>');
});

it('Hobby::description renders TipTap JSON to HTML', function () {
    $hobby = Hobby::create([
        'title' => 'Cycling',
        'slug' => 'cycling',
        'description' => paragraphDoc('Mountain bike rides.'),
        'is_visible' => true,
    ]);

    expect($hobby->fresh()->description)->toBe('<p>Mountain bike rides.</p>');
});

it('Page::body renders TipTap JSON to HTML', function () {
    $page = Page::create([
        'title' => 'Custom page',
        'slug' => 'custom',
        'body' => paragraphDoc('Custom page body.'),
    ]);

    expect($page->fresh()->body)->toBe('<p>Custom page body.</p>');
});

it('Article reading time still works after the body accessor (uses HTML, strip_tags)', function () {
    $words = str_repeat('word ', 400);
    $article = Article::create([
        'title' => 'Long',
        'slug' => 'long',
        'excerpt' => 's',
        'body' => paragraphDoc(trim($words)),
    ])->fresh();

    expect($article->reading_time)->toBe(2);
});

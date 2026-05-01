<?php

use App\Models\Repository;

it('renders the show page for a visible repository', function () {
    $repository = Repository::create([
        'name' => 'Inertia Toolkit',
        'slug' => 'inertia-toolkit',
        'url' => 'https://github.com/example/inertia-toolkit',
        'summary' => 'A summary of the toolkit.',
        'description' => '<p>Long-form description.</p>',
        'owner' => 'example',
        'language' => 'TypeScript',
        'technologies' => ['Vue', 'Inertia'],
        'stars' => 42,
        'forks' => 7,
        'status' => 'active',
        'is_featured' => false,
        'is_visible' => true,
        'sort_order' => 0,
    ]);

    $response = $this->get(route('public.repositories.show', ['locale' => 'en', 'repository' => $repository]));

    $response->assertOk();
    $response->assertSee($repository->name);
    $response->assertSee('<title>'.$repository->name.' — '.config('app.name').'</title>', false);
});

it('returns 404 for a hidden repository', function () {
    $repository = Repository::create([
        'name' => 'Hidden Repo',
        'slug' => 'hidden-repo',
        'url' => 'https://github.com/example/hidden-repo',
        'status' => 'active',
        'is_featured' => false,
        'is_visible' => false,
        'sort_order' => 0,
    ]);

    $this->get(route('public.repositories.show', ['locale' => 'en', 'repository' => $repository]))->assertNotFound();
});

it('returns 404 for a non-existent repository', function () {
    $this->get('/en/repositories/does-not-exist')->assertNotFound();
});

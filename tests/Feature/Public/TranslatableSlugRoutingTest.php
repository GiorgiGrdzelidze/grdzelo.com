<?php

declare(strict_types=1);

use App\Models\Hobby;
use App\Models\Project;

it('serves /en/hobbies/{en_slug} when only the default-locale slug is set', function () {
    Hobby::create(['title' => 'Photography', 'is_visible' => true]);

    $this->get('/en/hobbies/photography')->assertOk();
});

it('serves /ka/hobbies/{ka_slug} when both en and ka slugs are saved', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography', 'ka' => 'ფოტოგრაფია']);
    $hobby->setTranslations('slug', ['en' => 'photography', 'ka' => 'potograpia-manual']);
    $hobby->is_visible = true;
    $hobby->save();

    $this->get('/ka/hobbies/potograpia-manual')->assertOk();
});

it('301-redirects /ka/hobbies/{en_slug} to /ka/hobbies/{ka_slug} when ka slug exists', function () {
    $hobby = new Hobby;
    $hobby->setTranslations('title', ['en' => 'Photography', 'ka' => 'ფოტოგრაფია']);
    $hobby->setTranslations('slug', ['en' => 'photography', 'ka' => 'potograpia-manual']);
    $hobby->is_visible = true;
    $hobby->save();

    $this->get('/ka/hobbies/photography')
        ->assertStatus(301)
        ->assertRedirect('/ka/hobbies/potograpia-manual');
});

it('renders /ka/hobbies/{en_slug} in place when ka slug has not been saved yet', function () {
    Hobby::create(['title' => 'Photography', 'is_visible' => true]);

    $this->get('/ka/hobbies/photography')->assertOk();
});

it('returns 404 for /ka/hobbies/{nonexistent_slug}', function () {
    Hobby::create(['title' => 'Photography', 'is_visible' => true]);

    $this->get('/ka/hobbies/does-not-exist')->assertNotFound();
});

it('301-redirects /ru/projects/{en_slug} to /ru/projects/{ru_slug} when ru slug exists', function () {
    $project = new Project;
    $project->setTranslations('title', ['en' => 'My Project', 'ru' => 'Мой проект']);
    $project->setTranslations('slug', ['en' => 'my-project', 'ru' => 'moj-proekt-manual']);
    $project->status = 'published';
    $project->is_visible = true;
    $project->save();

    $this->get('/ru/projects/my-project')
        ->assertStatus(301)
        ->assertRedirect('/ru/projects/moj-proekt-manual');
});

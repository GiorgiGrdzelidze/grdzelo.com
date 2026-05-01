<?php

declare(strict_types=1);

use App\Models\Article;
use App\Models\ArticleCategory;
use Inertia\Testing\AssertableInertia;

function makePublishedArticle(array $attrs = []): Article
{
    return Article::create(array_merge([
        'title' => 'Sample article',
        'slug' => 'sample-'.uniqid(),
        'excerpt' => 'Sample excerpt',
        'body' => 'Body',
        'status' => 'published',
        'publish_at' => now()->subDay(),
    ], $attrs));
}

it('filters articles by category slug from query string', function () {
    $laravel = ArticleCategory::create(['name' => 'Laravel', 'slug' => 'laravel']);
    $vue = ArticleCategory::create(['name' => 'Vue', 'slug' => 'vue']);

    makePublishedArticle(['title' => 'Laravel one', 'article_category_id' => $laravel->id]);
    makePublishedArticle(['title' => 'Laravel two', 'article_category_id' => $laravel->id]);
    makePublishedArticle(['title' => 'Vue one', 'article_category_id' => $vue->id]);

    $this->get('/en/blog?category=laravel')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Blog/Index')
            ->where('activeCategory', 'laravel')
            ->where('articles.total', 2)
            ->has('articles.data', 2)
        );
});

it('returns all published articles when no category param is provided', function () {
    $cat = ArticleCategory::create(['name' => 'Misc', 'slug' => 'misc']);

    makePublishedArticle(['article_category_id' => $cat->id]);
    makePublishedArticle(['article_category_id' => $cat->id]);
    makePublishedArticle(['article_category_id' => null]);

    $this->get('/en/blog')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Public/Blog/Index')
            ->where('activeCategory', null)
            ->where('articles.total', 3)
        );
});

it('exposes paginator total even on the first of multiple pages', function () {
    $cat = ArticleCategory::create(['name' => 'Bulk', 'slug' => 'bulk']);

    foreach (range(1, 15) as $n) {
        makePublishedArticle([
            'title' => "Bulk {$n}",
            'article_category_id' => $cat->id,
        ]);
    }

    $this->get('/en/blog?category=bulk')
        ->assertStatus(200)
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('articles.total', 15)
            ->where('articles.current_page', 1)
            ->where('articles.last_page', 2)
            ->has('articles.data', 12)
        );
});

it('preserves the category filter across pagination links', function () {
    $cat = ArticleCategory::create(['name' => 'Bulk', 'slug' => 'bulk']);

    foreach (range(1, 15) as $n) {
        makePublishedArticle([
            'title' => "Bulk {$n}",
            'article_category_id' => $cat->id,
        ]);
    }

    $response = $this->get('/en/blog?category=bulk');

    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->where('articles.total', 15)
        ->has('articles.links')
    );

    $links = $response->viewData('page')['props']['articles']['links'];
    $nextLink = collect($links)->firstWhere(fn ($l) => $l['url'] !== null && str_contains($l['url'], 'page=2'));

    expect($nextLink)->not->toBeNull();
    expect($nextLink['url'])->toContain('category=bulk');
    expect($nextLink['url'])->toContain('/en/blog');
});

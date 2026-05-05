<?php

namespace App\Http\Controllers\Public;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ArticleController extends BasePublicController
{
    public function index(Request $request): Response
    {
        $categorySlug = $request->string('category')->toString() ?: null;

        $articles = Article::published()
            ->with('category:id,name,slug')
            ->when($categorySlug, fn ($q) => $q->whereHas(
                'category',
                fn ($cat) => $cat->where('slug', $categorySlug)
            ))
            ->latest('publish_at')
            ->paginate(12, [
                'id', 'title', 'slug', 'excerpt',
                'article_category_id', 'publish_at', 'reading_time', 'is_featured',
            ])
            ->withQueryString()
            ->through(fn (Article $article) => $this->articleCard($article));

        $categories = ArticleCategory::query()
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->ordered()
            ->get(['id', 'name', 'slug']);

        $featured = Article::published()
            ->featured()
            ->latest('publish_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'publish_at', 'reading_time'])
            ->map(fn (Article $article) => $this->articleCard($article));

        return Inertia::render('Public/Blog/Index', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Blog'),
            'articles' => $articles,
            'categories' => $categories,
            'featured' => $featured,
            'activeCategory' => $categorySlug,
        ]);
    }

    public function show(string $locale, Article $article): Response|RedirectResponse
    {
        if ($redirect = $this->localizedSlugRedirect($article, 'blog')) {
            return $redirect;
        }

        abort_unless($article->isPublished(), 404);

        $article->load(['category:id,name,slug', 'author:id,name', 'tags']);

        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->when($article->article_category_id, fn ($q) => $q->where('article_category_id', $article->article_category_id))
            ->latest('publish_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'publish_at', 'reading_time'])
            ->map(fn (Article $a) => $this->articleCard($a));

        return Inertia::render('Public/Blog/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor($article),
            'article' => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'body' => $article->body,
                'publish_at' => $article->publish_at?->toIso8601String(),
                'reading_time' => $article->reading_time,
                'is_featured' => $article->is_featured,
                'cover' => $article->getFirstMediaUrl('cover') ?: null,
                'category' => $article->category,
                'author' => $article->author,
                'tags' => $article->tags,
            ],
            'relatedArticles' => $relatedArticles,
        ]);
    }

    private function articleCard(Article $article): array
    {
        return [
            'id' => $article->id,
            'title' => $article->title,
            'slug' => $article->slug,
            'excerpt' => $article->excerpt,
            'category' => $article->relationLoaded('category') ? $article->category : null,
            'publish_at' => $article->publish_at?->toIso8601String(),
            'reading_time' => $article->reading_time,
            'is_featured' => $article->is_featured ?? false,
            'cover' => $article->getFirstMediaUrl('cover') ?: null,
        ];
    }
}

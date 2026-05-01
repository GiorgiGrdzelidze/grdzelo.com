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
                'id', 'title', 'slug', 'excerpt', 'cover_image',
                'article_category_id', 'publish_at', 'reading_time', 'is_featured',
            ])
            ->withQueryString();

        $categories = ArticleCategory::query()
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->ordered()
            ->get(['id', 'name', 'slug']);

        $featured = Article::published()
            ->featured()
            ->latest('publish_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'cover_image', 'publish_at', 'reading_time']);

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
            ->get(['id', 'title', 'slug', 'excerpt', 'cover_image', 'publish_at', 'reading_time']);

        return Inertia::render('Public/Blog/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor($article),
            'article' => $article,
            'relatedArticles' => $relatedArticles,
        ]);
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Models\Article;
use App\Models\Repository;
use App\Settings\SeoSettings;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RepositoryController extends BasePublicController
{
    public function index(): Response
    {
        $repositories = Repository::visible()
            ->ordered()
            ->get()
            ->map(fn (Repository $repo) => [
                'id' => $repo->id,
                'name' => $repo->name,
                'slug' => $repo->slug,
                'url' => $repo->url,
                'summary' => $repo->summary,
                'owner' => $repo->owner,
                'language' => $repo->language,
                'technologies' => $repo->technologies,
                'stars' => $repo->stars,
                'forks' => $repo->forks,
                'status' => $repo->status,
                'is_featured' => $repo->is_featured,
                'demo_url' => $repo->demo_url,
                'cover' => $repo->getFirstMediaUrl('cover') ?: null,
            ]);

        $featured = $repositories->where('is_featured', true)->values();

        return Inertia::render('Public/Repositories/Index', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Repositories'),
            'repositories' => $repositories,
            'featured' => $featured,
        ]);
    }

    public function show(string $locale, Repository $repository): Response|RedirectResponse
    {
        if ($redirect = $this->localizedSlugRedirect($repository, 'repositories')) {
            return $redirect;
        }

        abort_unless($repository->is_visible, 404);

        $repository->load(['project:id,title,slug,summary']);

        $screenshots = $repository->getMedia('screenshots')
            ->map(fn ($media) => [
                'url' => $media->getUrl(),
                'alt' => $media->getCustomProperty('alt'),
            ])
            ->values()
            ->all();

        $relatedArticles = $this->relatedArticlesFor($repository);

        return Inertia::render('Public/Repositories/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoForRepository($repository),
            'repository' => [
                'id' => $repository->id,
                'name' => $repository->name,
                'slug' => $repository->slug,
                'url' => $repository->url,
                'summary' => $repository->summary,
                'description' => $repository->description,
                'owner' => $repository->owner,
                'language' => $repository->language,
                'technologies' => $repository->technologies,
                'stars' => $repository->stars,
                'forks' => $repository->forks,
                'status' => $repository->status,
                'is_featured' => $repository->is_featured,
                'demo_url' => $repository->demo_url,
                'cover' => $repository->getFirstMediaUrl('cover') ?: null,
                'screenshots' => $screenshots,
                'project' => $repository->project ? [
                    'id' => $repository->project->id,
                    'title' => $repository->project->title,
                    'slug' => $repository->project->slug,
                    'summary' => $repository->project->summary,
                    'cover' => $repository->project->getFirstMediaUrl('cover') ?: null,
                ] : null,
            ],
            'relatedArticles' => $relatedArticles,
        ]);
    }

    private function seoForRepository(Repository $repository): array
    {
        $seo = $repository->toSeoArray();

        $base = app(SeoSettings::class)->canonicalBase();
        $self = $this->canonicalForCurrentRequest();

        // Same self-canonical-wins-over-internal-override rule as
        // BasePublicController::seoFor — keeps /ka/repositories/foo from
        // collapsing to en when admin sets repository.canonical_url.
        if (empty($seo['canonical']) || str_starts_with((string) $seo['canonical'], $base)) {
            $seo['canonical'] = $self;
        }

        $coverUrl = $repository->getFirstMediaUrl('cover') ?: null;

        if (empty($seo['og']['image']) && $coverUrl) {
            $seo['og']['image'] = $coverUrl;
        }
        if (! empty($seo['og']['image'])) {
            $seo['og']['image_alt'] ??= $repository->name;
        }
        if (empty($seo['twitter']['image']) && ! empty($seo['og']['image'])) {
            $seo['twitter']['image'] = $seo['og']['image'];
        }
        if (! empty($seo['twitter']['image'])) {
            $seo['twitter']['image_alt'] ??= $seo['og']['image_alt'] ?? $repository->name;
        }

        // JSON-LD `url` follows the resolved canonical (mirrors BasePublicController::seoFor).
        if (isset($seo['jsonld']) && is_array($seo['jsonld']) && empty($seo['jsonld']['url'])) {
            $seo['jsonld']['url'] = $seo['canonical'];
        }

        return $seo;
    }

    private function relatedArticlesFor(Repository $repository): array
    {
        $columns = ['id', 'title', 'slug', 'excerpt', 'publish_at'];
        $technologies = array_filter((array) ($repository->technologies ?? []));

        $articles = collect();

        if (! empty($technologies)) {
            $articles = Article::query()
                ->published()
                ->withAnyTags($technologies)
                ->latest('publish_at')
                ->take(3)
                ->get($columns);
        }

        if ($articles->isEmpty() && $repository->language) {
            $escaped = addcslashes($repository->language, '%_\\');
            $like = '%'.$escaped.'%';
            $articles = Article::query()
                ->published()
                ->where(function ($q) use ($like) {
                    $q->where('title', 'like', $like)
                        ->orWhere('excerpt', 'like', $like);
                })
                ->latest('publish_at')
                ->take(3)
                ->get($columns);
        }

        return $articles
            ->map(fn (Article $article) => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'publish_at' => $article->publish_at?->toIso8601String(),
                'cover' => $article->getFirstMediaUrl('cover') ?: null,
            ])
            ->all();
    }
}

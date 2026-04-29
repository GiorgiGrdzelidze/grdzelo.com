<?php

namespace App\Http\Controllers\Public;

use App\Models\Article;
use App\Models\Repository;
use App\Settings\SeoSettings;
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
                'thumbnail' => $repo->thumbnail,
            ]);

        $featured = $repositories->where('is_featured', true)->values();

        return Inertia::render('Public/Repositories/Index', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Repositories'),
            'repositories' => $repositories,
            'featured' => $featured,
        ]);
    }

    public function show(Repository $repository): Response
    {
        abort_unless($repository->is_visible, 404);

        $repository->load(['project:id,title,slug,summary,cover_image']);

        $screenshots = $repository->getMedia('screenshots')
            ->map(fn ($media) => $media->getUrl())
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
                'thumbnail' => $repository->thumbnail,
                'screenshots' => $screenshots,
                'project' => $repository->project ? [
                    'id' => $repository->project->id,
                    'title' => $repository->project->title,
                    'slug' => $repository->project->slug,
                    'summary' => $repository->project->summary,
                    'cover_image' => $repository->project->cover_image,
                ] : null,
            ],
            'relatedArticles' => $relatedArticles,
        ]);
    }

    private function seoForRepository(Repository $repository): array
    {
        $seo = $repository->toSeoArray();

        $base = app(SeoSettings::class)->canonicalBase();

        if (empty($seo['canonical'])) {
            $seo['canonical'] = $base.route('repositories.show', $repository, false);
        }

        if (empty($seo['og']['image']) && $repository->thumbnail) {
            $seo['og']['image'] = $this->absoluteImageUrl($repository->thumbnail, $base);
        }
        if (! empty($seo['og']['image'])) {
            $seo['og']['image_alt'] ??= $repository->name;
        }
        if (empty($seo['twitter']['image']) && ! empty($seo['og']['image'])) {
            $seo['twitter']['image'] = $seo['og']['image'];
        }

        return $seo;
    }

    private function absoluteImageUrl(string $path, string $base): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $base.$path;
        }

        return $base.'/storage/'.$path;
    }

    private function relatedArticlesFor(Repository $repository): array
    {
        $columns = ['id', 'title', 'slug', 'excerpt', 'cover_image', 'publish_at'];
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
                'cover_image' => $article->cover_image,
                'publish_at' => $article->publish_at?->toIso8601String(),
            ])
            ->all();
    }
}

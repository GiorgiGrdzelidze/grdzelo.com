<?php

namespace App\Http\Controllers\Public;

use App\Models\Repository;
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

        return Inertia::render('Public/Repositories', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Repositories'),
            'repositories' => $repositories,
            'featured' => $featured,
        ]);
    }
}

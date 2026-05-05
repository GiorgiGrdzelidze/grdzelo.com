<?php

namespace App\Http\Controllers\Public;

use App\Models\Album;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class GalleryController extends BasePublicController
{
    public function index(): Response
    {
        $albums = Album::published()
            ->visible()
            ->ordered()
            ->get()
            ->map(fn (Album $album) => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'summary' => $album->summary,
                'cover' => $album->getFirstMediaUrl('cover') ?: null,
                'photo_count' => $album->getMedia('photos')->count(),
                'location' => $album->location,
                'taken_at' => $album->taken_at?->format('M Y'),
                'is_featured' => $album->is_featured,
            ]);

        return Inertia::render('Public/Gallery/Index', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Gallery'),
            'albums' => $albums,
            'featured' => $albums->where('is_featured', true)->values(),
        ]);
    }

    public function show(string $locale, Album $album): Response|RedirectResponse
    {
        if ($redirect = $this->localizedSlugRedirect($album, 'gallery')) {
            return $redirect;
        }

        if (! $album->isPublished() || ! $album->is_visible) {
            abort(404);
        }

        $photos = $album->getMedia('photos')
            ->map(fn ($media, $index) => [
                'id' => $index,
                'url' => $media->getUrl(),
                'thumb' => $media->getUrl(),
                'preview' => $media->getUrl(),
                'alt' => $media->getCustomProperty('alt'),
                'caption' => null,
            ])
            ->values();

        return Inertia::render('Public/Gallery/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor($album, $album->title),
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'summary' => $album->summary,
                'description' => $album->description,
                'cover' => $album->getFirstMediaUrl('cover') ?: null,
                'location' => $album->location,
                'taken_at' => $album->taken_at?->format('F Y'),
            ],
            'photos' => $photos,
        ]);
    }
}

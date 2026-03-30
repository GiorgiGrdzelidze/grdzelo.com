<?php

namespace App\Http\Controllers\Public;

use App\Models\Album;
use Illuminate\Support\Facades\Storage;
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
                'cover' => $album->cover_image ? Storage::url($album->cover_image) : null,
                'photo_count' => $album->photo_count,
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

    public function show(Album $album): Response
    {
        if (! $album->isPublished() || ! $album->is_visible) {
            abort(404);
        }

        $photos = collect($album->photos ?? [])->map(fn ($path, $index) => [
            'id' => $index,
            'url' => Storage::url($path),
            'thumb' => Storage::url($path),
            'preview' => Storage::url($path),
            'alt' => $album->title,
            'caption' => null,
        ]);

        return Inertia::render('Public/Gallery/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor($album, $album->title),
            'album' => [
                'id' => $album->id,
                'title' => $album->title,
                'slug' => $album->slug,
                'summary' => $album->summary,
                'description' => $album->description,
                'cover' => $album->cover_image ? Storage::url($album->cover_image) : null,
                'location' => $album->location,
                'taken_at' => $album->taken_at?->format('F Y'),
            ],
            'photos' => $photos,
        ]);
    }
}

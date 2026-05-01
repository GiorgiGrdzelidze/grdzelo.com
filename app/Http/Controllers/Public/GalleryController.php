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

    public function show(string $locale, Album $album): Response
    {
        if (! $album->isPublished() || ! $album->is_visible) {
            abort(404);
        }

        $photos = collect($album->photos ?? [])
            ->map(function ($entry, $index) use ($album) {
                $path = is_array($entry) ? ($entry['path'] ?? $entry['url'] ?? null) : $entry;
                $caption = is_array($entry) ? ($entry['caption'] ?? null) : null;

                if (! is_string($path) || $path === '') {
                    return null;
                }

                $url = Storage::url($path);

                return [
                    'id' => $index,
                    'url' => $url,
                    'thumb' => $url,
                    'preview' => $url,
                    'alt' => $album->title,
                    'caption' => $caption,
                ];
            })
            ->filter()
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
                'cover' => $album->cover_image ? Storage::url($album->cover_image) : null,
                'location' => $album->location,
                'taken_at' => $album->taken_at?->format('F Y'),
            ],
            'photos' => $photos,
        ]);
    }
}

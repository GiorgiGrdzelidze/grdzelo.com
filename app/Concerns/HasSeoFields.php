<?php

namespace App\Concerns;

trait HasSeoFields
{
    public function getSeoTitle(): string
    {
        return $this->meta_title ?? $this->title ?? '';
    }

    public function getSeoDescription(): ?string
    {
        return $this->meta_description ?? $this->excerpt ?? $this->summary ?? null;
    }

    public function getSeoImage(): ?string
    {
        return $this->og_image ?? $this->cover_image ?? $this->featured_image ?? null;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->canonical_url ?? null;
    }

    public function getRobotsDirective(): ?string
    {
        $directives = [];

        if ($this->noindex ?? false) {
            $directives[] = 'noindex';
        }
        if ($this->nofollow ?? false) {
            $directives[] = 'nofollow';
        }

        return ! empty($directives) ? implode(', ', $directives) : ($this->robots ?? null);
    }

    public function getOgTitle(): string
    {
        return $this->og_title ?? $this->getSeoTitle();
    }

    public function getOgDescription(): ?string
    {
        return $this->og_description ?? $this->getSeoDescription();
    }

    public function getTwitterTitle(): string
    {
        return $this->twitter_title ?? $this->getOgTitle();
    }

    public function getTwitterDescription(): ?string
    {
        return $this->twitter_description ?? $this->getOgDescription();
    }

    public function getTwitterImage(): ?string
    {
        return $this->twitter_image ?? $this->getSeoImage();
    }

    public function toSeoArray(): array
    {
        return [
            'title' => $this->getSeoTitle(),
            'description' => $this->getSeoDescription(),
            'canonical' => $this->getCanonicalUrl(),
            'robots' => $this->getRobotsDirective(),
            'og' => [
                'title' => $this->getOgTitle(),
                'description' => $this->getOgDescription(),
                'image' => $this->getSeoImage(),
                'type' => $this->og_type ?? 'website',
            ],
            'twitter' => [
                'title' => $this->getTwitterTitle(),
                'description' => $this->getTwitterDescription(),
                'image' => $this->getTwitterImage(),
                'card' => $this->twitter_card ?? 'summary_large_image',
            ],
            'schema' => $this->schema_json ?? null,
            'breadcrumb_title' => $this->breadcrumb_title ?? $this->title ?? '',
        ];
    }
}

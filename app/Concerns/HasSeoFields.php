<?php

namespace App\Concerns;

trait HasSeoFields
{
    public function getSeoTitle(): string
    {
        return $this->translatedSeo('meta_title')
            ?? $this->translatedSeo('title')
            ?? '';
    }

    public function getSeoDescription(): ?string
    {
        return $this->translatedSeo('meta_description')
            ?? $this->translatedSeo('excerpt')
            ?? $this->translatedSeo('summary');
    }

    public function getSeoImage(): ?string
    {
        return $this->og_image ?? $this->cover_image ?? $this->featured_image ?? null;
    }

    public function getCanonicalUrl(): ?string
    {
        return $this->translatedSeo('canonical_url');
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

        return ! empty($directives) ? implode(', ', $directives) : $this->translatedSeo('robots');
    }

    public function getOgTitle(): string
    {
        return $this->translatedSeo('og_title') ?? $this->getSeoTitle();
    }

    public function getOgDescription(): ?string
    {
        return $this->translatedSeo('og_description') ?? $this->getSeoDescription();
    }

    public function getOgImageAlt(): ?string
    {
        return $this->translatedSeo('og_image_alt') ?? $this->getOgTitle() ?: null;
    }

    public function getTwitterTitle(): string
    {
        return $this->translatedSeo('twitter_title') ?? $this->getOgTitle();
    }

    public function getTwitterDescription(): ?string
    {
        return $this->translatedSeo('twitter_description') ?? $this->getOgDescription();
    }

    public function getTwitterImage(): ?string
    {
        return $this->twitter_image ?? $this->getSeoImage();
    }

    public function getTwitterImageAlt(): ?string
    {
        return $this->translatedSeo('twitter_image_alt') ?? $this->getOgImageAlt();
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
                'image_alt' => $this->getOgImageAlt(),
                'type' => $this->og_type ?? 'website',
            ],
            'twitter' => [
                'title' => $this->getTwitterTitle(),
                'description' => $this->getTwitterDescription(),
                'image' => $this->getTwitterImage(),
                'image_alt' => $this->getTwitterImageAlt(),
                'card' => $this->twitter_card ?? 'summary_large_image',
            ],
            'schema' => $this->schema_json ?? null,
            'breadcrumb_title' => $this->breadcrumb_title ?? $this->title ?? '',
        ];
    }

    /**
     * Read a translatable SEO column with active-locale → default-locale fallback.
     *
     * Falls through to the magic accessor when the field isn't in the model's
     * `$translatable` set — e.g. `excerpt` on Article (translatable) versus
     * `excerpt` on Project (non-existent). Calling `getTranslation()` on a
     * non-translatable attribute throws under Spatie, so we guard.
     */
    private function translatedSeo(string $field): ?string
    {
        $translatable = is_array($this->translatable ?? null) ? $this->translatable : [];

        if (in_array($field, $translatable, true) && method_exists($this, 'getTranslation')) {
            $value = $this->getTranslation($field, app()->getLocale(), useFallbackLocale: true);

            return is_string($value) && $value !== '' ? $value : null;
        }

        $value = $this->{$field} ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }
}

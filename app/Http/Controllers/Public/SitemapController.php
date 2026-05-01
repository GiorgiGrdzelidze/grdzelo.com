<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Article;
use App\Models\Hobby;
use App\Models\Project;
use App\Models\Repository;
use App\Settings\SeoSettings;
use App\Support\Locale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class SitemapController extends Controller
{
    /**
     * `/sitemap.xml` — a `<sitemapindex>` listing every per-locale sitemap.
     * Crawlers walk the index, then fetch the per-locale `<urlset>` files
     * separately. Per Google's multilingual recommendation, this lets us
     * keep each locale's URLs in its own document while every URL still
     * advertises the other locales' alternates.
     */
    public function index(): Response
    {
        $seo = app(SeoSettings::class);

        if (! $seo->sitemap_enabled) {
            abort(404);
        }

        $base = $seo->canonicalBase();
        $lastmod = $this->mostRecentUpdatedAt();

        $sitemaps = [];
        foreach (Locale::SUPPORTED as $locale) {
            $sitemaps[] = [
                'loc' => $base.'/sitemap-'.$locale.'.xml',
                'lastmod' => $lastmod?->toAtomString(),
            ];
        }

        $xml = view('sitemap-index', ['sitemaps' => $sitemaps])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    /**
     * `/sitemap-{locale}.xml` — a `<urlset>` listing this locale's URLs only,
     * each with reciprocal `<xhtml:link>` alternates pointing at the other
     * locales (and an `x-default` pinning the configured default locale).
     *
     * Slugs come from each model's translatable `slug` column under the
     * requested locale, falling back to the default locale when missing.
     * Setting `App::setLocale($locale)` before reading `$model->slug` lets
     * Spatie's HasTranslations return the right per-locale value.
     */
    public function locale(string $locale): Response
    {
        $seo = app(SeoSettings::class);

        if (! $seo->sitemap_enabled) {
            abort(404);
        }

        if (! Locale::isSupported($locale)) {
            abort(404);
        }

        $base = $seo->canonicalBase();
        $previousLocale = App::getLocale();
        App::setLocale($locale);

        try {
            $urls = $this->buildUrlsForLocale($locale, $base);
        } finally {
            App::setLocale($previousLocale);
        }

        $xml = view('sitemap', ['urls' => $urls])->render();

        return response($xml, 200, [
            'Content-Type' => 'application/xml',
        ]);
    }

    public function robots(): Response
    {
        $seo = app(SeoSettings::class);
        $base = $seo->canonicalBase();

        $lines = [
            'User-agent: *',
        ];

        if ($seo->indexing_enabled) {
            // Explicit per-locale Allows make the canonical paths obvious to
            // crawlers that handle directives line-by-line; the catch-all
            // Allow keeps non-localised utilities (sitemap, robots) reachable.
            $lines[] = 'Allow: /';
            foreach (Locale::SUPPORTED as $locale) {
                $lines[] = 'Allow: /'.$locale.'/';
            }
            $lines[] = 'Disallow: /admin';
            $lines[] = 'Disallow: /dashboard';
            $lines[] = 'Disallow: /livewire';
            $lines[] = 'Disallow: /storage';
        } else {
            $lines[] = 'Disallow: /';
        }

        if ($seo->sitemap_enabled) {
            $lines[] = '';
            $lines[] = 'Sitemap: '.$base.'/sitemap.xml';
        }

        return response(implode("\n", $lines), 200, [
            'Content-Type' => 'text/plain',
        ]);
    }

    /**
     * Build the per-locale `<url>` rows for the requested locale. Static
     * pages emit one entry each; model-backed pages emit one entry per
     * row using that row's per-locale slug, with reciprocal alternates
     * resolving each other locale's slug through Spatie's translatable layer.
     *
     * @return array<int, array<string, mixed>>
     */
    private function buildUrlsForLocale(string $locale, string $base): array
    {
        $urls = [];

        $pushStatic = function (string $relativePath, array $extras = []) use (&$urls, $base, $locale): void {
            $alternates = [];
            foreach (Locale::SUPPORTED as $altLocale) {
                $alternates[] = [
                    'hreflang' => $altLocale,
                    'href' => $base.$this->localePrefix($altLocale, $relativePath),
                ];
            }
            $alternates[] = [
                'hreflang' => 'x-default',
                'href' => $base.$this->localePrefix(Locale::default(), $relativePath),
            ];

            $urls[] = array_merge([
                'loc' => $base.$this->localePrefix($locale, $relativePath),
                'alternates' => $alternates,
            ], $extras);
        };

        $pushModel = function (Model $model, string $segment, array $extras = []) use (&$urls, $base, $locale): void {
            $localizedSlug = $this->slugForLocale($model, $locale);
            if ($localizedSlug === null) {
                return;
            }

            $alternates = [];
            foreach (Locale::SUPPORTED as $altLocale) {
                $altSlug = $this->slugForLocale($model, $altLocale);
                if ($altSlug === null) {
                    continue;
                }
                $alternates[] = [
                    'hreflang' => $altLocale,
                    'href' => $base.'/'.$altLocale.'/'.$segment.'/'.$altSlug,
                ];
            }
            $defaultSlug = $this->slugForLocale($model, Locale::default());
            if ($defaultSlug !== null) {
                $alternates[] = [
                    'hreflang' => 'x-default',
                    'href' => $base.'/'.Locale::default().'/'.$segment.'/'.$defaultSlug,
                ];
            }

            $urls[] = array_merge([
                'loc' => $base.'/'.$locale.'/'.$segment.'/'.$localizedSlug,
                'alternates' => $alternates,
            ], $extras);
        };

        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/projects', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => '/services', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/repositories', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/gallery', 'priority' => '0.6', 'changefreq' => 'monthly'],
            ['loc' => '/about', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/skills', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/experience', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/education', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/certifications', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/hobbies', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/social', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $pushStatic($page['loc'], [
                'priority' => $page['priority'],
                'changefreq' => $page['changefreq'],
            ]);
        }

        Project::published()->visible()->orderBy('sort_order')->each(function ($project) use ($pushModel) {
            $pushModel($project, 'projects', [
                'lastmod' => $project->updated_at->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ]);
        });

        Article::published()->latest('publish_at')->each(function ($article) use ($pushModel) {
            $pushModel($article, 'blog', [
                'lastmod' => $article->updated_at->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ]);
        });

        Repository::visible()->ordered()->each(function ($repository) use ($pushModel) {
            $pushModel($repository, 'repositories', [
                'lastmod' => $repository->updated_at->toAtomString(),
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ]);
        });

        Album::published()->visible()->ordered()->each(function ($album) use ($pushModel) {
            $pushModel($album, 'gallery', [
                'lastmod' => $album->updated_at->toAtomString(),
                'priority' => '0.5',
                'changefreq' => 'monthly',
            ]);
        });

        Hobby::visible()->ordered()->each(function ($hobby) use ($pushModel) {
            $pushModel($hobby, 'hobbies', [
                'lastmod' => $hobby->updated_at->toAtomString(),
                'priority' => '0.4',
                'changefreq' => 'monthly',
            ]);
        });

        return $urls;
    }

    /**
     * Read a model's slug for a specific locale, falling back to the
     * default-locale value when missing. Returns null if neither is set.
     */
    private function slugForLocale(Model $model, string $locale): ?string
    {
        if (! method_exists($model, 'getTranslation')) {
            $value = $model->slug ?? null;

            return is_string($value) && $value !== '' ? $value : null;
        }

        $value = $model->getTranslation('slug', $locale, useFallbackLocale: true);

        return is_string($value) && $value !== '' ? $value : null;
    }

    private function localePrefix(string $locale, string $relativePath): string
    {
        return $relativePath === '/' ? '/'.$locale : '/'.$locale.$relativePath;
    }

    /**
     * The most recent `updated_at` across every model-backed sitemap source,
     * used as the `<lastmod>` for each `<sitemap>` entry in the index. Falls
     * back to "now" when nothing has been published yet.
     */
    private function mostRecentUpdatedAt(): ?Carbon
    {
        $candidates = [
            Project::query()->max('updated_at'),
            Article::query()->max('updated_at'),
            Repository::query()->max('updated_at'),
            Album::query()->max('updated_at'),
            Hobby::query()->max('updated_at'),
        ];

        $latest = null;
        foreach ($candidates as $value) {
            if ($value === null) {
                continue;
            }
            $stamp = $value instanceof Model ? $value->updated_at : Carbon::parse($value);
            if ($latest === null || $stamp->greaterThan($latest)) {
                $latest = $stamp;
            }
        }

        return $latest;
    }
}

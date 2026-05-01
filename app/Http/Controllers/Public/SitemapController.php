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
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function sitemap(): Response
    {
        $seo = app(SeoSettings::class);

        if (! $seo->sitemap_enabled) {
            abort(404);
        }

        $base = $seo->canonicalBase();

        $urls = collect();

        // Push one logical path as three <url> entries — one per locale (en, ka, ru) —
        // each with the same reciprocal hreflang alternates. Per-locale rows is what
        // Google's sitemap docs recommend for multilingual sites.
        $push = function (string $path, array $extras = []) use ($urls, $base): void {
            $alternates = $this->alternatesFor($base, $path);

            foreach ($this->localePaths($path) as $localePath) {
                $urls->push(array_merge([
                    'loc' => $base.$localePath,
                    'alternates' => $alternates,
                ], $extras));
            }
        };

        // Static pages
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
            $push($page['loc'], [
                'priority' => $page['priority'],
                'changefreq' => $page['changefreq'],
            ]);
        }

        // Projects
        Project::published()->visible()->orderBy('sort_order')->each(function ($project) use ($push) {
            $push('/projects/'.$project->slug, [
                'lastmod' => $project->updated_at->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ]);
        });

        // Articles
        Article::published()->latest('publish_at')->each(function ($article) use ($push) {
            $push('/blog/'.$article->slug, [
                'lastmod' => $article->updated_at->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ]);
        });

        // Repositories
        Repository::visible()->ordered()->each(function ($repository) use ($push) {
            $push('/repositories/'.$repository->slug, [
                'lastmod' => $repository->updated_at->toAtomString(),
                'priority' => '0.6',
                'changefreq' => 'monthly',
            ]);
        });

        // Albums
        Album::published()->visible()->ordered()->each(function ($album) use ($push) {
            $push('/gallery/'.$album->slug, [
                'lastmod' => $album->updated_at->toAtomString(),
                'priority' => '0.5',
                'changefreq' => 'monthly',
            ]);
        });

        // Hobbies
        Hobby::visible()->ordered()->each(function ($hobby) use ($push) {
            $push('/hobbies/'.$hobby->slug, [
                'lastmod' => $hobby->updated_at->toAtomString(),
                'priority' => '0.4',
                'changefreq' => 'monthly',
            ]);
        });

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
            $lines[] = 'Allow: /';
            $lines[] = 'Disallow: /admin';
            $lines[] = 'Disallow: /dashboard';
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
     * Build hreflang alternates for a sitemap URL. Path is the locale-agnostic
     * form ('/' for the home, '/about' for inner pages); we emit one alternate
     * per supported locale plus an x-default pointing at the default locale.
     *
     * @return array<int, array{hreflang: string, href: string}>
     */
    private function alternatesFor(string $base, string $path): array
    {
        $alternates = [];
        foreach (Locale::SUPPORTED as $locale) {
            $alternates[] = [
                'hreflang' => $locale,
                'href' => $base.$this->localeUrl($locale, $path),
            ];
        }

        $alternates[] = [
            'hreflang' => 'x-default',
            'href' => $base.$this->localeUrl(Locale::default(), $path),
        ];

        return $alternates;
    }

    /**
     * Map a locale-agnostic path to its per-locale variants in SUPPORTED order.
     *
     * @return array<int, string>
     */
    private function localePaths(string $path): array
    {
        return array_map(
            fn (string $locale): string => $this->localeUrl($locale, $path),
            Locale::SUPPORTED,
        );
    }

    /**
     * Build the locale-prefixed URL path for a given locale + locale-agnostic path.
     * '/' becomes '/{locale}'; '/about' becomes '/{locale}/about'.
     */
    private function localeUrl(string $locale, string $path): string
    {
        return $path === '/' ? '/'.$locale : '/'.$locale.$path;
    }
}

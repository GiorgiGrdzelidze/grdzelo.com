<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Project;
use App\Models\Repository;
use App\Settings\SeoSettings;
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

        $push = function (string $path, array $extras = []) use ($urls, $base): void {
            $urls->push(array_merge([
                'loc' => $base.$path,
                'alternates' => $this->alternatesFor($base, $path),
            ], $extras));
        };

        // Static pages
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/projects', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => '/services', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/repositories', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/about', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/skills', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/experience', 'priority' => '0.5', 'changefreq' => 'monthly'],
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
     * Build hreflang alternates for a sitemap URL. Path is the unprefixed (en) form;
     * we emit the en self-reference, /ka, /ru, and x-default (= en) per Google guidelines.
     *
     * @return array<int, array{hreflang: string, href: string}>
     */
    private function alternatesFor(string $base, string $path): array
    {
        $en = $base.$path;
        $kaPath = $path === '/' ? '/ka' : '/ka'.$path;
        $ruPath = $path === '/' ? '/ru' : '/ru'.$path;

        return [
            ['hreflang' => 'en', 'href' => $en],
            ['hreflang' => 'ka', 'href' => $base.$kaPath],
            ['hreflang' => 'ru', 'href' => $base.$ruPath],
            ['hreflang' => 'x-default', 'href' => $en],
        ];
    }
}

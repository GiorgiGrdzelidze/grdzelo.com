<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Project;
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

        $base = rtrim($seo->canonical_base ?: config('app.url'), '/');

        $urls = collect();

        // Static pages
        $staticPages = [
            ['loc' => '/', 'priority' => '1.0', 'changefreq' => 'weekly'],
            ['loc' => '/projects', 'priority' => '0.8', 'changefreq' => 'weekly'],
            ['loc' => '/blog', 'priority' => '0.8', 'changefreq' => 'daily'],
            ['loc' => '/services', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/about', 'priority' => '0.7', 'changefreq' => 'monthly'],
            ['loc' => '/skills', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/experience', 'priority' => '0.5', 'changefreq' => 'monthly'],
            ['loc' => '/hobbies', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/social', 'priority' => '0.4', 'changefreq' => 'monthly'],
            ['loc' => '/contact', 'priority' => '0.6', 'changefreq' => 'monthly'],
        ];

        foreach ($staticPages as $page) {
            $urls->push([
                'loc' => $base.$page['loc'],
                'priority' => $page['priority'],
                'changefreq' => $page['changefreq'],
            ]);
        }

        // Projects
        Project::published()->visible()->orderBy('sort_order')->each(function ($project) use ($urls, $base) {
            $urls->push([
                'loc' => $base.'/projects/'.$project->slug,
                'lastmod' => $project->updated_at->toAtomString(),
                'priority' => '0.7',
                'changefreq' => 'monthly',
            ]);
        });

        // Articles
        Article::published()->latest('publish_at')->each(function ($article) use ($urls, $base) {
            $urls->push([
                'loc' => $base.'/blog/'.$article->slug,
                'lastmod' => $article->updated_at->toAtomString(),
                'priority' => '0.7',
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
        $base = rtrim($seo->canonical_base ?: config('app.url'), '/');

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
}

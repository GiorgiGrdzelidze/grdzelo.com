<?php

namespace App\Http\Controllers\Public;

use App\Models\Article;
use App\Models\Project;
use App\Models\Service;
use App\Models\Skill;
use App\Models\Testimonial;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends BasePublicController
{
    public function __invoke(): Response
    {
        $featuredProjects = Project::published()
            ->visible()
            ->featured()
            ->orderBy('sort_order')
            ->limit(6)
            ->get(['id', 'title', 'slug', 'summary', 'tech_stack', 'year'])
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'summary' => $project->summary,
                'tech_stack' => $project->tech_stack,
                'year' => $project->year,
                'cover' => $project->getFirstMediaUrl('cover') ?: null,
            ]);

        $latestArticles = Article::published()
            ->latest('publish_at')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'excerpt', 'publish_at', 'reading_time'])
            ->map(fn (Article $article) => [
                'id' => $article->id,
                'title' => $article->title,
                'slug' => $article->slug,
                'excerpt' => $article->excerpt,
                'publish_at' => $article->publish_at?->toIso8601String(),
                'reading_time' => $article->reading_time,
                'cover' => $article->getFirstMediaUrl('cover') ?: null,
            ]);

        $services = Service::query()
            ->featured()
            ->ordered()
            ->limit(6)
            ->get(['id', 'title', 'slug', 'summary', 'icon']);

        $skills = Skill::query()
            ->visible()
            ->featured()
            ->ordered()
            ->limit(12)
            ->get(['id', 'name', 'slug', 'category', 'proficiency_label', 'proficiency_score', 'icon']);

        $testimonials = Testimonial::query()
            ->visible()
            ->featured()
            ->ordered()
            ->limit(6)
            ->get(['id', 'author_name', 'author_role', 'company', 'quote', 'avatar', 'rating']);

        return Inertia::render('Public/Home', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null),
            'featuredProjects' => $featuredProjects,
            'latestArticles' => $latestArticles,
            'services' => $services,
            'skills' => $skills,
            'testimonials' => $testimonials,
        ]);
    }
}

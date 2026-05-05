<?php

namespace App\Http\Controllers\Public;

use App\Filament\Concerns\TranslatableMediaAlt;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends BasePublicController
{
    public function index(): Response
    {
        $projects = Project::published()
            ->visible()
            ->orderBy('sort_order')
            ->get([
                'id', 'title', 'slug', 'summary', 'tech_stack',
                'year', 'is_featured', 'client_type', 'industry', 'metrics',
            ])
            ->map(fn (Project $project) => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'summary' => $project->summary,
                'tech_stack' => $project->tech_stack,
                'year' => $project->year,
                'is_featured' => $project->is_featured,
                'client_type' => $project->client_type,
                'industry' => $project->industry,
                'metrics' => $project->metrics,
                'cover' => $project->getFirstMediaUrl('cover') ?: null,
            ]);

        return Inertia::render('Public/Projects/Index', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Projects'),
            'projects' => $projects,
        ]);
    }

    public function show(string $locale, Project $project): Response|RedirectResponse
    {
        if ($redirect = $this->localizedSlugRedirect($project, 'projects')) {
            return $redirect;
        }

        abort_unless($project->isPublished() && $project->is_visible, 404);

        $project->load(['skills:id,name,slug', 'testimonials:id,author_name,author_role,company,quote,avatar,rating', 'tags']);

        $relatedProjects = Project::published()
            ->visible()
            ->where('id', '!=', $project->id)
            ->orderBy('sort_order')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'summary', 'tech_stack'])
            ->map(fn (Project $p) => [
                'id' => $p->id,
                'title' => $p->title,
                'slug' => $p->slug,
                'summary' => $p->summary,
                'tech_stack' => $p->tech_stack,
                'cover' => $p->getFirstMediaUrl('cover') ?: null,
            ]);

        return Inertia::render('Public/Projects/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor($project),
            'project' => [
                'id' => $project->id,
                'title' => $project->title,
                'slug' => $project->slug,
                'summary' => $project->summary,
                'description' => $project->description,
                'challenge' => $project->challenge,
                'solution' => $project->solution,
                'process' => $project->process,
                'tech_stack' => $project->tech_stack,
                'role' => $project->role,
                'client_type' => $project->client_type,
                'industry' => $project->industry,
                'year' => $project->year,
                'date_start' => $project->date_start?->toIso8601String(),
                'date_end' => $project->date_end?->toIso8601String(),
                'live_url' => $project->live_url,
                'repo_url' => $project->repo_url,
                'metrics' => $project->metrics,
                'cover' => $project->getFirstMediaUrl('cover') ?: null,
                'logo' => $project->getFirstMediaUrl('logo') ?: null,
                'gallery' => $project->getMedia('gallery')->map(fn ($m) => [
                    'url' => $m->getUrl(),
                    'alt' => TranslatableMediaAlt::resolveAlt($m->getCustomProperty('alt')),
                ])->all(),
                'skills' => $project->skills,
                'testimonials' => $project->testimonials,
                'tags' => $project->tags,
            ],
            'relatedProjects' => $relatedProjects,
        ]);
    }
}

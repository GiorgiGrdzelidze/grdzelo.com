<?php

namespace App\Http\Controllers\Public;

use App\Models\Project;
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
                'id', 'title', 'slug', 'summary', 'cover_image',
                'tech_stack', 'year', 'is_featured', 'client_type', 'industry',
            ]);

        return Inertia::render('Public/Projects/Index', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Projects'),
            'projects' => $projects,
        ]);
    }

    public function show(Project $project): Response
    {
        abort_unless($project->isPublished() && $project->is_visible, 404);

        $project->load(['skills:id,name,slug', 'testimonials:id,author_name,author_role,company,quote,avatar,rating', 'tags']);

        $relatedProjects = Project::published()
            ->visible()
            ->where('id', '!=', $project->id)
            ->orderBy('sort_order')
            ->limit(3)
            ->get(['id', 'title', 'slug', 'summary', 'cover_image', 'tech_stack']);

        return Inertia::render('Public/Projects/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor($project),
            'project' => $project,
            'relatedProjects' => $relatedProjects,
        ]);
    }
}

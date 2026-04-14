<?php

namespace App\Http\Controllers\Public;

use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Hobby;
use App\Models\Skill;
use App\Models\SocialLink;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends BasePublicController
{
    public function about(): Response
    {
        $skills = Skill::query()
            ->visible()
            ->ordered()
            ->get(['id', 'name', 'slug', 'category', 'proficiency_label', 'proficiency_score', 'years_experience', 'icon']);

        $experiences = Experience::query()
            ->ordered()
            ->get();

        $education = Education::query()
            ->visible()
            ->ordered()
            ->get();

        $certifications = Certification::query()
            ->visible()
            ->ordered()
            ->with('skills:id,name,slug')
            ->get();

        $hobbies = Hobby::query()
            ->visible()
            ->ordered()
            ->get(['id', 'title', 'slug', 'summary', 'icon', 'is_featured']);

        return Inertia::render('Public/About', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'About'),
            'skills' => $skills,
            'experiences' => $experiences,
            'education' => $education,
            'certifications' => $certifications,
            'hobbies' => $hobbies,
        ]);
    }

    public function skills(): Response
    {
        $skills = Skill::query()
            ->visible()
            ->ordered()
            ->get();

        return Inertia::render('Public/Skills', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Skills'),
            'skills' => $skills,
        ]);
    }

    public function experience(): Response
    {
        $experiences = Experience::query()
            ->ordered()
            ->get();

        return Inertia::render('Public/Experience', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Experience'),
            'experiences' => $experiences,
        ]);
    }

    public function hobbies(): Response
    {
        $hobbies = Hobby::query()
            ->visible()
            ->ordered()
            ->get();

        return Inertia::render('Public/Hobbies', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Hobbies'),
            'hobbies' => $hobbies,
        ]);
    }

    public function hobby(Hobby $hobby): Response
    {
        abort_if(! $hobby->is_visible, 404);

        return Inertia::render('Public/Hobbies/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor($hobby),
            'hobby' => $hobby,
        ]);
    }

    public function social(): Response
    {
        $socialLinks = SocialLink::query()
            ->visible()
            ->ordered()
            ->get();

        return Inertia::render('Public/Social', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Social'),
            'socialLinks' => $socialLinks,
        ]);
    }

    public function education(): Response
    {
        $education = Education::query()
            ->visible()
            ->ordered()
            ->get();

        return Inertia::render('Public/Education', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Education'),
            'education' => $education,
        ]);
    }

    public function certifications(): Response
    {
        $certifications = Certification::query()
            ->visible()
            ->ordered()
            ->with('skills:id,name,slug')
            ->get();

        return Inertia::render('Public/Certifications', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Certifications'),
            'certifications' => $certifications,
        ]);
    }
}

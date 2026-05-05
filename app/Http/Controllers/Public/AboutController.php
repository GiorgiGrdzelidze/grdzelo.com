<?php

namespace App\Http\Controllers\Public;

use App\Filament\Concerns\TranslatableMediaAlt;
use App\Models\Certification;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Hobby;
use App\Models\Skill;
use App\Models\SocialLink;
use Illuminate\Http\RedirectResponse;
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
            ->get()
            ->map(fn (Certification $cert) => $this->certificationPayload($cert));

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
            ->get()
            ->map(fn (Hobby $hobby) => [
                'id' => $hobby->id,
                'title' => $hobby->title,
                'slug' => $hobby->slug,
                'summary' => $hobby->summary,
                'icon' => $hobby->icon,
                'is_featured' => $hobby->is_featured,
                'cover' => $hobby->getFirstMediaUrl('cover') ?: null,
            ]);

        return Inertia::render('Public/Hobbies', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Hobbies'),
            'hobbies' => $hobbies,
        ]);
    }

    public function hobby(string $locale, Hobby $hobby): Response|RedirectResponse
    {
        if ($redirect = $this->localizedSlugRedirect($hobby, 'hobbies')) {
            return $redirect;
        }

        abort_if(! $hobby->is_visible, 404);

        return Inertia::render('Public/Hobbies/Show', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor($hobby),
            'hobby' => [
                'id' => $hobby->id,
                'title' => $hobby->title,
                'slug' => $hobby->slug,
                'summary' => $hobby->summary,
                'description' => $hobby->description,
                'icon' => $hobby->icon,
                'is_featured' => $hobby->is_featured,
                'cover' => $hobby->getFirstMediaUrl('cover') ?: null,
                'gallery' => $hobby->getMedia('gallery')->map(fn ($m) => [
                    'url' => $m->getUrl(),
                    'alt' => TranslatableMediaAlt::resolveAlt($m->getCustomProperty('alt')),
                ])->all(),
            ],
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
            ->get()
            ->map(fn (Certification $cert) => $this->certificationPayload($cert));

        return Inertia::render('Public/Certifications', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Certifications'),
            'certifications' => $certifications,
        ]);
    }

    private function certificationPayload(Certification $cert): array
    {
        return [
            'id' => $cert->id,
            'title' => $cert->title,
            'issuing_organization' => $cert->issuing_organization,
            'description' => $cert->description,
            'issue_date' => $cert->issue_date?->toIso8601String(),
            'expiry_date' => $cert->expiry_date?->toIso8601String(),
            'no_expiry' => $cert->no_expiry,
            'credential_id' => $cert->credential_id,
            'credential_url' => $cert->credential_url,
            'is_featured' => $cert->is_featured,
            'is_visible' => $cert->is_visible,
            'sort_order' => $cert->sort_order,
            'is_expired' => $cert->is_expired,
            'status' => $cert->status,
            'skills' => $cert->relationLoaded('skills') ? $cert->skills : [],
            'badge' => $cert->getFirstMediaUrl('badge') ?: null,
        ];
    }
}

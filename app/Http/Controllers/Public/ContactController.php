<?php

namespace App\Http\Controllers\Public;

use App\Models\ContactSubmission;
use App\Settings\GeneralSettings;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ContactController extends BasePublicController
{
    public function show(): Response
    {
        $general = app(GeneralSettings::class);

        return Inertia::render('Public/Contact', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Contact'),
            'budgetRanges' => $general->budget_ranges,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $general = app(GeneralSettings::class);

        if (! $general->contact_form_enabled) {
            abort(403, 'Contact form is currently disabled.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['nullable', 'string', 'max:255'],
            'budget_range' => ['nullable', 'string', 'max:255'],
            'project_type' => ['nullable', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        ContactSubmission::create([
            ...$validated,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'source' => $request->header('Referer'),
            'status' => 'new',
        ]);

        return back()->with('success', 'Thank you for reaching out! I\'ll get back to you soon.');
    }
}

<?php

namespace App\Http\Controllers\Public;

use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends BasePublicController
{
    public function index(): Response
    {
        $services = Service::query()
            ->ordered()
            ->get();

        return Inertia::render('Public/Services', [
            ...$this->sharedProps(),
            'seo' => $this->seoFor(null, 'Services'),
            'services' => $services,
        ]);
    }
}

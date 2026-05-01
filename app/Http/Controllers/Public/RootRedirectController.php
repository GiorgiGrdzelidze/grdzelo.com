<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

/**
 * Redirects the bare `/` to `/{resolved_locale}` with a 302.
 *
 * 302 (not 301) because the resolved locale can vary per visitor by
 * Accept-Language; a 301 would let an intermediate cache pin one
 * locale's redirect for everyone.
 */
class RootRedirectController extends Controller
{
    public function __invoke(): RedirectResponse
    {
        return redirect('/'.app()->getLocale(), 302)
            ->header('Vary', 'Accept-Language');
    }
}

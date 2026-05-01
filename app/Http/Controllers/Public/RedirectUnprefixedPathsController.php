<?php

declare(strict_types=1);

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Support\Locale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Fallback controller for unprefixed public paths.
 *
 * Any request that doesn't match a registered route lands here. If the
 * path looks like a public page (no asset extension, not a reserved
 * framework prefix), 301-redirect to /{resolved_locale}{path} so the
 * canonical /{locale}/... form is enforced for crawlers and share links.
 *
 * Asset-shaped paths (.js, .css, .png, .xml, .json, fonts, ...) and
 * reserved prefixes (/admin, /storage, /livewire, /horizon, /telescope,
 * /api, /up, /build, /vendor, /fonts) return 404 instead of redirecting,
 * so a missing asset doesn't get rewritten into a non-existent localized
 * URL and confuse the browser.
 */
class RedirectUnprefixedPathsController extends Controller
{
    private const RESERVED_PREFIXES = [
        'admin',
        'storage',
        'livewire',
        'horizon',
        'telescope',
        'api',
        'up',
        'build',
        'vendor',
        'fonts',
        'locale',
    ];

    private const ASSET_EXTENSIONS = [
        'js', 'css', 'map', 'json', 'xml',
        'png', 'jpg', 'jpeg', 'gif', 'webp', 'avif', 'svg', 'ico',
        'woff', 'woff2', 'ttf', 'otf', 'eot',
        'mp3', 'mp4', 'webm', 'pdf',
    ];

    public function __invoke(Request $request): Response|RedirectResponse
    {
        $path = '/'.ltrim($request->getPathInfo(), '/');

        if ($this->shouldNotRedirect($path)) {
            throw new NotFoundHttpException;
        }

        $target = '/'.app()->getLocale().$path;
        $query = $request->getQueryString();
        if ($query !== null && $query !== '') {
            $target .= '?'.$query;
        }

        // SetLocale appends Accept-Language to Vary in its post-response phase.
        return redirect($target, 301);
    }

    private function shouldNotRedirect(string $path): bool
    {
        $first = explode('/', trim($path, '/'), 2)[0] ?? '';

        if ($first !== '' && in_array($first, self::RESERVED_PREFIXES, true)) {
            return true;
        }

        // Already-locale-prefixed paths that didn't match a registered route
        // (e.g. /en/this-doesnt-exist) must 404, not redirect to /en/en/...
        // Lets Laravel render the locale-aware NotFound page without a loop.
        if ($first !== '' && Locale::isSupported($first)) {
            return true;
        }

        // Looks-like-a-locale-code segments (2 lowercase alpha) that aren't
        // in our supported set get a hard 404 instead of redirecting — so
        // /de/about and /zz return 404 cleanly rather than redirecting to
        // /en/de/about. Prevents silent coercion of typo'd locale prefixes.
        if (preg_match('/^[a-z]{2}$/', $first) === 1) {
            return true;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if ($extension !== '' && in_array($extension, self::ASSET_EXTENSIONS, true)) {
            return true;
        }

        return false;
    }
}

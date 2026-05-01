<?php

use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LocaleController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\RedirectUnprefixedPathsController;
use App\Http\Controllers\Public\RepositoryController;
use App\Http\Controllers\Public\RootRedirectController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\SitemapController;
use App\Support\Locale;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Every public URL is canonical at /{locale}/... — no English-at-root.
| Visiting `/` 302-redirects to /{resolved_locale}; any other unprefixed
| public path 301-redirects to /{resolved_locale}{path} via the fallback
| handler.
|
| The {locale} prefix is constrained to the supported set so /de/... 404s
| (and never silently coerces to en, which would create duplicate-content
| variants). SetLocale middleware reads the segment by inspecting the
| path so it works whether the route param is named or not.
*/

Route::get('/', RootRedirectController::class)->name('root');

Route::prefix('{locale}')
    ->where(['locale' => Locale::pattern()])
    ->name('public.')
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');

        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

        Route::get('/blog', [ArticleController::class, 'index'])->name('blog.index');
        Route::get('/blog/{article}', [ArticleController::class, 'show'])->name('blog.show');

        Route::get('/services', [ServiceController::class, 'index'])->name('services');

        Route::get('/repositories', [RepositoryController::class, 'index'])->name('repositories.index');
        Route::get('/repositories/{repository}', [RepositoryController::class, 'show'])->name('repositories.show');

        Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
        Route::get('/gallery/{album}', [GalleryController::class, 'show'])->name('gallery.show');

        Route::get('/about', [AboutController::class, 'about'])->name('about');
        Route::get('/skills', [AboutController::class, 'skills'])->name('skills');
        Route::get('/experience', [AboutController::class, 'experience'])->name('experience');
        Route::get('/hobbies', [AboutController::class, 'hobbies'])->name('hobbies');
        Route::get('/hobbies/{hobby}', [AboutController::class, 'hobby'])->name('hobbies.show');
        Route::get('/social', [AboutController::class, 'social'])->name('social');
        Route::get('/education', [AboutController::class, 'education'])->name('education');
        Route::get('/certifications', [AboutController::class, 'certifications'])->name('certifications');

        Route::get('/contact', [ContactController::class, 'show'])->name('contact');
        Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');
    });

// Flat utilities — never under a locale prefix.
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])
    ->where('locale', Locale::pattern())
    ->name('public.locale.switch');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

// Catch-all for unprefixed public paths — 301 to the locale-prefixed form.
// Asset paths and reserved framework prefixes 404 instead (see controller).
Route::fallback(RedirectUnprefixedPathsController::class);

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| Note: Admin authentication is handled by Filament at /admin.
| No public auth routes are exposed.
*/

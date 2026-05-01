<?php

use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\LocaleController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\RepositoryController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
| Public surface is registered twice:
|   1. At the root for the default locale (en) — primary, named `public.*`.
|   2. Under the {locale} prefix (ka|ru) — same controllers, named
|      `public.localized.*`. SetLocale middleware reads the {locale}
|      segment when present and falls back to session/cookie/header.
|
| Controller methods don't accept a $locale parameter; Laravel matches
| route params by name, so the segment is consumed by the router and
| never reaches the action.
*/

$publicRoutes = function (): void {
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
};

Route::name('public.')->group($publicRoutes);

Route::prefix('{locale}')
    ->where(['locale' => 'ka|ru'])
    ->name('public.localized.')
    ->group($publicRoutes);

Route::get('/locale/{locale}', [LocaleController::class, 'switch'])
    ->where('locale', 'en|ka|ru')
    ->name('public.locale.switch');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| Note: Admin authentication is handled by Filament at /admin.
| No public auth routes are exposed.
*/

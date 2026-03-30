<?php

use App\Http\Controllers\Public\AboutController;
use App\Http\Controllers\Public\ArticleController;
use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\Public\ProjectController;
use App\Http\Controllers\Public\GalleryController;
use App\Http\Controllers\Public\RepositoryController;
use App\Http\Controllers\Public\ServiceController;
use App\Http\Controllers\Public\SitemapController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/blog', [ArticleController::class, 'index'])->name('blog.index');
Route::get('/blog/{article}', [ArticleController::class, 'show'])->name('blog.show');

Route::get('/services', [ServiceController::class, 'index'])->name('services.index');

Route::get('/repositories', [RepositoryController::class, 'index'])->name('repositories.index');

Route::get('/gallery', [GalleryController::class, 'index'])->name('gallery.index');
Route::get('/gallery/{album}', [GalleryController::class, 'show'])->name('gallery.show');

Route::get('/about', [AboutController::class, 'about'])->name('about');
Route::get('/skills', [AboutController::class, 'skills'])->name('skills');
Route::get('/experience', [AboutController::class, 'experience'])->name('experience');
Route::get('/hobbies', [AboutController::class, 'hobbies'])->name('hobbies');
Route::get('/social', [AboutController::class, 'social'])->name('social');
Route::get('/education', [AboutController::class, 'education'])->name('education');
Route::get('/certifications', [AboutController::class, 'certifications'])->name('certifications');

Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('robots');

/*
|--------------------------------------------------------------------------
| Authenticated Routes
|--------------------------------------------------------------------------
| Note: Admin authentication is handled by Filament at /admin.
| No public auth routes are exposed.
*/

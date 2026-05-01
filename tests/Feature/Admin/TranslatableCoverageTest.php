<?php

declare(strict_types=1);

use App\Filament\Resources\AlbumResource;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\HobbyResource;
use App\Filament\Resources\PageResource;
use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\RepositoryResource;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\SkillResource;
use App\Models\Album;
use App\Models\Article;
use App\Models\Hobby;
use App\Models\Page;
use App\Models\Project;
use App\Models\Repository;
use App\Models\Service;
use App\Models\Skill;
use Illuminate\Database\Eloquent\Model;

/**
 * Invariant: every column in a model's $translatable array must appear as
 * `field.{$locale}` in the corresponding Filament Resource's source so admins
 * can edit each locale via the per-locale tab strip. Hand-rolled coverage
 * (no Spatie translatable plugin on Filament v5 yet) means a missed field
 * silently regresses to "edit only the active locale" — this test catches it.
 */
dataset('translatable_resources', [
    'Album' => [Album::class, AlbumResource::class],
    'Article' => [Article::class, ArticleResource::class],
    'Hobby' => [Hobby::class, HobbyResource::class],
    'Page' => [Page::class, PageResource::class],
    'Project' => [Project::class, ProjectResource::class],
    'Repository' => [Repository::class, RepositoryResource::class],
    'Service' => [Service::class, ServiceResource::class],
    'Skill' => [Skill::class, SkillResource::class],
]);

it('uses TranslatableSchema::tabs in every translatable resource', function (string $modelClass, string $resourceClass): void {
    $reflector = new ReflectionClass($resourceClass);
    $source = file_get_contents($reflector->getFileName());

    expect($source)->toContain('TranslatableSchema::tabs(');
})->with('translatable_resources');

it('exposes every translatable field via the per-locale tab closure', function (string $modelClass, string $resourceClass): void {
    /** @var Model $model */
    $model = new $modelClass;
    $translatable = $model->translatable ?? [];

    expect($translatable)->not->toBeEmpty();

    $reflector = new ReflectionClass($resourceClass);
    $source = file_get_contents($reflector->getFileName());

    // The 10 standard SEO string fields are emitted via TranslatableSchema::seoTabs(),
    // which the resource calls inside its SEO tab — they don't need an inline
    // make("$field.\$locale") in the resource source itself.
    $standardSeoFields = [
        'meta_title', 'meta_description', 'canonical_url', 'robots',
        'og_title', 'og_description', 'og_image_alt',
        'twitter_title', 'twitter_description', 'twitter_image_alt',
    ];

    foreach ($translatable as $field) {
        if (in_array($field, $standardSeoFields, true)) {
            $hasSeoTabs = str_contains($source, 'TranslatableSchema::seoTabs(');
            expect($hasSeoTabs)->toBeTrue(
                "{$resourceClass} declares `{$field}` as translatable but doesn't call TranslatableSchema::seoTabs() to surface the SEO tab strip."
            );

            continue;
        }

        if ($field === 'jsonld') {
            $hasJsonLdTabs = str_contains($source, 'TranslatableSchema::jsonLdTabs(');
            expect($hasJsonLdTabs)->toBeTrue(
                "{$resourceClass} declares `jsonld` as translatable but doesn't call TranslatableSchema::jsonLdTabs() to surface the JSON-LD tab strip."
            );

            continue;
        }

        $pattern = '/make\(\s*["\']'.preg_quote($field, '/').'\.\{?\$locale\}?["\']/';
        $matched = preg_match($pattern, $source) === 1;

        expect($matched)->toBeTrue(
            "{$resourceClass} must call ->make('{$field}.\$locale') somewhere in its form schema."
        );
    }
})->with('translatable_resources');

it('declares the trait imports on every translatable resource', function (string $modelClass, string $resourceClass): void {
    $reflector = new ReflectionClass($resourceClass);
    $source = file_get_contents($reflector->getFileName());

    expect($source)->toContain('use App\Filament\Concerns\TranslatableSchema;');
    expect($source)->toContain('use App\Filament\Concerns\TranslationCompleteness;');
})->with('translatable_resources');

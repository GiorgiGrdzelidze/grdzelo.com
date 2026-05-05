<?php

declare(strict_types=1);

use App\Filament\Pages\ManageBrand;
use App\Models\Brand;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

beforeEach(function (): void {
    $this->actingAs(User::factory()->create());
});

it('renders the Brand admin page for an authenticated panel user', function (): void {
    $this->get('/admin/brand')->assertOk();
});

it('mounts the page bound to Brand::current()', function (): void {
    Livewire::test(ManageBrand::class)
        ->assertOk()
        ->assertSet('record.id', Brand::current()->id);
});

it('persists an uploaded file to the about media collection', function (): void {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('portrait.jpg', 800, 1000);

    Livewire::test(ManageBrand::class)
        ->set('data.about', [$file])
        ->call('save')
        ->assertHasNoErrors();

    $brand = Brand::current();

    expect($brand->getMedia('about'))->toHaveCount(1);
    expect($brand->getFirstMediaUrl('about'))->not->toBeEmpty();
});

it('replaces the existing file when a second one is uploaded (singleFile collection)', function (): void {
    Storage::fake('public');

    $first = UploadedFile::fake()->image('one.jpg');
    $second = UploadedFile::fake()->image('two.jpg');

    Livewire::test(ManageBrand::class)
        ->set('data.about', [$first])
        ->call('save')
        ->assertHasNoErrors();

    $firstMediaId = Brand::current()->getFirstMedia('about')->id;

    Livewire::test(ManageBrand::class)
        ->set('data.about', [$second])
        ->call('save')
        ->assertHasNoErrors();

    $brand = Brand::current();

    expect($brand->getMedia('about'))->toHaveCount(1);
    expect($brand->getFirstMedia('about')->id)->not->toBe($firstMediaId);
});

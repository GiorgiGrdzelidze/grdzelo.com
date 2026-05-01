<?php

declare(strict_types=1);

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Log;

it('logs a warning when ADMIN_NAME or ADMIN_EMAIL is missing in production', function () {
    putenv('ADMIN_NAME');
    putenv('ADMIN_EMAIL');

    app()->detectEnvironment(fn () => 'production');

    Log::shouldReceive('warning')
        ->once()
        ->with(Mockery::pattern('/ADMIN_NAME or ADMIN_EMAIL missing in production/'));

    $seeder = $this->partialMock(DatabaseSeeder::class, function ($mock) {
        $mock->shouldReceive('call')->andReturnNull();
    });

    $seeder->run();

    expect(User::count())->toBe(1);
});

it('does not log a warning when both env vars are set in production', function () {
    putenv('ADMIN_NAME=Test Admin');
    putenv('ADMIN_EMAIL=admin@example.test');

    app()->detectEnvironment(fn () => 'production');

    Log::shouldReceive('warning')->never();

    $seeder = $this->partialMock(DatabaseSeeder::class, function ($mock) {
        $mock->shouldReceive('call')->andReturnNull();
    });

    $seeder->run();

    $admin = User::first();
    expect($admin->name)->toBe('Test Admin');
    expect($admin->email)->toBe('admin@example.test');

    putenv('ADMIN_NAME');
    putenv('ADMIN_EMAIL');
});

it('does not log a warning in non-production environments even with empty env', function () {
    putenv('ADMIN_NAME');
    putenv('ADMIN_EMAIL');

    app()->detectEnvironment(fn () => 'local');

    Log::shouldReceive('warning')->never();

    $seeder = $this->partialMock(DatabaseSeeder::class, function ($mock) {
        $mock->shouldReceive('call')->andReturnNull();
    });

    $seeder->run();

    expect(User::count())->toBe(1);
});

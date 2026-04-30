<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $attributes = [];
        $name = env('ADMIN_NAME');
        $email = env('ADMIN_EMAIL');

        if ($name) {
            $attributes['name'] = $name;
        }

        if ($email) {
            $attributes['email'] = $email;
        }

        if (App::environment('production') && (! $name || ! $email)) {
            Log::warning('DatabaseSeeder: ADMIN_NAME or ADMIN_EMAIL missing in production — falling back to factory-generated admin user. Set both env vars before running seeders in production.');
        }

        User::factory()->create($attributes);

        $this->call(ContentSeeder::class);

        if (! App::environment('production')) {
            $this->call(DemoDataSeeder::class);
        }
    }
}

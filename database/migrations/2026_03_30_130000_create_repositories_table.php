<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('repositories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('url');
            $table->string('summary', 500)->nullable();
            $table->text('description')->nullable();
            $table->string('owner')->nullable();
            $table->string('language')->nullable();
            $table->json('technologies')->nullable();
            $table->unsignedInteger('stars')->default(0);
            $table->unsignedInteger('forks')->default(0);
            $table->string('status')->default('active');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_visible')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->string('demo_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->foreignId('project_id')->nullable()->constrained()->nullOnDelete();

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('og_image')->nullable();

            $table->timestamps();

            $table->index(['is_visible', 'is_featured']);
            $table->index('sort_order');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('repositories');
    }
};

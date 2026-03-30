<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('summary', 500)->nullable();
            $table->longText('description')->nullable();
            $table->text('challenge')->nullable();
            $table->text('solution')->nullable();
            $table->text('process')->nullable();
            $table->json('tech_stack')->nullable();
            $table->string('role')->nullable();
            $table->string('client_type')->nullable();
            $table->string('industry')->nullable();
            $table->unsignedSmallInteger('year')->nullable();
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->string('status')->default('draft');
            $table->timestamp('publish_at')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->string('cover_image')->nullable();
            $table->json('gallery')->nullable();
            $table->string('logo')->nullable();
            $table->string('live_url')->nullable();
            $table->string('repo_url')->nullable();
            $table->json('case_study_blocks')->nullable();
            $table->json('metrics')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);

            // SEO fields
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 500)->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('robots')->nullable();
            $table->boolean('noindex')->default(false);
            $table->boolean('nofollow')->default(false);
            $table->string('og_title')->nullable();
            $table->string('og_description', 500)->nullable();
            $table->string('og_image')->nullable();
            $table->string('og_type')->default('website');
            $table->string('twitter_title')->nullable();
            $table->string('twitter_description', 500)->nullable();
            $table->string('twitter_image')->nullable();
            $table->string('twitter_card')->default('summary_large_image');
            $table->json('schema_json')->nullable();
            $table->string('breadcrumb_title')->nullable();

            $table->timestamps();

            $table->index(['status', 'publish_at']);
            $table->index(['status', 'is_visible']);
            $table->index(['is_featured', 'sort_order']);
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};

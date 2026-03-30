<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('category')->nullable();
            $table->string('proficiency_label')->nullable();
            $table->unsignedTinyInteger('proficiency_score')->default(0);
            $table->unsignedTinyInteger('years_experience')->default(0);
            $table->string('icon')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['is_featured', 'sort_order']);
            $table->index('category');
        });

        Schema::create('project_skill', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('skill_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'skill_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_skill');
        Schema::dropIfExists('skills');
    }
};

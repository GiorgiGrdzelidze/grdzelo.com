<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('author_name');
            $table->string('author_role')->nullable();
            $table->string('company')->nullable();
            $table->text('quote');
            $table->string('avatar')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_visible')->default(true);
            $table->timestamps();

            $table->index(['is_featured', 'sort_order']);
        });

        Schema::create('project_testimonial', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('testimonial_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'testimonial_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_testimonial');
        Schema::dropIfExists('testimonials');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('experiences', function (Blueprint $table) {
            $table->id();
            $table->string('company');
            $table->string('role');
            $table->string('type')->default('job');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_current')->default(false);
            $table->text('summary')->nullable();
            $table->json('achievements')->nullable();
            $table->json('technologies')->nullable();
            $table->string('website_url')->nullable();
            $table->string('logo')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('sort_order');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('experiences');
    }
};

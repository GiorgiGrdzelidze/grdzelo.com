<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_sources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('freelance');
            $table->boolean('is_recurring')->default(false);
            $table->decimal('amount', 12, 2)->default(0);
            $table->string('currency', 10)->default('GEL');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('interval')->nullable();
            $table->unsignedTinyInteger('expected_day')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_sources');
    }
};

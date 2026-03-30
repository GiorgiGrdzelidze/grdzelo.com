<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_source_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('GEL');
            $table->boolean('is_received')->default(false);
            $table->timestamp('received_at')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('date');
            $table->index('is_received');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_entries');
    }
};

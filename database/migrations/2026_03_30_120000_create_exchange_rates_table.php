<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exchange_rates', function (Blueprint $table) {
            $table->id();
            $table->string('from_currency', 10);
            $table->string('to_currency', 10);
            $table->decimal('rate', 16, 8);
            $table->date('rate_date');
            $table->string('source')->default('manual');
            $table->timestamps();

            $table->unique(['from_currency', 'to_currency', 'rate_date']);
            $table->index('rate_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exchange_rates');
    }
};

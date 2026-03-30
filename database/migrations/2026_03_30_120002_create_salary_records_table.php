<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('income_source_id')->nullable()->constrained()->nullOnDelete();
            $table->string('employer');
            $table->decimal('gross_amount', 12, 2);
            $table->string('currency', 10)->default('GEL');
            $table->decimal('tax_percentage', 5, 2)->default(20.00);
            $table->decimal('tax_amount', 12, 2);
            $table->decimal('net_amount', 12, 2);
            $table->string('pay_frequency')->default('monthly');
            $table->unsignedTinyInteger('payment_day')->nullable();
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('is_active');
            $table->index('employer');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('income_sources', function (Blueprint $table) {
            $table->text('description')->nullable()->after('notes');
        });

        Schema::table('income_entries', function (Blueprint $table) {
            $table->decimal('exchange_rate', 16, 8)->nullable()->after('currency');
            $table->decimal('base_amount', 12, 2)->nullable()->after('exchange_rate');
            $table->string('base_currency', 10)->default('GEL')->after('base_amount');
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->decimal('exchange_rate', 16, 8)->nullable()->after('currency');
            $table->decimal('base_amount', 12, 2)->nullable()->after('exchange_rate');
            $table->string('base_currency', 10)->default('GEL')->after('base_amount');
            $table->string('merchant')->nullable()->after('interval');
        });
    }

    public function down(): void
    {
        Schema::table('income_sources', function (Blueprint $table) {
            $table->dropColumn('description');
        });

        Schema::table('income_entries', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'base_amount', 'base_currency']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['exchange_rate', 'base_amount', 'base_currency', 'merchant']);
        });
    }
};

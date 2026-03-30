<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('provider')->nullable();
            $table->string('category')->nullable();
            $table->decimal('amount', 12, 2);
            $table->string('currency', 10)->default('GEL');
            $table->string('billing_interval')->default('monthly');
            $table->date('start_date');
            $table->date('next_billing_date')->nullable();
            $table->unsignedTinyInteger('renewal_day')->nullable();
            $table->string('status')->default('active');
            $table->boolean('auto_renew')->default(true);
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('resumed_at')->nullable();
            $table->date('ended_at')->nullable();
            $table->foreignId('expense_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('website_url')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('reminders_enabled')->default(true);
            $table->unsignedSmallInteger('reminder_days_before')->default(3);
            $table->timestamps();

            $table->index('status');
            $table->index('next_billing_date');
            $table->index('billing_interval');
        });

        Schema::create('subscription_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscription_id')->constrained()->cascadeOnDelete();
            $table->string('event_type');
            $table->decimal('old_amount', 12, 2)->nullable();
            $table->decimal('new_amount', 12, 2)->nullable();
            $table->text('note')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('occurred_at');
            $table->timestamps();

            $table->index('event_type');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscription_events');
        Schema::dropIfExists('subscriptions');
    }
};

<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\SubscriptionReminder;
use App\Settings\FinanceSettings;
use Illuminate\Console\Command;

class GenerateSubscriptionReminders extends Command
{
    protected $signature = 'subscriptions:generate-reminders';

    protected $description = 'Generate upcoming subscription renewal reminders';

    public function handle(): int
    {
        $financeSettings = app(FinanceSettings::class);

        if (! $financeSettings->reminders_enabled) {
            $this->info('Subscription reminders are disabled in settings.');

            return self::SUCCESS;
        }

        $subscriptions = Subscription::active()
            ->where('reminders_enabled', true)
            ->whereNotNull('next_billing_date')
            ->get();

        $created = 0;

        foreach ($subscriptions as $subscription) {
            $remindOn = $subscription->next_billing_date
                ->subDays($subscription->reminder_days_before);

            if ($remindOn->isPast()) {
                continue;
            }

            $exists = SubscriptionReminder::where('subscription_id', $subscription->id)
                ->where('remind_on', $remindOn->toDateString())
                ->exists();

            if ($exists) {
                continue;
            }

            SubscriptionReminder::create([
                'subscription_id' => $subscription->id,
                'remind_on' => $remindOn,
                'type' => 'renewal',
                'channel' => 'mail',
            ]);

            $created++;
            $this->line("  → Created reminder for: {$subscription->title} on {$remindOn->toDateString()}");
        }

        $this->info("Generated {$created} new reminder(s).");

        return self::SUCCESS;
    }
}

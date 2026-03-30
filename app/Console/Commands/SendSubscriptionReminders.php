<?php

namespace App\Console\Commands;

use App\Models\SubscriptionReminder;
use App\Models\User;
use App\Notifications\SubscriptionRenewalReminder;
use App\Settings\FinanceSettings;
use Illuminate\Console\Command;

class SendSubscriptionReminders extends Command
{
    protected $signature = 'subscriptions:send-reminders';

    protected $description = 'Send pending subscription renewal reminders';

    public function handle(): int
    {
        $financeSettings = app(FinanceSettings::class);

        if (! $financeSettings->reminders_enabled) {
            $this->info('Subscription reminders are disabled in settings.');

            return self::SUCCESS;
        }

        $reminders = SubscriptionReminder::with('subscription')
            ->dueToday()
            ->get();

        if ($reminders->isEmpty()) {
            $this->info('No reminders due today.');

            return self::SUCCESS;
        }

        $user = User::first();

        if (! $user) {
            $this->error('No user found to send notifications to.');

            return self::FAILURE;
        }

        $sent = 0;

        foreach ($reminders as $reminder) {
            if (! $reminder->subscription || ! $reminder->subscription->reminders_enabled) {
                $reminder->markAsSent();
                continue;
            }

            $user->notify(new SubscriptionRenewalReminder($reminder->subscription));
            $reminder->markAsSent();
            $sent++;

            $this->line("  → Sent reminder for: {$reminder->subscription->title}");
        }

        $this->info("Sent {$sent} subscription reminder(s).");

        return self::SUCCESS;
    }
}

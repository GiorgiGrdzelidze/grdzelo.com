<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionRenewalReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscription $subscription,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $sub = $this->subscription;

        return (new MailMessage)
            ->subject("Subscription Renewal: {$sub->title}")
            ->greeting("Subscription Renewal Reminder")
            ->line("Your subscription **{$sub->title}** is due for renewal.")
            ->line("**Provider:** {$sub->provider}")
            ->line("**Amount:** {$sub->currency->symbol()}" . number_format((float) $sub->amount, 2))
            ->line("**Billing Date:** {$sub->next_billing_date->format('M j, Y')}")
            ->line("**Interval:** {$sub->billing_interval->label()}")
            ->action('Manage Subscriptions', url('/admin/subscriptions'))
            ->line('This is an automated reminder.');
    }

    public function toArray(object $notifiable): array
    {
        $sub = $this->subscription;

        return [
            'subscription_id' => $sub->id,
            'title' => $sub->title,
            'amount' => (float) $sub->amount,
            'currency' => $sub->currency->value,
            'next_billing_date' => $sub->next_billing_date?->toDateString(),
        ];
    }
}

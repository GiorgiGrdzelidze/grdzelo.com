<?php

namespace App\Enums;

enum SubscriptionEventType: string
{
    case Started = 'started';
    case Renewed = 'renewed';
    case Paused = 'paused';
    case Resumed = 'resumed';
    case Cancelled = 'cancelled';
    case Expired = 'expired';
    case PriceChanged = 'price_changed';
    case PlanChanged = 'plan_changed';

    public function label(): string
    {
        return match ($this) {
            self::Started => 'Started',
            self::Renewed => 'Renewed',
            self::Paused => 'Paused',
            self::Resumed => 'Resumed',
            self::Cancelled => 'Cancelled',
            self::Expired => 'Expired',
            self::PriceChanged => 'Price Changed',
            self::PlanChanged => 'Plan Changed',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $case) => [$case->value => $case->label()])
            ->all();
    }
}

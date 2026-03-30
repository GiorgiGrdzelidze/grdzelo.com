<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Enums\SubscriptionEventType;
use App\Filament\Resources\SubscriptionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected function afterCreate(): void
    {
        $this->record->recordEvent(SubscriptionEventType::Started, 'Subscription created');
    }
}

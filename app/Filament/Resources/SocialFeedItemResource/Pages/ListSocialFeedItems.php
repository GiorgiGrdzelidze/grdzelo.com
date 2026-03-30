<?php

namespace App\Filament\Resources\SocialFeedItemResource\Pages;

use App\Filament\Resources\SocialFeedItemResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSocialFeedItems extends ListRecords
{
    protected static string $resource = SocialFeedItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}

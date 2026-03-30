<?php

namespace App\Filament\Resources\SocialFeedItemResource\Pages;

use App\Filament\Resources\SocialFeedItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSocialFeedItem extends EditRecord
{
    protected static string $resource = SocialFeedItemResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

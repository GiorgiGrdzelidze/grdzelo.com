<?php

namespace App\Filament\Resources\HobbyResource\Pages;

use App\Filament\Concerns\HandlesMediaAltState;
use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\HobbyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHobby extends EditRecord
{
    use HandlesMediaAltState, HandlesTranslatableForm;

    protected static string $resource = HobbyResource::class;

    protected function mediaAltCollections(): array
    {
        return ['cover'];
    }

    protected function afterSave(): void
    {
        $this->persistMediaAltState();
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

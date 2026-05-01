<?php

namespace App\Filament\Resources\HobbyResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\HobbyResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHobby extends EditRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = HobbyResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

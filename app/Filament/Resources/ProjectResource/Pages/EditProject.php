<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Concerns\HandlesMediaAltState;
use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    use HandlesMediaAltState, HandlesTranslatableForm;

    protected static string $resource = ProjectResource::class;

    protected function mediaAltCollections(): array
    {
        return ['cover', 'logo'];
    }

    protected function afterSave(): void
    {
        $this->persistMediaAltState();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

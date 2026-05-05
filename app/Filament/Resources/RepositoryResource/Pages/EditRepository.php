<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Concerns\HandlesMediaAltState;
use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\RepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepository extends EditRecord
{
    use HandlesMediaAltState, HandlesTranslatableForm;

    protected static string $resource = RepositoryResource::class;

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
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

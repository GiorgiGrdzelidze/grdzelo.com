<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\RepositoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRepository extends EditRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = RepositoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

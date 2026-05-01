<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\AlbumResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlbum extends EditRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = AlbumResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

<?php

namespace App\Filament\Resources\AlbumResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\AlbumResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAlbum extends CreateRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = AlbumResource::class;
}

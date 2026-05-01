<?php

namespace App\Filament\Resources\HobbyResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\HobbyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateHobby extends CreateRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = HobbyResource::class;
}

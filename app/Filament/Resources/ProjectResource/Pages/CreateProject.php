<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\ProjectResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProject extends CreateRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = ProjectResource::class;
}

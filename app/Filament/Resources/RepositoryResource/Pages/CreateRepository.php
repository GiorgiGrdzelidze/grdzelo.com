<?php

namespace App\Filament\Resources\RepositoryResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\RepositoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRepository extends CreateRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = RepositoryResource::class;
}

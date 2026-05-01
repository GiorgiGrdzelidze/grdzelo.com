<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\ServiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateService extends CreateRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = ServiceResource::class;
}

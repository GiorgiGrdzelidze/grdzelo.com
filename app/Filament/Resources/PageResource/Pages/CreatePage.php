<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\PageResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePage extends CreateRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = PageResource::class;
}

<?php

namespace App\Filament\Resources\PageResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\PageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPage extends EditRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}

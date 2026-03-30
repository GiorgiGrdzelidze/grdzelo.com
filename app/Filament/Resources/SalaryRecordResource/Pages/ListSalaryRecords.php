<?php

namespace App\Filament\Resources\SalaryRecordResource\Pages;

use App\Filament\Resources\SalaryRecordResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSalaryRecords extends ListRecords
{
    protected static string $resource = SalaryRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}

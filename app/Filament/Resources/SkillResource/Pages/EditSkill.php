<?php

namespace App\Filament\Resources\SkillResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\SkillResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSkill extends EditRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = SkillResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

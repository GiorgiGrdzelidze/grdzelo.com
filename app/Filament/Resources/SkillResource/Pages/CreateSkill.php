<?php

namespace App\Filament\Resources\SkillResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\SkillResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSkill extends CreateRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = SkillResource::class;
}

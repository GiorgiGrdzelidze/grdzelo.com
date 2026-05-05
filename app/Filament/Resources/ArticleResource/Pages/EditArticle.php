<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Concerns\HandlesMediaAltState;
use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\ArticleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    use HandlesMediaAltState, HandlesTranslatableForm;

    protected static string $resource = ArticleResource::class;

    protected function mediaAltCollections(): array
    {
        return ['cover'];
    }

    protected function afterSave(): void
    {
        $this->persistMediaAltState();
    }

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }
}

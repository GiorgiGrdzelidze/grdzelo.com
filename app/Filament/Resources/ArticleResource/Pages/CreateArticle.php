<?php

namespace App\Filament\Resources\ArticleResource\Pages;

use App\Filament\Concerns\HandlesTranslatableForm;
use App\Filament\Resources\ArticleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateArticle extends CreateRecord
{
    use HandlesTranslatableForm;

    protected static string $resource = ArticleResource::class;
}

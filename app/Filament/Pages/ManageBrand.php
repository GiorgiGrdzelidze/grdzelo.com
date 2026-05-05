<?php

namespace App\Filament\Pages;

use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class ManageBrand extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paint-brush';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Brand';

    protected static ?string $slug = 'brand';

    protected string $view = 'filament.pages.manage-brand';

    public ?array $data = [];

    public Brand $record;

    public function mount(): void
    {
        $this->record = Brand::current();
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->model($this->record)
            ->statePath('data')
            ->schema([
                Forms\Components\SpatieMediaLibraryFileUpload::make('about')
                    ->collection('about')
                    ->image()
                    ->responsiveImages()
                    ->label('About portrait')
                    ->helperText('Square or 4:5 aspect ratio works best.'),
            ]);
    }

    public function save(): void
    {
        $this->form->getState();

        Notification::make()
            ->title('Saved')
            ->success()
            ->send();
    }
}

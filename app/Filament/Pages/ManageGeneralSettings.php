<?php

namespace App\Filament\Pages;

use App\Settings\GeneralSettings;
use Filament\Forms;
use Filament\Pages\SettingsPage;
use Filament\Schemas;
use Filament\Schemas\Schema;

class ManageGeneralSettings extends SettingsPage
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string|\UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $title = 'General Settings';

    protected static string $settings = GeneralSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Settings')->tabs([
                Schemas\Components\Tabs\Tab::make('Site')->schema([
                    Forms\Components\TextInput::make('site_name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('brand_name')->required()->maxLength(255),
                    Forms\Components\TextInput::make('tagline')->maxLength(255),
                    Forms\Components\TextInput::make('email')->email()->maxLength(255),
                    Forms\Components\TextInput::make('phone')->maxLength(50),
                    Forms\Components\TextInput::make('location')->maxLength(255),
                ]),
                Schemas\Components\Tabs\Tab::make('Footer & Copyright')->schema([
                    Forms\Components\Textarea::make('footer_text')->rows(3),
                    Forms\Components\TextInput::make('copyright_text')->maxLength(255),
                ]),
                Schemas\Components\Tabs\Tab::make('CTA Defaults')->schema([
                    Forms\Components\TextInput::make('default_cta_text')->maxLength(255),
                    Forms\Components\TextInput::make('default_cta_url')->maxLength(255),
                ]),
                Schemas\Components\Tabs\Tab::make('Contact')->schema([
                    Forms\Components\TextInput::make('contact_email')->email()->maxLength(255),
                    Forms\Components\Toggle::make('contact_form_enabled')->label('Enable Contact Form'),
                    Forms\Components\TagsInput::make('budget_ranges')
                        ->placeholder('Add budget range'),
                ]),
                Schemas\Components\Tabs\Tab::make('Integrations')->schema([
                    Forms\Components\TextInput::make('analytics_id')
                        ->label('Google Analytics ID')
                        ->placeholder('G-XXXXXXXXXX')
                        ->maxLength(50),
                    Forms\Components\Textarea::make('verification_tags')
                        ->label('Verification Meta Tags')
                        ->rows(4)
                        ->helperText('Paste verification meta tags here'),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}

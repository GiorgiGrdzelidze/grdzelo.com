<?php

namespace App\Filament\Pages;

use App\Settings\SeoSettings;
use Filament\Forms;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Pages\SettingsPage;

class ManageSeoSettings extends SettingsPage
{
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string | \UnitEnum | null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'SEO Settings';

    protected static string $settings = SeoSettings::class;

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('SEO')->tabs([
                Schemas\Components\Tabs\Tab::make('Defaults')->schema([
                    Forms\Components\TextInput::make('default_title')->maxLength(255),
                    Forms\Components\TextInput::make('title_template')
                        ->maxLength(255)
                        ->helperText('Use %s as placeholder for page title'),
                    Forms\Components\Textarea::make('default_description')->maxLength(500)->rows(3),
                    Forms\Components\TextInput::make('canonical_base')
                        ->url()
                        ->maxLength(255)
                        ->placeholder('https://grdzelo.com'),
                    Forms\Components\TextInput::make('default_robots')
                        ->maxLength(255)
                        ->placeholder('index, follow'),
                ]),
                Schemas\Components\Tabs\Tab::make('Open Graph')->schema([
                    Forms\Components\TextInput::make('default_og_title')->maxLength(255),
                    Forms\Components\Textarea::make('default_og_description')->maxLength(500)->rows(2),
                    Forms\Components\FileUpload::make('default_og_image')
                        ->image()
                        ->directory('seo'),
                ]),
                Schemas\Components\Tabs\Tab::make('Twitter')->schema([
                    Forms\Components\TextInput::make('twitter_handle')
                        ->prefix('@')
                        ->maxLength(50),
                    Forms\Components\TextInput::make('default_twitter_title')->maxLength(255),
                    Forms\Components\Textarea::make('default_twitter_description')->maxLength(500)->rows(2),
                    Forms\Components\FileUpload::make('default_twitter_image')
                        ->image()
                        ->directory('seo'),
                ]),
                Schemas\Components\Tabs\Tab::make('Schema / Structured Data')->schema([
                    Forms\Components\KeyValue::make('schema_person')
                        ->label('Person Schema')
                        ->helperText('Key-value pairs for Person structured data'),
                    Forms\Components\TagsInput::make('social_profiles')
                        ->label('Social Profile URLs')
                        ->placeholder('Add URL'),
                ]),
                Schemas\Components\Tabs\Tab::make('Indexing')->schema([
                    Forms\Components\Toggle::make('indexing_enabled')
                        ->label('Allow Search Engine Indexing')
                        ->helperText('Disable to add noindex to all pages'),
                    Forms\Components\Toggle::make('sitemap_enabled')
                        ->label('Enable XML Sitemap'),
                ]),
            ])->columnSpanFull(),
        ]);
    }
}

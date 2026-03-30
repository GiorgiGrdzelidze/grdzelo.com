<?php

namespace App\Filament\Resources\IncomeSourceResource\RelationManagers;

use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EntriesRelationManager extends RelationManager
{
    protected static string $relationship = 'entries';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\DatePicker::make('date')->required(),
            Forms\Components\TextInput::make('amount')->numeric()->prefix('₾')->required(),
            Forms\Components\TextInput::make('currency')->default('GEL')->maxLength(10),
            Forms\Components\Toggle::make('is_received')->label('Received')->default(false),
            Forms\Components\DateTimePicker::make('received_at')->visible(fn (Schemas\Components\Utilities\Get $get) => $get('is_received')),
            Forms\Components\Textarea::make('note')->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('date')->date()->sortable(),
                Tables\Columns\TextColumn::make('amount')->money('GEL')->sortable(),
                Tables\Columns\IconColumn::make('is_received')->boolean()->label('Received'),
                Tables\Columns\TextColumn::make('received_at')->dateTime()->toggleable(),
                Tables\Columns\TextColumn::make('note')->limit(30)->toggleable(),
            ])
            ->defaultSort('date', 'desc')
            ->headerActions([Actions\CreateAction::make()])
            ->recordActions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->toolbarActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }
}

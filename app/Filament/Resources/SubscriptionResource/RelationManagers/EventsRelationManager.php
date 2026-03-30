<?php

namespace App\Filament\Resources\SubscriptionResource\RelationManagers;

use App\Enums\SubscriptionEventType;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class EventsRelationManager extends RelationManager
{
    protected static string $relationship = 'events';

    protected static ?string $title = 'Lifecycle History';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('event_type')
                ->options(SubscriptionEventType::options())
                ->required(),
            Forms\Components\DateTimePicker::make('occurred_at')->required()->default(now()),
            Schemas\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('old_amount')->numeric(),
                Forms\Components\TextInput::make('new_amount')->numeric(),
            ]),
            Forms\Components\Textarea::make('note')->rows(2),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('event_type')
                    ->badge()
                    ->formatStateUsing(fn (SubscriptionEventType $state) => $state->label())
                    ->color(fn (SubscriptionEventType $state) => match ($state) {
                        SubscriptionEventType::Started, SubscriptionEventType::Resumed => 'success',
                        SubscriptionEventType::Paused => 'warning',
                        SubscriptionEventType::Cancelled, SubscriptionEventType::Expired => 'danger',
                        SubscriptionEventType::PriceChanged, SubscriptionEventType::PlanChanged => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('occurred_at')->dateTime('M j, Y H:i')->sortable(),
                Tables\Columns\TextColumn::make('old_amount')->money('GEL')->toggleable(),
                Tables\Columns\TextColumn::make('new_amount')->money('GEL')->toggleable(),
                Tables\Columns\TextColumn::make('note')->limit(50)->toggleable(),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->headerActions([Actions\CreateAction::make()])
            ->actions([Actions\EditAction::make(), Actions\DeleteAction::make()])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }
}

<?php

namespace App\Filament\Resources;

use App\Enums\BillingInterval;
use App\Enums\Currency;
use App\Enums\SubscriptionStatus;
use App\Filament\Resources\SubscriptionResource\Pages;
use App\Filament\Resources\SubscriptionResource\RelationManagers;
use App\Models\Subscription;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Tabs::make('Subscription')->tabs([
                Schemas\Components\Tabs\Tab::make('Details')->schema([
                    Forms\Components\TextInput::make('title')->required()->maxLength(255),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('provider')->maxLength(255),
                        Forms\Components\TextInput::make('category')->maxLength(255),
                    ]),
                    Schemas\Components\Grid::make(3)->schema([
                        Forms\Components\TextInput::make('amount')->numeric()->required(),
                        Forms\Components\Select::make('currency')
                            ->options(Currency::options())
                            ->default('GEL')
                            ->required(),
                        Forms\Components\Select::make('billing_interval')
                            ->options(BillingInterval::options())
                            ->default('monthly')
                            ->required(),
                    ]),
                    Schemas\Components\Grid::make(3)->schema([
                        Forms\Components\DatePicker::make('start_date')->required(),
                        Forms\Components\DatePicker::make('next_billing_date'),
                        Forms\Components\TextInput::make('renewal_day')
                            ->numeric()->minValue(1)->maxValue(31)
                            ->label('Renewal Day of Month'),
                    ]),
                    Schemas\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('status')
                            ->options(SubscriptionStatus::options())
                            ->default('active')
                            ->required(),
                        Forms\Components\Toggle::make('auto_renew')->label('Auto-Renew')->default(true),
                    ]),
                    Forms\Components\Select::make('expense_category_id')
                        ->relationship('expenseCategory', 'title')
                        ->searchable()
                        ->preload(),
                    Forms\Components\TextInput::make('website_url')->url()->maxLength(255)->label('Website / Login URL'),
                    Forms\Components\Textarea::make('notes')->rows(3),
                ]),
                Schemas\Components\Tabs\Tab::make('Reminders')->schema([
                    Forms\Components\Toggle::make('reminders_enabled')->label('Enable Reminders')->default(true),
                    Forms\Components\TextInput::make('reminder_days_before')
                        ->numeric()->default(3)->minValue(0)->maxValue(30)
                        ->label('Remind X Days Before Billing')
                        ->visible(fn (Schemas\Components\Utilities\Get $get) => $get('reminders_enabled')),
                ]),
                Schemas\Components\Tabs\Tab::make('Lifecycle')->schema([
                    Forms\Components\Placeholder::make('cancelled_at_display')
                        ->label('Cancelled At')
                        ->content(fn (?Subscription $record) => $record?->cancelled_at?->format('M j, Y H:i') ?? '—'),
                    Forms\Components\Placeholder::make('paused_at_display')
                        ->label('Paused At')
                        ->content(fn (?Subscription $record) => $record?->paused_at?->format('M j, Y H:i') ?? '—'),
                    Forms\Components\Placeholder::make('resumed_at_display')
                        ->label('Resumed At')
                        ->content(fn (?Subscription $record) => $record?->resumed_at?->format('M j, Y H:i') ?? '—'),
                    Forms\Components\Placeholder::make('ended_at_display')
                        ->label('Ended At')
                        ->content(fn (?Subscription $record) => $record?->ended_at?->format('M j, Y') ?? '—'),
                    Forms\Components\Placeholder::make('monthly_cost')
                        ->label('Monthly Cost')
                        ->content(fn (?Subscription $record) => $record ? ($record->currency->value.' '.number_format($record->monthly_amount, 2)) : '—'),
                    Forms\Components\Placeholder::make('yearly_cost')
                        ->label('Yearly Cost')
                        ->content(fn (?Subscription $record) => $record ? ($record->currency->value.' '.number_format($record->yearly_amount, 2)) : '—'),
                ]),
            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('provider')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('amount')
                    ->formatStateUsing(fn (Subscription $record) => $record->currency->symbol().number_format((float) $record->amount, 2))
                    ->sortable(),
                Tables\Columns\TextColumn::make('billing_interval')
                    ->badge()
                    ->formatStateUsing(fn (BillingInterval $state) => $state->label()),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (SubscriptionStatus $state) => $state->color()),
                Tables\Columns\TextColumn::make('next_billing_date')->date()->sortable(),
                Tables\Columns\IconColumn::make('auto_renew')->boolean()->label('Auto'),
            ])
            ->defaultSort('next_billing_date')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options(SubscriptionStatus::options()),
                Tables\Filters\SelectFilter::make('billing_interval')
                    ->options(BillingInterval::options()),
                Tables\Filters\SelectFilter::make('currency')
                    ->options(Currency::options()),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\Action::make('pause')
                    ->icon('heroicon-o-pause')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record) => $record->status === SubscriptionStatus::Active)
                    ->action(fn (Subscription $record) => $record->pause()),
                Actions\Action::make('resume')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record) => $record->status === SubscriptionStatus::Paused)
                    ->action(fn (Subscription $record) => $record->resume()),
                Actions\Action::make('cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record) => in_array($record->status, [SubscriptionStatus::Active, SubscriptionStatus::Paused]))
                    ->action(fn (Subscription $record) => $record->cancel()),
            ])
            ->bulkActions([Actions\BulkActionGroup::make([Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\EventsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}

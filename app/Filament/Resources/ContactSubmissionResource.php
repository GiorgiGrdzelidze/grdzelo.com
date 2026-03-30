<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactSubmissionResource\Pages;
use App\Models\ContactSubmission;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-inbox';

    protected static string|\UnitEnum|null $navigationGroup = 'Leads';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Inquiries';

    public static function getNavigationBadge(): ?string
    {
        return (string) ContactSubmission::where('status', 'new')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Schemas\Components\Section::make('Contact Details')->schema([
                Forms\Components\TextInput::make('name')->disabled(),
                Forms\Components\TextInput::make('email')->disabled(),
                Forms\Components\TextInput::make('company')->disabled(),
                Forms\Components\TextInput::make('subject')->disabled(),
                Forms\Components\TextInput::make('budget_range')->disabled(),
                Forms\Components\TextInput::make('project_type')->disabled(),
                Forms\Components\Textarea::make('message')->disabled()->rows(6),
                Forms\Components\TextInput::make('source')->disabled(),
            ]),
            Schemas\Components\Section::make('Internal')->schema([
                Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                        'replied' => 'Replied',
                        'archived' => 'Archived',
                        'spam' => 'Spam',
                    ])
                    ->required(),
                Forms\Components\Textarea::make('internal_notes')->rows(4),
                Forms\Components\DateTimePicker::make('replied_at')->label('Replied At'),
            ]),
            Schemas\Components\Section::make('Metadata')->schema([
                Forms\Components\TextInput::make('ip_address')->disabled(),
                Forms\Components\TextInput::make('user_agent')->disabled(),
                Forms\Components\DateTimePicker::make('created_at')->disabled()->label('Submitted At'),
            ])->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('company')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('subject')->limit(40)->toggleable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'new',
                        'info' => 'read',
                        'success' => 'replied',
                        'gray' => 'archived',
                        'danger' => 'spam',
                    ]),
                Tables\Columns\TextColumn::make('budget_range')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->label('Submitted'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'read' => 'Read',
                        'replied' => 'Replied',
                        'archived' => 'Archived',
                        'spam' => 'Spam',
                    ]),
            ])
            ->actions([
                Actions\EditAction::make(),
                Actions\Action::make('markRead')
                    ->label('Mark Read')
                    ->icon('heroicon-o-eye')
                    ->action(fn (ContactSubmission $record) => $record->markAsRead())
                    ->visible(fn (ContactSubmission $record) => $record->status === 'new')
                    ->requiresConfirmation(false),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                    Actions\BulkAction::make('markSpam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-o-no-symbol')
                        ->action(fn ($records) => $records->each->markAsSpam())
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactSubmissions::route('/'),
            'edit' => Pages\EditContactSubmission::route('/{record}/edit'),
        ];
    }
}

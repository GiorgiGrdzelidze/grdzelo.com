<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentLeadsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Contact Submissions';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContactSubmission::query()
                    ->latest()
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('subject')->limit(40),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'new',
                        'info' => 'read',
                        'success' => 'replied',
                        'danger' => 'archived',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Actions\Action::make('view')
                    ->url(fn (ContactSubmission $record) => route('filament.admin.resources.contact-submissions.edit', $record))
                    ->icon('heroicon-o-eye'),
            ]);
    }
}

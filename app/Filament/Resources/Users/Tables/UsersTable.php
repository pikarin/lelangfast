<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                IconColumn::make('is_admin')
                    ->boolean()
                    ->label('Admin'),

                TextColumn::make('disabled_at')
                    ->label('Status')
                    ->formatStateUsing(fn (?string $state) => $state ? 'Disabled' : 'Active')
                    ->badge()
                    ->color(fn (?string $state) => $state ? 'danger' : 'success'),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                ViewAction::make(),

                Action::make('toggleDisable')
                    ->label(fn (User $record) => $record->disabled_at ? 'Enable' : 'Disable')
                    ->icon(fn (User $record) => $record->disabled_at ? 'heroicon-o-check-circle' : 'heroicon-o-no-symbol')
                    ->color(fn (User $record) => $record->disabled_at ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->action(function (User $record) {
                        $record->update([
                            'disabled_at' => $record->disabled_at ? null : now(),
                        ]);
                    }),
            ]);
    }
}

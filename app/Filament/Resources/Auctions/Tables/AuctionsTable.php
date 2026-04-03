<?php

namespace App\Filament\Resources\Auctions\Tables;

use App\Enums\AuctionStatus;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class AuctionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('starting_bid')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),

                TextColumn::make('current_high_bid')
                    ->numeric()
                    ->prefix('Rp ')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('start_time')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('end_time')
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(AuctionStatus::class),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
}

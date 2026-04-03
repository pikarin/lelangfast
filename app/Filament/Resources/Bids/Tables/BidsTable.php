<?php

namespace App\Filament\Resources\Bids\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BidsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('auction.title')
                    ->label('Auction')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Bidder')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('amount')
                    ->numeric()
                    ->prefix('Rp ')
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Placed At')
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('auction')
                    ->relationship('auction', 'title')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->label('Bidder')
                    ->searchable()
                    ->preload(),
            ]);
    }
}

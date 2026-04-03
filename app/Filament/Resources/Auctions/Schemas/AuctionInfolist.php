<?php

namespace App\Filament\Resources\Auctions\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class AuctionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('title'),

                TextEntry::make('status')
                    ->badge(),

                TextEntry::make('description')
                    ->html()
                    ->columnSpanFull(),

                TextEntry::make('starting_bid')
                    ->numeric()
                    ->prefix('Rp '),

                TextEntry::make('min_increment')
                    ->numeric()
                    ->prefix('Rp '),

                TextEntry::make('current_high_bid')
                    ->numeric()
                    ->prefix('Rp ')
                    ->default('-'),

                TextEntry::make('highestBidder.name')
                    ->label('Highest Bidder')
                    ->default('-'),

                TextEntry::make('start_time')
                    ->dateTime(),

                TextEntry::make('end_time')
                    ->dateTime(),

                TextEntry::make('creator.name')
                    ->label('Created By'),

                TextEntry::make('categories.name')
                    ->badge(),

                ImageEntry::make('images.path')
                    ->label('Images')
                    ->disk('public')
                    ->columnSpanFull(),
            ]);
    }
}

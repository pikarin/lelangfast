<?php

namespace App\Filament\Resources\Auctions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AuctionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),

                RichEditor::make('description')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('starting_bid')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->prefix('Rp'),

                TextInput::make('min_increment')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->prefix('Rp'),

                DateTimePicker::make('start_time')
                    ->required()
                    ->native(false),

                DateTimePicker::make('end_time')
                    ->required()
                    ->native(false)
                    ->after('start_time'),

                Select::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                FileUpload::make('images')
                    ->multiple()
                    ->image()
                    ->maxFiles(10)
                    ->maxSize(2048)
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->disk('public')
                    ->directory('auction-images')
                    ->reorderable()
                    ->columnSpanFull(),
            ]);
    }
}

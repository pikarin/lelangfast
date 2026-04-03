<?php

namespace App\Filament\Resources\Auctions\Pages;

use App\Enums\AuctionStatus;
use App\Filament\Resources\Auctions\AuctionResource;
use App\Models\AuctionImage;
use Filament\Resources\Pages\CreateRecord;

class CreateAuction extends CreateRecord
{
    protected static string $resource = AuctionResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = AuctionStatus::Upcoming;
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function afterCreate(): void
    {
        $imagePaths = $this->data['images'] ?? [];

        foreach ($imagePaths as $order => $path) {
            AuctionImage::create([
                'auction_id' => $this->record->id,
                'path' => $path,
                'display_order' => $order,
            ]);
        }
    }
}

<?php

namespace App\Filament\Resources\Auctions\Pages;

use App\Enums\AuctionStatus;
use App\Filament\Resources\Auctions\AuctionResource;
use App\Models\AuctionImage;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditAuction extends EditRecord
{
    protected static string $resource = AuctionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),

            Action::make('cancel')
                ->label('Cancel Auction')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->requiresConfirmation()
                ->modalHeading('Cancel Auction')
                ->modalDescription('Are you sure you want to cancel this auction? This action cannot be undone.')
                ->action(function () {
                    $this->record->update(['status' => AuctionStatus::Cancelled]);
                    $this->refreshFormData(['status']);
                })
                ->visible(fn () => in_array($this->record->status, [
                    AuctionStatus::Upcoming,
                    AuctionStatus::Active,
                ])),
        ];
    }

    protected function isFormDisabled(): bool
    {
        return $this->record->status !== AuctionStatus::Upcoming;
    }

    /**
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['images'] = $this->record->images->pluck('path')->toArray();

        return $data;
    }

    protected function afterSave(): void
    {
        $imagePaths = $this->data['images'] ?? [];

        $this->record->images()->delete();

        foreach ($imagePaths as $order => $path) {
            AuctionImage::create([
                'auction_id' => $this->record->id,
                'path' => $path,
                'display_order' => $order,
            ]);
        }
    }
}

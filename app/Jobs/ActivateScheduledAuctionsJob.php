<?php

namespace App\Jobs;

use App\Enums\AuctionStatus;
use App\Models\Auction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ActivateScheduledAuctionsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Auction::where('status', AuctionStatus::Upcoming)
            ->where('start_time', '<=', now())
            ->update(['status' => AuctionStatus::Active]);
    }
}

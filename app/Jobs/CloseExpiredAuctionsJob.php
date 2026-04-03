<?php

namespace App\Jobs;

use App\Enums\AuctionStatus;
use App\Models\Auction;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CloseExpiredAuctionsJob implements ShouldQueue
{
    use Queueable;

    public function handle(): void
    {
        Auction::where('status', AuctionStatus::Active)
            ->where('end_time', '<=', now())
            ->update(['status' => AuctionStatus::Ended]);
    }
}

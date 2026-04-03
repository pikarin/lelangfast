<?php

namespace App\Models;

use Database\Factories\AuctionImageFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['auction_id', 'path', 'display_order'])]
class AuctionImage extends Model
{
    /** @use HasFactory<AuctionImageFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Auction, $this>
     */
    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }
}

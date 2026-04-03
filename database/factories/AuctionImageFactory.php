<?php

namespace Database\Factories;

use App\Models\Auction;
use App\Models\AuctionImage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AuctionImage>
 */
class AuctionImageFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'auction_id' => Auction::factory(),
            'path' => 'auction-images/' . fake()->uuid() . '.jpg',
            'display_order' => fake()->numberBetween(0, 9),
        ];
    }
}

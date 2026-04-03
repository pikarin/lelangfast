<?php

namespace Database\Factories;

use App\Enums\AuctionStatus;
use App\Models\Auction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Auction>
 */
class AuctionFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('+1 hour', '+7 days');
        $endTime = fake()->dateTimeBetween($startTime, '+14 days');

        return [
            'title' => fake()->sentence(4),
            'description' => fake()->paragraphs(3, true),
            'starting_bid' => fake()->numberBetween(10_000, 1_000_000) * 100,
            'min_increment' => fake()->randomElement([10_000, 25_000, 50_000, 100_000]),
            'current_high_bid' => null,
            'highest_bidder_id' => null,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => AuctionStatus::Upcoming,
            'created_by' => User::factory(),
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => AuctionStatus::Active,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(6),
        ]);
    }

    public function ended(): static
    {
        return $this->state(fn () => [
            'status' => AuctionStatus::Ended,
            'start_time' => now()->subDays(2),
            'end_time' => now()->subHour(),
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn () => [
            'status' => AuctionStatus::Cancelled,
            'start_time' => now()->subHour(),
            'end_time' => now()->addHours(6),
        ]);
    }
}

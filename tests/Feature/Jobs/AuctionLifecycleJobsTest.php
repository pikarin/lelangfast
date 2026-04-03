<?php

use App\Enums\AuctionStatus;
use App\Jobs\ActivateScheduledAuctionsJob;
use App\Jobs\CloseExpiredAuctionsJob;
use App\Models\Auction;
use App\Models\User;

it('activates upcoming auctions when start time is reached', function () {
    $admin = User::factory()->admin()->create();

    $shouldActivate = Auction::factory()->create([
        'created_by' => $admin->id,
        'status' => AuctionStatus::Upcoming,
        'start_time' => now()->subMinute(),
        'end_time' => now()->addHours(6),
    ]);

    $shouldNotActivate = Auction::factory()->create([
        'created_by' => $admin->id,
        'status' => AuctionStatus::Upcoming,
        'start_time' => now()->addHour(),
        'end_time' => now()->addHours(6),
    ]);

    (new ActivateScheduledAuctionsJob)->handle();

    expect($shouldActivate->fresh()->status)->toBe(AuctionStatus::Active);
    expect($shouldNotActivate->fresh()->status)->toBe(AuctionStatus::Upcoming);
});

it('closes active auctions when end time is passed', function () {
    $admin = User::factory()->admin()->create();

    $shouldClose = Auction::factory()->active()->create([
        'created_by' => $admin->id,
        'end_time' => now()->subMinute(),
    ]);

    $shouldNotClose = Auction::factory()->active()->create([
        'created_by' => $admin->id,
        'end_time' => now()->addHour(),
    ]);

    (new CloseExpiredAuctionsJob)->handle();

    expect($shouldClose->fresh()->status)->toBe(AuctionStatus::Ended);
    expect($shouldNotClose->fresh()->status)->toBe(AuctionStatus::Active);
});

it('does not affect cancelled auctions', function () {
    $admin = User::factory()->admin()->create();

    $cancelled = Auction::factory()->cancelled()->create([
        'created_by' => $admin->id,
        'start_time' => now()->subHour(),
        'end_time' => now()->subMinute(),
    ]);

    (new ActivateScheduledAuctionsJob)->handle();
    (new CloseExpiredAuctionsJob)->handle();

    expect($cancelled->fresh()->status)->toBe(AuctionStatus::Cancelled);
});

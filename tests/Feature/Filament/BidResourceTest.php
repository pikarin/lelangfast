<?php

use App\Filament\Resources\Bids\Pages\ListBids;
use App\Models\Bid;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

it('can list bids', function () {
    $bids = Bid::factory(3)->create();

    livewire(ListBids::class)
        ->assertCanSeeTableRecords($bids);
});

it('displays auction and bidder information', function () {
    $bid = Bid::factory()->create();

    livewire(ListBids::class)
        ->assertCanSeeTableRecords([$bid]);
});

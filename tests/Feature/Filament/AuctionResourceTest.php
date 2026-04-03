<?php

use App\Enums\AuctionStatus;
use App\Filament\Resources\Auctions\Pages\CreateAuction;
use App\Filament\Resources\Auctions\Pages\EditAuction;
use App\Filament\Resources\Auctions\Pages\ListAuctions;
use App\Filament\Resources\Auctions\RelationManagers\BidsRelationManager;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\Category;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

it('can list auctions', function () {
    $auctions = Auction::factory(3)->create(['created_by' => $this->admin->id]);

    livewire(ListAuctions::class)
        ->assertCanSeeTableRecords($auctions);
});

it('can create an auction', function () {
    $categories = Category::factory(2)->create();

    livewire(CreateAuction::class)
        ->set('data.title', 'Vintage Watch')
        ->set('data.description', '<p>A beautiful vintage watch.</p>')
        ->set('data.starting_bid', 500_000)
        ->set('data.min_increment', 50_000)
        ->set('data.start_time', now()->addDay()->format('Y-m-d H:i:s'))
        ->set('data.end_time', now()->addDays(3)->format('Y-m-d H:i:s'))
        ->set('data.categories', $categories->pluck('id')->toArray())
        ->call('create')
        ->assertHasNoFormErrors();

    $auction = Auction::latest()->first();

    expect($auction)
        ->title->toBe('Vintage Watch')
        ->status->toBe(AuctionStatus::Upcoming)
        ->created_by->toBe($this->admin->id)
        ->starting_bid->toBe(500_000)
        ->min_increment->toBe(50_000);

    expect($auction->categories)->toHaveCount(2);
});

it('can cancel an active auction', function () {
    $auction = Auction::factory()->active()->create(['created_by' => $this->admin->id]);

    livewire(EditAuction::class, ['record' => $auction->getRouteKey()])
        ->callAction('cancel');

    expect($auction->fresh()->status)->toBe(AuctionStatus::Cancelled);
});

it('cannot cancel an ended auction', function () {
    $auction = Auction::factory()->ended()->create(['created_by' => $this->admin->id]);

    livewire(EditAuction::class, ['record' => $auction->getRouteKey()])
        ->assertActionHidden('cancel');
});

it('shows bid relation manager on edit page', function () {
    $auction = Auction::factory()->active()->create(['created_by' => $this->admin->id]);
    $bids = Bid::factory(3)->create(['auction_id' => $auction->id]);

    livewire(BidsRelationManager::class, [
        'ownerRecord' => $auction,
        'pageClass' => EditAuction::class,
    ])
        ->assertCanSeeTableRecords($bids);
});

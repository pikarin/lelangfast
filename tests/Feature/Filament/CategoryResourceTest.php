<?php

use App\Filament\Resources\Categories\Pages\CreateCategory;
use App\Filament\Resources\Categories\Pages\EditCategory;
use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Auction;
use App\Models\Category;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

it('can list categories', function () {
    $categories = Category::factory(3)->create();

    livewire(ListCategories::class)
        ->assertCanSeeTableRecords($categories);
});

it('can create a category', function () {
    livewire(CreateCategory::class)
        ->set('data.name', 'Electronics')
        ->set('data.slug', 'electronics')
        ->call('create')
        ->assertHasNoFormErrors();

    expect(Category::where('slug', 'electronics')->exists())->toBeTrue();
});

it('can edit a category', function () {
    $category = Category::factory()->create();

    livewire(EditCategory::class, ['record' => $category->getRouteKey()])
        ->set('data.name', 'Updated Name')
        ->set('data.slug', 'updated-name')
        ->call('save')
        ->assertHasNoFormErrors();

    expect($category->fresh()->name)->toBe('Updated Name');
});

it('can delete a category and removes pivot rows', function () {
    $category = Category::factory()->create();
    $auction = Auction::factory()->create(['created_by' => $this->admin->id]);
    $auction->categories()->attach($category);

    livewire(ListCategories::class)
        ->callTableAction('delete', $category);

    expect(Category::find($category->id))->toBeNull();
    expect($auction->fresh())->not->toBeNull();
    expect($auction->categories)->toHaveCount(0);
});

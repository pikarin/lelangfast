<?php

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\User;

use function Pest\Livewire\livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->actingAs($this->admin);
});

it('can list users', function () {
    $users = User::factory(3)->create();

    livewire(ListUsers::class)
        ->assertCanSeeTableRecords($users);
});

it('can disable a user', function () {
    $user = User::factory()->create();

    expect($user->disabled_at)->toBeNull();

    livewire(ListUsers::class)
        ->callTableAction('toggleDisable', $user);

    expect($user->fresh()->disabled_at)->not->toBeNull();
});

it('can re-enable a disabled user', function () {
    $user = User::factory()->disabled()->create();

    expect($user->disabled_at)->not->toBeNull();

    livewire(ListUsers::class)
        ->callTableAction('toggleDisable', $user);

    expect($user->fresh()->disabled_at)->toBeNull();
});

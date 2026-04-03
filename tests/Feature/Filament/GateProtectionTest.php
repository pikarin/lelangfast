<?php

use App\Models\User;

it('non-admin cannot access filament panel', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

it('admin can access filament panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk();
});

it('guest is redirected to login', function () {
    $this->get('/admin')
        ->assertRedirect();
});

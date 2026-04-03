<?php

namespace Database\Seeders;

use App\Models\Auction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@lelangfast.test',
        ]);

        User::factory(10)->create();

        $categories = collect([
            'Electronics',
            'Collectibles',
            'Vehicles',
            'Art',
            'Jewelry',
        ])->map(fn (string $name) => Category::factory()->create(['name' => $name, 'slug' => str($name)->slug()->toString()]));

        Auction::factory(3)->create([
            'created_by' => $admin->id,
        ])->each(fn (Auction $auction) => $auction->categories()->attach(
            $categories->random(rand(1, 3))->pluck('id'),
        ));

        Auction::factory(2)->active()->create([
            'created_by' => $admin->id,
        ])->each(fn (Auction $auction) => $auction->categories()->attach(
            $categories->random(rand(1, 2))->pluck('id'),
        ));

        Auction::factory(2)->ended()->create([
            'created_by' => $admin->id,
        ])->each(fn (Auction $auction) => $auction->categories()->attach(
            $categories->random(rand(1, 2))->pluck('id'),
        ));

        Auction::factory()->cancelled()->create([
            'created_by' => $admin->id,
        ]);
    }
}

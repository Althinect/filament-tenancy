<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Organization::factory()
            ->count(10)
            ->has(
                User::factory()->count(10))
            ->createQuietly();

        Organization::query()
            ->each(function (Organization $organization) {
                $organization->users()->attach(User::find(1));
            });
    }
}

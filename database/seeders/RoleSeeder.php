<?php

namespace Database\Seeders;

use App\Models\Organization;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Organization::query()->each(function (Organization $organization) {
            $organization->roles()->create([
                'name' => 'Admin',
                'guard_name' => 'web',
            ]);

            $organization->roles()->create([
                'name' => 'Lawyer',
                'guard_name' => 'web',
            ]);

            $organization->roles()->create([
                'name' => 'Paralegal',
                'guard_name' => 'web',
            ]);

            $organization->roles()->create([
                'name' => 'Client',
                'guard_name' => 'web',
            ]);
        });

    }
}

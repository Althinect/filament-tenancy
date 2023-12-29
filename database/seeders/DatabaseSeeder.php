<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        \App\Models\User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
        ]);

        $this->call(PermissionSeeder::class);
        $this->call(TenantSeeder::class);
        $this->call(RoleSeeder::class);
    }
}

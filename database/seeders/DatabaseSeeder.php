<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PostSeeder::class,
        ]);

        // Create a test admin user
        User::factory()->create([
            'name' => 'Test Admin',
            'email' => 'admin@engineerhub.com',
        ]);
    }
}

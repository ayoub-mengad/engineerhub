<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test engineers
        $engineers = [
            [
                'name' => 'Alice Johnson',
                'email' => 'alice@engineerhub.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Bob Smith',
                'email' => 'bob@engineerhub.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Carol Davis',
                'email' => 'carol@engineerhub.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'David Wilson',
                'email' => 'david@engineerhub.com',
                'password' => Hash::make('password'),
            ],
            [
                'name' => 'Emma Thompson',
                'email' => 'emma@engineerhub.com',
                'password' => Hash::make('password'),
            ],
        ];

        foreach ($engineers as $engineer) {
            User::create($engineer);
        }
    }
}

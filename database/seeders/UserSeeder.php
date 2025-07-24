<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test users for authentication testing
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Artist User',
            'email' => 'artist@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'bio' => 'Digital artist and creator',
            'location' => 'Tbilisi, Georgia',
            'website' => 'https://artist-portfolio.com',
        ]);

        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'email_verified_at' => now(),
            'bio' => 'Platform administrator',
        ]);

        $this->command->info('Created 3 test users:');
        $this->command->info('- test@example.com (password: password123)');
        $this->command->info('- artist@example.com (password: password123)');
        $this->command->info('- admin@example.com (password: password123)');
    }
}

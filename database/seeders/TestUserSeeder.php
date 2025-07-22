<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\LinkedAccount;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create test user with password
        $testUser = User::updateOrCreate([
            'email' => 'test@acumencraft.com'
        ], [
            'name' => 'Test User',
            'email' => 'test@acumencraft.com',
            'password' => 'password', // This will be hashed automatically
            'email_verified_at' => now(),
            'role' => 'user',
            'status' => 'active',
        ]);

        // Create OAuth test user (simulating Google OAuth)
        $oauthUser = User::updateOrCreate([
            'email' => 'oauth@acumencraft.com'
        ], [
            'name' => 'OAuth Test User',
            'email' => 'oauth@acumencraft.com',
            'provider' => 'google',
            'provider_id' => 'test_google_123',
            'oauth_avatar' => 'https://lh3.googleusercontent.com/a/default-user=s96-c',
            'oauth_email_verified' => true,
            'email_verified_at' => now(),
            'password' => null, // OAuth users don't have passwords initially
            'role' => 'user',
            'status' => 'active',
        ]);

        // Create linked account for OAuth user
        LinkedAccount::updateOrCreate([
            'user_id' => $oauthUser->id,
            'provider' => 'google'
        ], [
            'user_id' => $oauthUser->id,
            'provider' => 'google',
            'provider_id' => 'test_google_123',
            'email' => 'oauth@acumencraft.com',
            'avatar_url' => 'https://lh3.googleusercontent.com/a/default-user=s96-c',
        ]);

        $this->command->info('Test users created:');
        $this->command->info('1. test@acumencraft.com (password: password)');
        $this->command->info('2. oauth@acumencraft.com (OAuth user)');
    }
}

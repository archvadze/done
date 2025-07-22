<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // OAuth fields
            $table->string('provider', 32)->nullable()->after('password');
            $table->string('provider_id', 128)->nullable()->after('provider');
            $table->string('oauth_avatar', 512)->nullable()->after('provider_id');
            $table->boolean('oauth_email_verified')->default(false)->after('oauth_avatar');

            // 2FA
            $table->string('twofa_secret')->nullable()->after('oauth_email_verified');

            // Profile fields
            $table->string('avatar_path')->nullable()->after('twofa_secret');
            $table->text('bio')->nullable()->after('avatar_path');
            $table->string('creative_field', 128)->nullable()->after('bio');
            $table->string('lang', 8)->default('en')->after('creative_field');

            // Preferences (JSON)
            $table->json('notification_prefs')->nullable()->after('lang');
            $table->json('privacy_prefs')->nullable()->after('notification_prefs');

            // Role and status
            $table->enum('role', ['user', 'artist', 'moderator', 'admin'])->default('user')->after('privacy_prefs');
            $table->enum('status', ['active', 'suspended', 'deleted'])->default('active')->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'provider_id',
                'oauth_avatar',
                'oauth_email_verified',
                'twofa_secret',
                'avatar_path',
                'bio',
                'creative_field',
                'lang',
                'notification_prefs',
                'privacy_prefs',
                'role',
                'status'
            ]);
        });
    }
};

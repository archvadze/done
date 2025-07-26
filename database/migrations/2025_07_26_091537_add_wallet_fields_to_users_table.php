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
            $table->string('wallet_address')->nullable()->after('balance_currency');
            $table->enum('wallet_type', ['metamask', 'walletconnect', 'coinbase', 'mock'])->nullable()->after('wallet_address');
            $table->timestamp('wallet_connected_at')->nullable()->after('wallet_type');
            $table->json('wallet_metadata')->nullable()->after('wallet_connected_at'); // Additional wallet info
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wallet_address', 'wallet_type', 'wallet_connected_at', 'wallet_metadata']);
        });
    }
};

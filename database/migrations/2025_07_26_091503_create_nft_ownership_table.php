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
        Schema::create('nft_ownership', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artwork_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Original creator
            $table->string('owner_wallet')->nullable(); // Current NFT owner wallet address
            $table->enum('network', ['ethereum', 'polygon', 'bsc', 'mock'])->default('mock');
            $table->string('token_id')->nullable(); // NFT token ID on blockchain
            $table->string('contract_address')->nullable(); // Smart contract address
            $table->string('tx_hash')->nullable(); // Minting transaction hash
            $table->decimal('mint_price', 18, 8)->nullable(); // Price paid for minting in crypto
            $table->string('mint_currency', 10)->nullable(); // ETH, MATIC, BNB, etc.
            $table->json('metadata')->nullable(); // NFT metadata (name, description, attributes)
            $table->string('ipfs_hash')->nullable(); // IPFS hash for artwork file
            $table->enum('status', ['pending', 'minted', 'failed', 'burned'])->default('pending');
            $table->timestamp('minted_at')->nullable();
            $table->timestamps();
            
            $table->index(['artwork_id', 'status']);
            $table->index(['owner_wallet', 'network']);
            $table->index(['network', 'token_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nft_ownership');
    }
};

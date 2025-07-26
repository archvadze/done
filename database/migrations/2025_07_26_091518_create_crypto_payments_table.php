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
        Schema::create('crypto_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 18, 8); // Crypto amounts need higher precision
            $table->string('currency', 10); // ETH, BTC, USDT, MATIC, etc.
            $table->string('tx_hash')->nullable(); // Blockchain transaction hash
            $table->enum('status', ['pending', 'confirmed', 'failed', 'expired'])->default('pending');
            $table->enum('network', ['ethereum', 'polygon', 'bsc', 'bitcoin', 'mock'])->default('mock');
            $table->string('from_address')->nullable(); // Sender wallet address
            $table->string('to_address')->nullable(); // Receiver wallet address
            $table->integer('confirmations')->default(0); // Blockchain confirmations
            $table->json('metadata')->nullable(); // Additional payment data
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index(['tx_hash', 'network']);
            $table->index(['status', 'network']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crypto_payments');
    }
};

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
        Schema::create('linked_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('provider', 32);
            $table->string('provider_id', 128);
            $table->string('email')->nullable();
            $table->string('avatar_url', 512)->nullable();
            $table->timestamps();

            // Unique constraint per user/provider
            $table->unique(['user_id', 'provider']);

            // Index for fast lookups
            $table->index(['provider', 'provider_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('linked_accounts');
    }
};

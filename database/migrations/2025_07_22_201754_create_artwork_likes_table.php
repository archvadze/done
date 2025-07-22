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
        Schema::create('artwork_likes', function (Blueprint $table) {
            $table->id();

            // Relationships
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('artwork_id')->constrained()->onDelete('cascade');

            $table->timestamps();

            // Unique constraint to prevent duplicate likes
            $table->unique(['user_id', 'artwork_id']);

            // Indexes for performance
            $table->index('artwork_id'); // For counting likes per artwork
            $table->index('user_id'); // For finding user's liked artworks
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_likes');
    }
};

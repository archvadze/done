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
        Schema::create('artwork_categories', function (Blueprint $table) {
            $table->id();

            // Multilingual category information
            $table->json('name'); // {"en": "Digital Art", "ka": "ციფრული ხელოვნება"}
            $table->json('description')->nullable(); // Multilingual descriptions
            $table->string('slug')->unique(); // URL-friendly identifier
            $table->string('icon')->nullable(); // Icon class or SVG for UI
            $table->string('color_hex')->nullable(); // Theme color for category

            // Hierarchy support
            $table->foreignId('parent_id')->nullable()->constrained('artwork_categories')->onDelete('cascade');
            $table->integer('sort_order')->default(0); // For custom ordering

            // Media type restrictions (optional)
            $table->json('allowed_media_types')->nullable(); // ["image", "video"] - null means all allowed

            // Status
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false); // Show on homepage/featured sections

            // Statistics (cached for performance)
            $table->integer('artwork_count')->default(0);

            $table->timestamps();

            // Indexes
            $table->index(['parent_id', 'sort_order']);
            $table->index(['is_active', 'is_featured']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artwork_categories');
    }
};

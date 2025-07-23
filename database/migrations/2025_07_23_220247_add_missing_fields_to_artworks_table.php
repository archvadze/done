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
        Schema::table('artworks', function (Blueprint $table) {
            // Add missing fields that are used in Factory and Model
            $table->text('creative_process')->nullable()->after('tags');
            $table->decimal('acq_total_score', 5, 2)->nullable()->after('acq_breakdown');
            $table->boolean('featured')->default(false)->after('is_featured');
            $table->decimal('price', 8, 2)->nullable()->after('nft_token_id');
            $table->boolean('is_for_sale')->default(false)->after('price');
            $table->boolean('allow_downloads')->default(false)->after('downloads_enabled');
            $table->timestamp('featured_until')->nullable()->after('featured');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn([
                'creative_process',
                'acq_total_score',
                'featured',
                'price',
                'is_for_sale',
                'allow_downloads',
                'featured_until'
            ]);
        });
    }
};

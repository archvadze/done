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
        Schema::create('artworks', function (Blueprint $table) {
            $table->id();

            // User relationship
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Content fields (multilingual JSON support)
            $table->json('title'); // {"en": "My Artwork", "ka": "ჩემი ნამუშევარი"}
            $table->json('description')->nullable(); // Multilingual descriptions

            // Media and file information
            $table->string('media_type'); // image, audio, video, pdf, other
            $table->string('file_path'); // S3 path or local storage path
            $table->string('file_url'); // Public URL for access
            $table->string('thumbnail_path')->nullable(); // Generated thumbnail
            $table->string('original_filename'); // Original upload filename
            $table->string('file_hash'); // SHA-256 hash for integrity
            $table->bigInteger('file_size'); // Size in bytes
            $table->string('mime_type'); // MIME type of the file
            $table->json('file_metadata')->nullable(); // Width, height, duration, etc.

            // Copyright and licensing
            $table->enum('license_type', [
                'all_rights_reserved',
                'creative_commons_by',
                'creative_commons_by_sa',
                'creative_commons_by_nc',
                'creative_commons_by_nc_sa',
                'public_domain',
                'nft_exclusive'
            ])->default('all_rights_reserved');
            $table->text('copyright_notice')->nullable();
            $table->boolean('watermark_enabled')->default(true);
            $table->timestamp('blockchain_timestamp')->nullable(); // Optional blockchain verification
            $table->string('blockchain_hash')->nullable(); // Transaction hash if timestamped

            // Content classification
            $table->json('tags')->nullable(); // ["art", "digital", "painting"]
            $table->string('category')->nullable(); // painting, photography, music, etc.
            $table->string('subcategory')->nullable(); // portrait, landscape, etc.
            $table->boolean('is_ai_generated')->default(false);
            $table->json('ai_tools_used')->nullable(); // Tools used if AI-generated

            // Visibility and access
            $table->enum('visibility', ['public', 'private', 'unlisted'])->default('public');
            $table->boolean('comments_enabled')->default(true);
            $table->boolean('downloads_enabled')->default(false);
            $table->boolean('is_featured')->default(false); // Admin can feature artworks

            // Engagement metrics
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('download_count')->default(0);

            // ACQ (Acumen Craft Quotient) scores
            $table->decimal('acq_score', 5, 2)->nullable(); // Overall quality score
            $table->json('acq_breakdown')->nullable(); // Detailed scoring breakdown
            $table->integer('evaluation_count')->default(0);

            // Status and moderation
            $table->enum('status', ['draft', 'pending', 'published', 'rejected', 'archived'])->default('draft');
            $table->text('rejection_reason')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamp('archived_at')->nullable();

            // NFT related (for future NFT functionality)
            $table->boolean('is_nft')->default(false);
            $table->string('nft_contract_address')->nullable();
            $table->string('nft_token_id')->nullable();
            $table->string('blockchain_network')->nullable(); // ethereum, polygon, etc.

            $table->timestamps();

            // Indexes for performance
            $table->index(['user_id', 'status']);
            $table->index(['visibility', 'published_at']);
            $table->index(['category', 'subcategory']);
            $table->index(['is_featured', 'published_at']);
            $table->index('acq_score');
            $table->index('file_hash'); // For duplicate detection
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artworks');
    }
};

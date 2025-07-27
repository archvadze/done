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
        Schema::create('moderation_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reporter_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('reported_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('reportable_type'); // artwork, comment, community_post, etc.
            $table->unsignedBigInteger('reportable_id');
            $table->enum('reason', ['spam', 'harassment', 'copyright', 'inappropriate_content', 'fake_profile', 'other']);
            $table->text('description');
            $table->enum('status', ['pending', 'under_review', 'resolved', 'dismissed'])->default('pending');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            $table->json('evidence')->nullable(); // screenshots, links, etc.
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->json('resolution_notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['reportable_type', 'reportable_id']);
            $table->index(['status', 'priority', 'created_at']);
            $table->index(['assigned_to', 'status']);
            $table->index(['reporter_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_reports');
    }
};

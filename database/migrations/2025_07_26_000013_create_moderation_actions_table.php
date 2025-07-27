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
        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moderator_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('report_id')->nullable()->constrained('moderation_reports')->onDelete('cascade');
            $table->foreignId('target_user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('actionable_type')->nullable(); // artwork, comment, etc.
            $table->unsignedBigInteger('actionable_id')->nullable();
            $table->enum('action_type', ['warning', 'content_removal', 'temporary_ban', 'permanent_ban', 'copyright_strike', 'account_suspension', 'other']);
            $table->text('reason');
            $table->json('details')->nullable();
            $table->timestamp('expires_at')->nullable(); // for temporary actions
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index(['actionable_type', 'actionable_id']);
            $table->index(['target_user_id', 'action_type']);
            $table->index(['moderator_id', 'created_at']);
            $table->index(['is_active', 'expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moderation_actions');
    }
};

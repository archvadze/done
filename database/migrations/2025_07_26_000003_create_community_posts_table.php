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
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('community_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['discussion', 'announcement', 'question', 'showcase'])->default('discussion');
            $table->json('attachments')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_locked')->default(false);
            $table->integer('like_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->integer('view_count')->default(0);
            $table->timestamps();
            
            // Indexes
            $table->index(['community_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index(['type', 'is_pinned']);
            $table->index('like_count');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};

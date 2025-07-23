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
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            // Core relationships
            $table->foreignId('artwork_id')->constrained()->onDelete('cascade');
            $table->foreignId('evaluator_id')->nullable()->constrained('users')->onDelete('set null');

            // Scoring criteria (1-10 scale)
            $table->tinyInteger('score_technique')->nullable()->comment('Technical skill rating 1-10');
            $table->tinyInteger('score_composition')->nullable()->comment('Composition quality rating 1-10');
            $table->tinyInteger('score_originality')->nullable()->comment('Originality rating 1-10');
            $table->tinyInteger('score_impact')->nullable()->comment('Emotional/visual impact rating 1-10');

            // Overall computed score
            $table->decimal('overall_score', 4, 2)->nullable()->comment('Calculated average of all scores');

            // Feedback and metadata
            $table->text('feedback_text')->nullable()->comment('Written feedback from evaluator');
            $table->enum('source', ['human', 'ai', 'aggregate'])->default('human')->comment('Source of evaluation');
            $table->json('metadata')->nullable()->comment('Additional data like AI model version, etc.');

            // Status and moderation
            $table->enum('status', ['pending', 'approved', 'rejected', 'flagged'])->default('approved');
            $table->text('moderation_notes')->nullable();

            $table->timestamps();

            // Indexes for performance
            $table->index(['artwork_id', 'status']);
            $table->index(['evaluator_id', 'created_at']);
            $table->index(['source', 'status']);
            $table->index('overall_score');

            // Ensure evaluator can only evaluate artwork once
            $table->unique(['artwork_id', 'evaluator_id'], 'unique_artwork_evaluator');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};

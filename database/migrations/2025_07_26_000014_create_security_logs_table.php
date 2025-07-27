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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('event_type'); // login, logout, password_change, etc.
            $table->string('event_category')->default('security'); // security, moderation, system
            $table->text('description');
            $table->json('metadata')->nullable(); // IP, user agent, additional data
            $table->enum('severity', ['info', 'warning', 'error', 'critical'])->default('info');
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'created_at']);
            $table->index(['event_type', 'created_at']);
            $table->index(['event_category', 'severity']);
            $table->index(['ip_address', 'created_at']);
            $table->index('created_at'); // for cleanup/archival
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};

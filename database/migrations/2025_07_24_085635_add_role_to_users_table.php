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
        Schema::table('users', function (Blueprint $table) {
            // Check if columns don't already exist
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['artist', 'moderator', 'admin'])->default('artist')->after('email');
            }
            if (!Schema::hasColumn('users', 'can_evaluate')) {
                $table->boolean('can_evaluate')->default(false)->after('role');
            }
            if (!Schema::hasColumn('users', 'moderator_notes')) {
                $table->text('moderator_notes')->nullable()->after('can_evaluate');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'can_evaluate', 'moderator_notes']);
        });
    }
};

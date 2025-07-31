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
            // Check if columns don't already exist before adding
            if (!Schema::hasColumn('users', 'provider')) {
                $table->string('provider')->nullable()->after('password');
            }
            if (!Schema::hasColumn('users', 'provider_id')) {
                $table->string('provider_id')->nullable()->after('provider');
            }
            if (!Schema::hasColumn('users', 'avatar')) {
                $table->string('avatar')->nullable()->after('provider_id');
            }

            // Make password nullable for OAuth users (check if already nullable)
            $column = Schema::getConnection()->getDoctrineSchemaManager()
                ->listTableDetails('users')->getColumn('password');
            if (!$column->getNotnull()) {
                // Password is already nullable, skip this change
            } else {
                $table->string('password')->nullable()->change();
            }

            // Add indexes for OAuth lookups (if not exists)
            // Note: Laravel doesn't have hasIndex, so we'll wrap in try-catch
            try {
                $table->index(['provider', 'provider_id']);
            } catch (\Exception $e) {
                // Index might already exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['provider', 'provider_id']);
            $table->dropColumn(['provider', 'provider_id', 'avatar']);

            // Make password required again
            $table->string('password')->nullable(false)->change();
        });
    }
};

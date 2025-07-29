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
        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('artwork_id')->nullable()->after('user_id')->constrained()->onDelete('cascade');
            $table->index(['artwork_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropForeign(['artwork_id']);
            $table->dropIndex(['artwork_id', 'status']);
            $table->dropColumn('artwork_id');
        });
    }
};

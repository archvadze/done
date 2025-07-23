<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            // Add multilingual title and description fields
            $table->json('title_translations')->nullable()->after('title');
            $table->json('description_translations')->nullable()->after('description');

            // Add indexes for better performance
            $table->index('title_translations');
        });

        // Migrate existing data to multilingual format
        DB::table('artworks')->get()->each(function ($artwork) {
            $titleTranslations = [];
            $descriptionTranslations = [];

            // Handle existing title
            if ($artwork->title) {
                if (is_string($artwork->title)) {
                    $titleTranslations['en'] = $artwork->title;
                } else {
                    $existingTitle = json_decode($artwork->title, true);
                    if (is_array($existingTitle)) {
                        $titleTranslations = $existingTitle;
                    } else {
                        $titleTranslations['en'] = $artwork->title;
                    }
                }
            }

            // Handle existing description
            if ($artwork->description) {
                if (is_string($artwork->description)) {
                    $descriptionTranslations['en'] = $artwork->description;
                } else {
                    $existingDescription = json_decode($artwork->description, true);
                    if (is_array($existingDescription)) {
                        $descriptionTranslations = $existingDescription;
                    } else {
                        $descriptionTranslations['en'] = $artwork->description;
                    }
                }
            }

            // Update the record with translations
            DB::table('artworks')
                ->where('id', $artwork->id)
                ->update([
                    'title_translations' => json_encode($titleTranslations),
                    'description_translations' => json_encode($descriptionTranslations),
                ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artworks', function (Blueprint $table) {
            $table->dropColumn(['title_translations', 'description_translations']);
        });
    }
};

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Artwork;
use App\Models\User;

class ArtworkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get test users
        $testUser = User::where('email', 'test@example.com')->first();
        $artistUser = User::where('email', 'artist@example.com')->first();

        if (!$testUser || !$artistUser) {
            $this->command->error('Test users not found. Please run UserSeeder first.');
            return;
        }

        // Create sample artworks
        $artworks = [
            [
                'user_id' => $testUser->id,
                'title' => json_encode([
                    'en' => 'Digital Landscape',
                    'ka' => 'ციფრული პეიზაჟი'
                ]),
                'description' => json_encode([
                    'en' => 'A beautiful digital painting of a mountain landscape at sunset.',
                    'ka' => 'მთის პეიზაჟის ლამაზი ციფრული ნახატი მზის ჩასვლისას.'
                ]),
                'media_type' => 'image',
                'file_path' => 'artworks/sample-landscape.jpg',
                'file_url' => '/storage/artworks/sample-landscape.jpg',
                'thumbnail_path' => 'artworks/thumbnails/sample-landscape-thumb.jpg',
                'original_filename' => 'landscape.jpg',
                'file_hash' => 'sample_hash_1',
                'file_size' => 2048000,
                'mime_type' => 'image/jpeg',
                'file_metadata' => json_encode(['width' => 1920, 'height' => 1080]),
                'license_type' => 'creative_commons_by',
                'copyright_notice' => '© 2025 Test User',
                'watermark_enabled' => true,
                'tags' => json_encode(['landscape', 'digital', 'painting', 'sunset']),
                'category' => 'digital-art',
                'subcategory' => 'landscape',
                'is_ai_generated' => false,
                'visibility' => 'public',
                'comments_enabled' => true,
                'downloads_enabled' => false,
                'status' => 'published',
                'published_at' => now(),
                'view_count' => 127,
                'like_count' => 23,
                'comment_count' => 5,
            ],
            [
                'user_id' => $artistUser->id,
                'title' => json_encode([
                    'en' => 'Abstract Composition',
                    'ka' => 'აბსტრაქტული კომპოზიცია'
                ]),
                'description' => json_encode([
                    'en' => 'An experimental abstract artwork exploring color and form.',
                    'ka' => 'ექსპერიმენტული აბსტრაქტული ნამუშევარი, რომელიც იკვლევს ფერს და ფორმას.'
                ]),
                'media_type' => 'image',
                'file_path' => 'artworks/sample-abstract.jpg',
                'file_url' => '/storage/artworks/sample-abstract.jpg',
                'thumbnail_path' => 'artworks/thumbnails/sample-abstract-thumb.jpg',
                'original_filename' => 'abstract.jpg',
                'file_hash' => 'sample_hash_2',
                'file_size' => 1536000,
                'mime_type' => 'image/jpeg',
                'file_metadata' => json_encode(['width' => 1200, 'height' => 1600]),
                'license_type' => 'all_rights_reserved',
                'copyright_notice' => '© 2025 Artist User',
                'watermark_enabled' => true,
                'tags' => json_encode(['abstract', 'digital', 'experimental', 'color']),
                'category' => 'digital-art',
                'subcategory' => 'abstract',
                'is_ai_generated' => true,
                'ai_tools_used' => json_encode(['Midjourney', 'Photoshop']),
                'visibility' => 'public',
                'comments_enabled' => true,
                'downloads_enabled' => true,
                'status' => 'published',
                'published_at' => now()->subDays(2),
                'view_count' => 89,
                'like_count' => 34,
                'comment_count' => 8,
            ],
            [
                'user_id' => $testUser->id,
                'title' => json_encode([
                    'en' => 'Portrait Study',
                    'ka' => 'პორტრეტის შესწავლა'
                ]),
                'description' => json_encode([
                    'en' => 'A character portrait study in digital medium.',
                    'ka' => 'პერსონაჟის პორტრეტის შესწავლა ციფრულ მედიუმში.'
                ]),
                'media_type' => 'image',
                'file_path' => 'artworks/sample-portrait.jpg',
                'file_url' => '/storage/artworks/sample-portrait.jpg',
                'thumbnail_path' => 'artworks/thumbnails/sample-portrait-thumb.jpg',
                'original_filename' => 'portrait.jpg',
                'file_hash' => 'sample_hash_3',
                'file_size' => 3072000,
                'mime_type' => 'image/jpeg',
                'file_metadata' => json_encode(['width' => 1500, 'height' => 2000]),
                'license_type' => 'creative_commons_by_nc',
                'copyright_notice' => '© 2025 Test User',
                'watermark_enabled' => false,
                'tags' => json_encode(['portrait', 'character', 'digital', 'study']),
                'category' => 'digital-art',
                'subcategory' => 'portrait',
                'is_ai_generated' => false,
                'visibility' => 'public',
                'comments_enabled' => true,
                'downloads_enabled' => false,
                'status' => 'draft',
                'view_count' => 15,
                'like_count' => 3,
                'comment_count' => 1,
            ]
        ];

        foreach ($artworks as $artworkData) {
            Artwork::create($artworkData);
        }

        $this->command->info('Created ' . count($artworks) . ' sample artworks:');
        $this->command->info('- Digital Landscape (published)');
        $this->command->info('- Abstract Composition (published, AI-generated)');
        $this->command->info('- Portrait Study (draft)');
    }
}

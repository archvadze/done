<?php

namespace Database\Factories;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArtworkFactory extends Factory
{
    protected $model = Artwork::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => json_encode([
                'en' => $this->faker->sentence(3),
                'ka' => 'ქართული სათაური'
            ]),
            'description' => json_encode([
                'en' => $this->faker->paragraph(2),
                'ka' => 'ქართული აღწერა'
            ]),
            'media_type' => 'image',
            'file_path' => 'artworks/' . $this->faker->uuid() . '.jpg',
            'file_url' => 'https://example.com/artworks/' . $this->faker->uuid() . '.jpg',
            'thumbnail_path' => 'artworks/thumbnails/' . $this->faker->uuid() . '_thumb.jpg',
            'original_filename' => $this->faker->word() . '.jpg',
            'file_hash' => $this->faker->sha256(),
            'file_size' => $this->faker->numberBetween(100000, 5000000),
            'mime_type' => 'image/jpeg',
            'file_metadata' => json_encode([
                'width' => $this->faker->numberBetween(800, 4000),
                'height' => $this->faker->numberBetween(600, 3000),
                'format' => 'JPEG'
            ]),
            'license_type' => $this->faker->randomElement([
                'all_rights_reserved', 'creative_commons_by', 'creative_commons_by_sa', 
                'creative_commons_by_nc', 'creative_commons_by_nc_sa', 'public_domain'
            ]),
            'watermark_enabled' => $this->faker->boolean(70),
            'category' => $this->faker->randomElement([
                'digital-art', 'painting', 'photography', 'sculpture', 
                'music', 'video', 'mixed-media'
            ]),
            'tags' => json_encode($this->faker->randomElements([
                'abstract', 'landscape', 'portrait', 'digital', 'traditional',
                'colorful', 'minimalist', 'nature', 'urban', 'fantasy'
            ], $this->faker->numberBetween(1, 4))),
            'creative_process' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['draft', 'published', 'pending']),
            'is_ai_generated' => $this->faker->boolean(20),
            'price' => $this->faker->randomFloat(2, 10, 1000),
            'is_for_sale' => $this->faker->boolean(30),
            'allow_downloads' => $this->faker->boolean(60),
            'acq_total_score' => null,
            'view_count' => $this->faker->numberBetween(0, 1000),
            'featured_until' => $this->faker->optional(0.1)->dateTimeBetween('now', '+30 days'),
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function forSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_for_sale' => true,
            'price' => $this->faker->randomFloat(2, 50, 2000),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
        ]);
    }
}

<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Artwork;
use App\Services\FileUploadService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileUploadTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        $this->user = User::factory()->create();
    }

    /** @test */
    public function file_upload_service_stores_original_file()
    {
        // Create a fake uploaded file
        $file = UploadedFile::fake()->image('test.jpg', 800, 600);

        $service = app(FileUploadService::class);

        $metadata = [
            'title' => ['en' => 'Test Artwork'],
            'description' => ['en' => 'Test Description'],
            'category' => 'digital-art',
            'license_type' => 'all_rights_reserved',
            'visibility' => 'public',
        ];

        // Upload artwork
        $artwork = $service->uploadArtwork($file, $this->user, $metadata);

        // Assert file exists in storage
        $this->assertTrue(Storage::disk('public')->exists($artwork->file_path));

        // Assert thumbnail was created if applicable
        if ($artwork->thumbnail_path) {
            $this->assertTrue(Storage::disk('public')->exists($artwork->thumbnail_path));
        }

        // Assert database record
        $this->assertDatabaseHas('artworks', [
            'id' => $artwork->id,
            'user_id' => $this->user->id,
            'original_filename' => 'test.jpg',
            'media_type' => 'image',
        ]);
    }

    /** @test */
    public function replace_artwork_file_removes_old_and_stores_new()
    {
        // First upload
        $originalFile = UploadedFile::fake()->image('original.jpg', 800, 600);
        $service = app(FileUploadService::class);

        $artwork = $service->uploadArtwork($originalFile, $this->user, ['category' => 'digital-art']);
        $originalPath = $artwork->file_path;

        // Verify original file exists
        $this->assertTrue(Storage::disk('public')->exists($originalPath));

        // Replace with new file
        $newFile = UploadedFile::fake()->image('new.jpg', 1000, 800);
        $fileAttributes = $service->replaceArtworkFile($artwork, $newFile);

        // Verify old file was deleted
        $this->assertFalse(Storage::disk('public')->exists($originalPath));

        // Verify new file exists
        $this->assertTrue(Storage::disk('public')->exists($fileAttributes['file_path']));
    }

    /** @test */
    public function artwork_update_form_saves_changes()
    {
        // Create artwork
        $artwork = Artwork::factory()->create([
            'user_id' => $this->user->id,
            'title' => ['en' => 'Original Title'],
            'description' => ['en' => 'Original Description'],
        ]);

        // Submit update form
        $response = $this->actingAs($this->user)
            ->put(route('artworks.update', $artwork), [
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'category' => 'painting',
                'license_type' => 'cc_by',
                'visibility' => 'public',
                'comments_enabled' => true,
                'downloads_enabled' => false,
                'watermark_enabled' => true,
                'action' => 'save',
            ]);

        // Assert redirect
        $response->assertRedirect(route('artworks.show', $artwork));

        // Assert database was updated
        $artwork->refresh();
        $this->assertEquals('Updated Title', $artwork->getTitle());
        $this->assertEquals('Updated Description', $artwork->getDescription());
        $this->assertEquals('painting', $artwork->category);
    }
}

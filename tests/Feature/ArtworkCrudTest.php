<?php

namespace Tests\Feature;

use App\Models\Artwork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ArtworkCrudTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $artist;
    private User $regularUser;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');

        // Create test users
        $this->artist = User::factory()->create(['role' => 'artist']);
        $this->regularUser = User::factory()->create(['role' => 'user']);
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function artist_can_create_artwork()
    {
        $file = UploadedFile::fake()->image('artwork.jpg', 800, 600);

        $response = $this->actingAs($this->artist)
            ->post(route('artworks.store'), [
                'file' => $file,
                'title' => 'Test Artwork',
                'description' => 'Test description',
                'category' => 'digital-art',
                'license_type' => 'all_rights_reserved',
                'visibility' => 'public',
                'comments_enabled' => true,
                'downloads_enabled' => false,
                'watermark_enabled' => true,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('artworks', [
            'user_id' => $this->artist->id,
            'category' => 'digital-art',
        ]);

        // Verify file was uploaded
        $artwork = Artwork::where('user_id', $this->artist->id)->first();
        $this->assertNotNull($artwork->file_path);
        $this->assertTrue(Storage::disk('public')->exists($artwork->file_path));
    }

    /** @test */
    public function artist_can_view_own_artwork()
    {
        $artwork = Artwork::factory()->create(['user_id' => $this->artist->id]);

        $response = $this->actingAs($this->artist)
            ->get(route('artworks.show', $artwork));

        $response->assertOk();
        $response->assertViewIs('artworks.show');
        $response->assertViewHas('artwork');
    }

    /** @test */
    public function artist_can_edit_own_artwork()
    {
        $artwork = Artwork::factory()->create(['user_id' => $this->artist->id]);

        $response = $this->actingAs($this->artist)
            ->get(route('artworks.edit', $artwork));

        $response->assertOk();
        $response->assertViewIs('artworks.edit');
    }

    /** @test */
    public function artist_can_update_artwork_metadata()
    {
        $artwork = Artwork::factory()->create([
            'user_id' => $this->artist->id,
            'title' => ['en' => 'Original Title'],
            'description' => ['en' => 'Original Description'],
        ]);

        $response = $this->actingAs($this->artist)
            ->put(route('artworks.update', $artwork), [
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'category' => 'painting',
                'license_type' => 'cc_by',
                'visibility' => 'public',
                'comments_enabled' => true,
                'downloads_enabled' => false,
                'watermark_enabled' => true,
            ]);

        $response->assertRedirect(route('artworks.show', $artwork));

        $artwork->refresh();
        $this->assertEquals('Updated Title', $artwork->getTitle());
        $this->assertEquals('Updated Description', $artwork->getDescription());
        $this->assertEquals('painting', $artwork->category);
    }

    /** @test */
    public function artist_can_update_image_upload_on_artwork()
    {
        // Create artwork with initial file
        $initialFile = UploadedFile::fake()->image('initial.jpg', 800, 600);
        
        // First create the artwork
        $response = $this->actingAs($this->artist)
            ->post(route('artworks.store'), [
                'file' => $initialFile,
                'title' => 'Initial Title',
                'description' => 'Initial Description',
                'category' => 'digital-art',
                'license_type' => 'all_rights_reserved',
                'visibility' => 'public',
                'comments_enabled' => true,
                'downloads_enabled' => false,
                'watermark_enabled' => true,
            ]);

        $response->assertRedirect();
        $artwork = Artwork::where('user_id', $this->artist->id)->latest()->first();
        $this->assertNotNull($artwork);
        
        $originalFilePath = $artwork->file_path;
        $originalFilename = $artwork->original_filename;

        // Try to update with new file
        $newFile = UploadedFile::fake()->image('updated.jpg', 1000, 800);

        $response = $this->actingAs($this->artist)
            ->put(route('artworks.update', $artwork), [
                'file' => $newFile,
                'title' => 'Updated Title',
                'description' => 'Updated Description',
                'category' => 'painting',
                'license_type' => 'cc_by',
                'visibility' => 'public',
                'comments_enabled' => true,
                'downloads_enabled' => false,
                'watermark_enabled' => true,
            ]);

        $response->assertRedirect(route('artworks.show', $artwork));

        // Check that artwork is updated AND file was replaced
        $artwork->refresh();
        $this->assertEquals('Updated Title', $artwork->getTitle());
        $this->assertEquals('Updated Description', $artwork->getDescription());
        $this->assertEquals('painting', $artwork->category);
        
        // Verify file was actually replaced
        $this->assertNotEquals($originalFilePath, $artwork->file_path);
        $this->assertEquals('updated.jpg', $artwork->original_filename);
        
        // Verify new file exists
        $this->assertTrue(Storage::disk('public')->exists($artwork->file_path));
        
        // Verify old file was deleted
        if ($originalFilePath) {
            $this->assertFalse(Storage::disk('public')->exists($originalFilePath));
        }
    }

    /** @test */
    public function artist_can_delete_own_artwork()
    {
        $artwork = Artwork::factory()->create(['user_id' => $this->artist->id]);

        $response = $this->actingAs($this->artist)
            ->delete(route('artworks.destroy', $artwork));

        $response->assertRedirect(route('artworks.index'));
        $this->assertDatabaseMissing('artworks', ['id' => $artwork->id]);
    }

    /** @test */
    public function regular_user_cannot_create_artwork()
    {
        $file = UploadedFile::fake()->image('artwork.jpg', 800, 600);

        $response = $this->actingAs($this->regularUser)
            ->post(route('artworks.store'), [
                'file' => $file,
                'title' => 'Test Artwork',
                'description' => 'Test description',
                'category' => 'digital-art',
                'license_type' => 'all_rights_reserved',
                'visibility' => 'public',
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function regular_user_cannot_edit_others_artwork()
    {
        $artwork = Artwork::factory()->create(['user_id' => $this->artist->id]);

        $response = $this->actingAs($this->regularUser)
            ->get(route('artworks.edit', $artwork));

        $response->assertForbidden();
    }

    /** @test */
    public function regular_user_cannot_update_others_artwork()
    {
        $artwork = Artwork::factory()->create(['user_id' => $this->artist->id]);

        $response = $this->actingAs($this->regularUser)
            ->put(route('artworks.update', $artwork), [
                'title' => 'Hacked Title',
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function regular_user_cannot_delete_others_artwork()
    {
        $artwork = Artwork::factory()->create(['user_id' => $this->artist->id]);

        $response = $this->actingAs($this->regularUser)
            ->delete(route('artworks.destroy', $artwork));

        $response->assertForbidden();
    }

    /** @test */
    public function admin_cannot_create_artwork()
    {
        $file = UploadedFile::fake()->image('artwork.jpg', 800, 600);

        $response = $this->actingAs($this->admin)
            ->post(route('artworks.store'), [
                'file' => $file,
                'title' => 'Admin Artwork',
                'description' => 'Admin description',
                'category' => 'digital-art',
                'license_type' => 'all_rights_reserved',
                'visibility' => 'public',
            ]);

        $response->assertForbidden();
    }

    /** @test */
    public function admin_cannot_edit_others_artwork()
    {
        $artwork = Artwork::factory()->create(['user_id' => $this->artist->id]);

        $response = $this->actingAs($this->admin)
            ->get(route('artworks.edit', $artwork));

        $response->assertForbidden();
    }

    /** @test */
    public function guest_cannot_access_create_form()
    {
        $response = $this->get(route('artworks.create'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function guest_can_view_published_artwork()
    {
        $artwork = Artwork::factory()->create([
            'status' => 'published',
            'visibility' => 'public'
        ]);

        $response = $this->get(route('artworks.show', $artwork));
        $response->assertOk();
    }

    /** @test */
    public function guest_cannot_view_draft_artwork()
    {
        $artwork = Artwork::factory()->create([
            'status' => 'draft',
            'visibility' => 'private'
        ]);

        $response = $this->get(route('artworks.show', $artwork));
        $response->assertNotFound();
    }
}

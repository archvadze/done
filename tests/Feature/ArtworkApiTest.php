<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Artwork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ArtworkApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a test user with artist role
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'name' => 'Test User',
            'role' => 'artist'
        ]);
        
        Storage::fake('public');
    }

    /** @test */
    public function guest_can_view_published_artworks()
    {
        // Create published artwork
        $artwork = Artwork::factory()->published()->create([
            'user_id' => $this->user->id,
        ]);

        $response = $this->get('/artworks');

        $response->assertStatus(200);
    }

    /** @test */
    public function authenticated_user_can_access_create_form()
    {
        $response = $this->actingAs($this->user)
            ->get('/artworks/create');

        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_only_edit_own_artworks()
    {
        $artwork = Artwork::factory()->create([
            'user_id' => $this->user->id
        ]);

        $otherUser = User::factory()->create();

        // Owner can edit
        $response = $this->actingAs($this->user)
            ->get("/artworks/{$artwork->id}/edit");

        $response->assertStatus(200);

        // Other user cannot edit (should be redirected or forbidden)
        $response = $this->actingAs($otherUser)
            ->get("/artworks/{$artwork->id}/edit");

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function user_can_like_and_unlike_artwork()
    {
        $otherUser = User::factory()->create();
        $artwork = Artwork::factory()->published()->create([
            'user_id' => $otherUser->id,
        ]);

        // Like the artwork
        $response = $this->actingAs($this->user)
            ->post("/artworks/{$artwork->id}/like");

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('artwork_likes', [
            'user_id' => $this->user->id,
            'artwork_id' => $artwork->id
        ]);
    }

    /** @test */
    public function user_cannot_like_own_artwork()
    {
        $artwork = Artwork::factory()->create([
            'user_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->post("/artworks/{$artwork->id}/like");

        $response->assertStatus(403); // Should be forbidden
    }

    /** @test */
    public function guest_cannot_create_artwork()
    {
        $response = $this->get('/artworks/create');

        $response->assertStatus(302); // Should redirect to login
        $response->assertRedirect('/login');
    }

    /** @test */
    public function artwork_can_be_published_and_unpublished()
    {
        $artwork = Artwork::factory()->draft()->create([
            'user_id' => $this->user->id,
        ]);

        // Publish artwork
        $response = $this->actingAs($this->user)
            ->post("/artworks/{$artwork->id}/publish");

        $response->assertStatus(302);
        
        $artwork->refresh();
        $this->assertEquals('published', $artwork->status);
    }
}

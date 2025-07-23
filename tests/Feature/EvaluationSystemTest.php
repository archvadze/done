<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Artwork;
use App\Models\Evaluation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EvaluationSystemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    private User $user;
    private User $artist;
    private Artwork $artwork;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'name' => 'Test Evaluator',
            'email' => 'evaluator@test.com'
        ]);

        $this->artist = User::factory()->create([
            'name' => 'Test Artist',
            'email' => 'artist@test.com'
        ]);

        $this->artwork = Artwork::factory()->create([
            'user_id' => $this->artist->id,
            'title' => 'Test Artwork',
            'status' => 'published'
        ]);
    }

    /** @test */
    public function guest_cannot_access_evaluation_form()
    {
        $response = $this->get(route('evaluations.create', $this->artwork));

        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function artist_cannot_evaluate_own_artwork()
    {
        $response = $this->actingAs($this->artist)
            ->get(route('evaluations.create', $this->artwork));

        $response->assertStatus(403);
    }

    /** @test */
    public function authenticated_user_can_access_evaluation_form()
    {
        $response = $this->actingAs($this->user)
            ->get(route('evaluations.create', $this->artwork));

        $response->assertStatus(200)
            ->assertSee('Evaluate Artwork')
            ->assertSee('Technical Skill')
            ->assertSee('Composition')
            ->assertSee('Originality')
            ->assertSee('Emotional Impact');
    }

    /** @test */
    public function user_can_create_evaluation()
    {
        $evaluationData = [
            'score_technique' => 8,
            'score_composition' => 7,
            'score_originality' => 9,
            'score_impact' => 6,
            'feedback_text' => 'Great artwork with excellent technique!',
            'source' => 'human'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('evaluations.store', $this->artwork), $evaluationData);

        $response->assertRedirect(route('artworks.show', $this->artwork))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('evaluations', [
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $this->user->id,
            'score_technique' => 8,
            'score_composition' => 7,
            'score_originality' => 9,
            'score_impact' => 6,
            'feedback_text' => 'Great artwork with excellent technique!'
        ]);
    }

    /** @test */
    public function user_cannot_evaluate_same_artwork_twice()
    {
        // Create first evaluation
        Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $this->user->id
        ]);

        $evaluationData = [
            'score_technique' => 5,
            'score_composition' => 5,
            'score_originality' => 5,
            'score_impact' => 5,
            'source' => 'human'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('evaluations.store', $this->artwork), $evaluationData);

        $response->assertRedirect()
            ->assertSessionHasErrors();
    }

    /** @test */
    public function evaluation_scores_must_be_between_1_and_10()
    {
        $invalidData = [
            'score_technique' => 15, // Invalid: > 10
            'score_composition' => 0,  // Invalid: < 1
            'score_originality' => 5,
            'score_impact' => 5,
            'source' => 'human'
        ];

        $response = $this->actingAs($this->user)
            ->post(route('evaluations.store', $this->artwork), $invalidData);

        $response->assertSessionHasErrors(['score_technique', 'score_composition']);
    }

    /** @test */
    public function user_can_edit_own_evaluation()
    {
        $evaluation = Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $this->user->id,
            'score_technique' => 5
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('evaluations.edit', [$this->artwork, $evaluation]));

        $response->assertStatus(200)
            ->assertSee('Edit Your Evaluation');
    }

    /** @test */
    public function user_cannot_edit_others_evaluation()
    {
        $otherUser = User::factory()->create();
        $evaluation = Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $otherUser->id
        ]);

        $response = $this->actingAs($this->user)
            ->get(route('evaluations.edit', [$this->artwork, $evaluation]));

        $response->assertStatus(403);
    }

    /** @test */
    public function user_can_update_own_evaluation()
    {
        $evaluation = Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $this->user->id,
            'score_technique' => 5
        ]);

        $updateData = [
            'score_technique' => 9,
            'score_composition' => 8,
            'score_originality' => 7,
            'score_impact' => 8,
            'feedback_text' => 'Updated feedback'
        ];

        $response = $this->actingAs($this->user)
            ->put(route('evaluations.update', [$this->artwork, $evaluation]), $updateData);

        $response->assertRedirect(route('artworks.show', $this->artwork))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('evaluations', [
            'id' => $evaluation->id,
            'score_technique' => 9,
            'feedback_text' => 'Updated feedback'
        ]);
    }

    /** @test */
    public function user_can_delete_own_evaluation()
    {
        $evaluation = Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $this->user->id
        ]);

        $response = $this->actingAs($this->user)
            ->delete(route('evaluations.destroy', [$this->artwork, $evaluation]));

        $response->assertRedirect(route('artworks.show', $this->artwork))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('evaluations', [
            'id' => $evaluation->id
        ]);
    }

    /** @test */
    public function acq_score_is_calculated_correctly()
    {
        // Create multiple evaluations
        Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $this->user->id,
            'score_technique' => 8,
            'score_composition' => 7,
            'score_originality' => 9,
            'score_impact' => 6,
            'status' => 'approved'
        ]);

        $anotherUser = User::factory()->create();
        Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $anotherUser->id,
            'score_technique' => 6,
            'score_composition' => 8,
            'score_originality' => 7,
            'score_impact' => 9,
            'status' => 'approved'
        ]);

        // Trigger ACQ calculation
        $this->artwork->calculateAcqScore();

        // Expected: ((8+7+9+6)/4 + (6+8+7+9)/4) / 2 = (7.5 + 7.5) / 2 = 7.5
        $this->assertEquals(7.5, $this->artwork->fresh()->acq_score);
    }

    /** @test */
    public function leaderboard_shows_top_artworks()
    {
        // Create artwork with high ACQ score
        $topArtwork = Artwork::factory()->create([
            'user_id' => $this->artist->id,
            'acq_score' => 9.5,
            'status' => 'published',
            'evaluation_count' => 2
        ]);

        // Create artwork with lower ACQ score
        $lowerArtwork = Artwork::factory()->create([
            'user_id' => $this->artist->id,
            'acq_score' => 6.0,
            'status' => 'published',
            'evaluation_count' => 1
        ]);

        $response = $this->get(route('leaderboard'));

        $response->assertStatus(200)
            ->assertSee('ACQ Leaderboard')
            ->assertSee('Acumen Craft Quotient');

        // Refresh models to ensure casting works
        $topArtwork = $topArtwork->fresh();
        $lowerArtwork = $lowerArtwork->fresh();

        $response = $this->get(route('leaderboard'));

        $response->assertStatus(200)
            ->assertSee('ACQ Leaderboard')
            ->assertSee('Acumen Craft Quotient');

        // Check that higher scored artwork appears first
        $content = $response->getContent();
        $topTitle = $topArtwork->getTitle();
        $lowerTitle = $lowerArtwork->getTitle();
        
        $topPos = strpos($content, $topTitle);
        $lowerPos = strpos($content, $lowerTitle);
        
        $this->assertTrue($topPos !== false, 'Top artwork title should be found in content');
        $this->assertTrue($lowerPos !== false, 'Lower artwork title should be found in content');
        $this->assertTrue($topPos < $lowerPos, 'Top rated artwork should appear before lower rated ones');
    }

    /** @test */
    public function evaluation_index_shows_all_evaluations_for_artwork()
    {
        $evaluation1 = Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $this->user->id,
            'feedback_text' => 'First evaluation'
        ]);

        $anotherUser = User::factory()->create();
        $evaluation2 = Evaluation::factory()->create([
            'artwork_id' => $this->artwork->id,
            'evaluator_id' => $anotherUser->id,
            'feedback_text' => 'Second evaluation'
        ]);

        $response = $this->actingAs($this->user)->get(route('evaluations.index', $this->artwork));

        $response->assertStatus(200)
            ->assertSee('First evaluation')
            ->assertSee('Second evaluation')
            ->assertSee($this->user->name)
            ->assertSee($anotherUser->name);
    }
}

<?php

namespace Database\Factories;

use App\Models\Evaluation;
use App\Models\Artwork;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Evaluation>
 */
class EvaluationFactory extends Factory
{
    protected $model = Evaluation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'artwork_id' => Artwork::factory(),
            'evaluator_id' => User::factory(),
            'score_technique' => $this->faker->numberBetween(1, 10),
            'score_composition' => $this->faker->numberBetween(1, 10),
            'score_originality' => $this->faker->numberBetween(1, 10),
            'score_impact' => $this->faker->numberBetween(1, 10),
            'feedback_text' => $this->faker->optional(0.7)->paragraph(),
            'source' => $this->faker->randomElement(['human', 'ai', 'aggregate']),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected', 'flagged']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => function (array $attributes) {
                return $this->faker->dateTimeBetween($attributes['created_at'], 'now');
            },
        ];
    }

    /**
     * Indicate that the evaluation is approved.
     */
    public function approved(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'approved',
        ]);
    }

    /**
     * Indicate that the evaluation is from a human evaluator.
     */
    public function human(): static
    {
        return $this->state(fn(array $attributes) => [
            'source' => 'human',
        ]);
    }

    /**
     * Indicate that the evaluation has high scores.
     */
    public function excellent(): static
    {
        return $this->state(fn(array $attributes) => [
            'score_technique' => $this->faker->numberBetween(8, 10),
            'score_composition' => $this->faker->numberBetween(8, 10),
            'score_originality' => $this->faker->numberBetween(8, 10),
            'score_impact' => $this->faker->numberBetween(8, 10),
        ]);
    }

    /**
     * Indicate that the evaluation has low scores.
     */
    public function poor(): static
    {
        return $this->state(fn(array $attributes) => [
            'score_technique' => $this->faker->numberBetween(1, 4),
            'score_composition' => $this->faker->numberBetween(1, 4),
            'score_originality' => $this->faker->numberBetween(1, 4),
            'score_impact' => $this->faker->numberBetween(1, 4),
        ]);
    }
}

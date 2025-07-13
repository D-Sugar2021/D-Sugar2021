<?php

namespace Database\Factories;

use App\Models\Exam;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Question>
 */
class QuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'exam_id' => Exam::factory(),
            'question_text' => fake()->sentence() . '?',
            'type' => 'multiple_choice',
            'points' => fake()->randomElement([1, 2, 5, 10]),
        ];
    }

    /**
     * Indicate that the question is an essay question.
     */
    public function essay(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'essay',
        ]);
    }
}

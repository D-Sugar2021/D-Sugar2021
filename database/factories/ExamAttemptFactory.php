<?php

namespace Database\Factories;

use App\Models\Exam;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ExamAttempt>
 */
class ExamAttemptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = Carbon::instance(fake()->dateTimeBetween('-1 hour', 'now'));
        $exam = Exam::factory()->create(); // Create a default exam if not provided

        return [
            'user_id' => User::factory(), // Create a default user (student)
            'exam_id' => $exam->id,
            'start_time' => $startTime,
            'end_time' => null, // Typically null when attempt starts
            'score' => null,    // Typically null until graded
            'status' => 'started', // Default status
            'answers_payload' => json_encode([
                'q'.fake()->randomNumber(2) => fake()->word(),
                'q'.fake()->randomNumber(2) => fake()->sentence(),
            ]),
        ];
    }

    /**
     * Indicate that the attempt is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            // Ensure start_time is set, using a default if not already part of attributes
            $startTime = $attributes['start_time'] ?? Carbon::now()->subHour();
            $examDuration = ($attributes['exam_id'] ? Exam::find($attributes['exam_id'])->duration : 60) ?? 60;

            return [
                'status' => 'completed',
                'end_time' => Carbon::parse($startTime)->addMinutes($examDuration),
            ];
        });
    }

    /**
     * Indicate that the attempt is submitted.
     */
    public function submitted(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = $attributes['start_time'] ?? Carbon::now()->subHour();
            $examDuration = ($attributes['exam_id'] ? Exam::find($attributes['exam_id'])->duration : 60) ?? 60;
            $endTime = Carbon::parse($startTime)->addMinutes(fake()->numberBetween(10, $examDuration)); // Submitted before or at full duration

            return [
                'status' => 'submitted',
                'end_time' => $endTime,
            ];
        });
    }

    /**
     * Indicate that the attempt is graded.
     */
    public function graded(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = $attributes['start_time'] ?? Carbon::now()->subHour();
            $examDuration = ($attributes['exam_id'] ? Exam::find($attributes['exam_id'])->duration : 60) ?? 60;
            $endTime = Carbon::parse($startTime)->addMinutes(fake()->numberBetween(10, $examDuration));

            return [
                'status' => 'graded',
                'end_time' => $endTime,
                'score' => fake()->numberBetween(0, 100), // Assuming a score out of 100
            ];
        });
    }
}

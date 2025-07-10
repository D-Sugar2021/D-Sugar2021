<?php

namespace Tests\Feature\Console\Commands;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AutoSubmitExamsTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->create();
        $this->admin = User::factory()->admin()->create();
    }

    /** @test */
    public function command_auto_submits_expired_started_attempts()
    {
        $exam = Exam::factory()->recycle($this->admin)->create(['duration' => 30]); // 30 minutes duration

        // This attempt should be submitted
        $expiredAttempt = ExamAttempt::factory()->recycle($this->student)->recycle($exam)->create([
            'start_time' => Carbon::now()->subMinutes(35), // Started 35 mins ago
            'status' => 'started',
            'answers_payload' => json_encode(['q1' => 'some answer']),
        ]);

        // This attempt should not be submitted (still active)
        $activeAttempt = ExamAttempt::factory()->recycle($this->student)->recycle($exam)->create([
            'start_time' => Carbon::now()->subMinutes(10), // Started 10 mins ago
            'status' => 'started',
        ]);

        // This attempt should not be submitted (already submitted)
        $submittedAttempt = ExamAttempt::factory()->recycle($this->student)->recycle($exam)->create([
            'start_time' => Carbon::now()->subMinutes(40),
            'status' => 'submitted',
        ]);

        // Log::shouldReceive('info')->atLeast()->once(); // Expect some logging

        $this->artisan('exams:auto-submit')
            ->expectsOutputToContain("Attempt ID {$expiredAttempt->id} for Exam '{$exam->title}' by User ID {$this->student->id} has expired.")
            ->expectsOutputToContain('Successfully auto-submitted 1 exam attempts.')
            ->expectsOutputToContain('Auto-submission process finished.')
            ->assertExitCode(0);

        $expiredAttempt->refresh();
        $this->assertEquals('completed', $expiredAttempt->status);
        $this->assertNotNull($expiredAttempt->end_time);
        // Check if end_time is approximately start_time + duration
        $expectedEndTime = Carbon::parse($expiredAttempt->getRawOriginal('start_time'))->addMinutes($exam->duration);
        $this->assertEquals($expectedEndTime->toDateTimeString(), $expiredAttempt->end_time->toDateTimeString());


        $activeAttempt->refresh();
        $this->assertEquals('started', $activeAttempt->status); // Should remain started

        $submittedAttempt->refresh();
        $this->assertEquals('submitted', $submittedAttempt->status); // Should remain submitted
    }

    /** @test */
    public function command_handles_attempts_with_no_autosaved_answers()
    {
        $exam = Exam::factory()->recycle($this->admin)->create(['duration' => 30]);
        $expiredAttemptNoAnswers = ExamAttempt::factory()->recycle($this->student)->recycle($exam)->create([
            'start_time' => Carbon::now()->subMinutes(35),
            'status' => 'started',
            'answers_payload' => null, // No answers saved
        ]);

        $this->artisan('exams:auto-submit')
            ->assertExitCode(0);

        $expiredAttemptNoAnswers->refresh();
        $this->assertEquals('completed', $expiredAttemptNoAnswers->status);
        $this->assertNotNull($expiredAttemptNoAnswers->answers_payload);
        $payload = json_decode($expiredAttemptNoAnswers->answers_payload, true);
        $this->assertTrue($payload['auto_submitted_empty']);
    }

    /** @test */
    public function command_does_nothing_if_no_attempts_are_due()
    {
        $exam = Exam::factory()->recycle($this->admin)->create(['duration' => 60]);
        ExamAttempt::factory()->recycle($this->student)->recycle($exam)->create([
            'start_time' => Carbon::now()->subMinutes(10), // Not expired
            'status' => 'started',
        ]);

        $this->artisan('exams:auto-submit')
            ->expectsOutputToContain('No exam attempts were due for auto-submission at this time.')
            ->assertExitCode(0);
    }
}

<?php

namespace Tests\Unit\Models;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamAttemptTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $admin;
    protected Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->create(['role' => 'student']);
        $this->admin = User::factory()->admin()->create();
        $this->exam = Exam::factory()->recycle($this->admin)->published()->create(['duration' => 60]);
    }

    /** @test */
    public function exam_attempt_can_be_created()
    {
        $startTime = Carbon::now();
        $attempt = ExamAttempt::create([
            'user_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'start_time' => $startTime,
            'status' => 'started',
            'answers_payload' => json_encode(['q1' => 'ans1']),
        ]);

        $this->assertDatabaseHas('exam_attempts', [
            'user_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'status' => 'started',
        ]);
        $this->assertEquals($this->student->id, $attempt->user_id);
        $this->assertEquals($this->exam->id, $attempt->exam_id);
        $this->assertEquals($startTime->toDateTimeString(), $attempt->start_time->toDateTimeString());
        $this->assertEquals(['q1' => 'ans1'], json_decode($attempt->answers_payload, true));
    }

    /** @test */
    public function exam_attempt_belongs_to_a_user_and_an_exam()
    {
        $attempt = ExamAttempt::factory()
            ->for($this->student)
            ->for($this->exam)
            ->create();

        $this->assertInstanceOf(User::class, $attempt->user);
        $this->assertEquals($this->student->id, $attempt->user->id);
        $this->assertInstanceOf(Exam::class, $attempt->exam);
        $this->assertEquals($this->exam->id, $attempt->exam->id);
    }

    /** @test */
    public function remaining_time_in_seconds_attribute_calculates_correctly()
    {
        $durationMinutes = 30;
        $exam = Exam::factory()->recycle($this->admin)->create(['duration' => $durationMinutes]);

        $startTime = Carbon::now()->subMinutes(10); // Started 10 minutes ago
        $attempt = ExamAttempt::factory()->for($this->student)->for($exam)->create([
            'start_time' => $startTime,
            'status' => 'started',
        ]);

        $expectedRemainingSeconds = ($durationMinutes * 60) - (10 * 60);
        $this->assertEquals($expectedRemainingSeconds, $attempt->getRemainingTimeInSecondsAttribute());

        // Test when time is up
        $startTimeExpired = Carbon::now()->subMinutes($durationMinutes + 5); // Started 35 minutes ago for a 30 min exam
        $attempt->update(['start_time' => $startTimeExpired]);
        $this->assertEquals(0, $attempt->getRemainingTimeInSecondsAttribute());

        // Test when start_time is null
        $attempt->update(['start_time' => null]);
        $this->assertNull($attempt->getRemainingTimeInSecondsAttribute());
    }
}

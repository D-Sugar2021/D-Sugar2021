<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessExamAutosave;
use App\Models\ExamAttempt;
use App\Models\User;
use App\Models\Exam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class ProcessExamAutosaveTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function job_updates_exam_attempt_with_answers_payload()
    {
        $student = User::factory()->create();
        $admin = User::factory()->admin()->create();
        $exam = Exam::factory()->recycle($admin)->create();
        $attempt = ExamAttempt::factory()->recycle($student)->recycle($exam)->create([
            'answers_payload' => null, // Ensure it's null initially
        ]);

        $answersToSave = ['q1' => 'answer1', 'q2' => 'answer2'];

        // Log::shouldReceive('info')->once()->with("ProcessExamAutosave: Successfully processed autosave for ExamAttempt ID {$attempt->id}. Answers: ", $answersToSave);

        $job = new ProcessExamAutosave($attempt->id, $answersToSave);
        $job->handle();

        $attempt->refresh();
        $this->assertNotNull($attempt->answers_payload);
        $this->assertEquals($answersToSave, json_decode($attempt->answers_payload, true));
    }

    /** @test */
    public function job_logs_warning_if_attempt_not_found()
    {
        $nonExistentAttemptId = 9999;
        $answersToSave = ['q1' => 'answer1'];

        Log::shouldReceive('warning')
            ->once()
            ->with("ProcessExamAutosave: ExamAttempt with ID {$nonExistentAttemptId} not found.");

        $job = new ProcessExamAutosave($nonExistentAttemptId, $answersToSave);
        $job->handle();
    }
}

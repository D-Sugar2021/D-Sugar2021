<?php

namespace Tests\Feature\Http\Controllers\Student;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use App\Jobs\ProcessExamAutosave; // To assert it's dispatched
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue; // For testing jobs
use Tests\TestCase;
use Carbon\Carbon;

class ExamControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $otherStudent;
    protected User $admin;
    protected Exam $publishedExam;
    protected Exam $draftExam;

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->create(['role' => 'student']);
        $this->otherStudent = User::factory()->create(['role' => 'student']);
        $this->admin = User::factory()->admin()->create();

        $this->publishedExam = Exam::factory()->recycle($this->admin)->published()->create(['duration' => 60]);
        $this->draftExam = Exam::factory()->recycle($this->admin)->draft()->create();
    }

    /** @test */
    public function student_can_view_available_exams_index()
    {
        $response = $this->actingAs($this->student)->get(route('student.exams.index'));

        $response->assertStatus(200);
        $response->assertViewIs('student.exams.index');
        $response->assertViewHas('availableExams');
        $this->assertTrue($response->viewData('availableExams')->contains($this->publishedExam));
        $this->assertFalse($response->viewData('availableExams')->contains($this->draftExam));
    }

    /** @test */
    public function admin_cannot_view_student_exams_index_via_student_route()
    {
        $response = $this->actingAs($this->admin)->get(route('student.exams.index'));
        // Expect redirect to admin dashboard due to RoleMiddleware
        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function student_can_take_a_published_exam()
    {
        $response = $this->actingAs($this->student)->get(route('student.exams.take', $this->publishedExam));

        $response->assertStatus(200);
        $response->assertViewIs('student.exams.take');
        $response->assertViewHas('exam', $this->publishedExam);
        $response->assertViewHas('attempt');
        $response->assertViewHas('initialSecondsRemaining', $this->publishedExam->duration * 60);
        $this->assertDatabaseHas('exam_attempts', [
            'user_id' => $this->student->id,
            'exam_id' => $this->publishedExam->id,
            'status' => 'started',
        ]);
    }

    /** @test */
    public function student_cannot_take_a_draft_exam()
    {
        $response = $this->actingAs($this->student)->get(route('student.exams.take', $this->draftExam));
        $response->assertRedirect(route('student.exams.index'));
        $response->assertSessionHas('error', 'This exam is not currently available.');
    }

    /** @test */
    public function student_is_redirected_if_exam_already_completed()
    {
        ExamAttempt::factory()->recycle($this->student)->recycle($this->publishedExam)->create(['status' => 'submitted']);
        $response = $this->actingAs($this->student)->get(route('student.exams.take', $this->publishedExam));
        $response->assertRedirect(route('student.exams.index'));
        $response->assertSessionHas('info');
    }

    /** @test */
    public function student_can_submit_an_exam()
    {
        // Start an attempt
        $this->actingAs($this->student)->get(route('student.exams.take', $this->publishedExam));
        $attempt = ExamAttempt::where('user_id', $this->student->id)->where('exam_id', $this->publishedExam->id)->first();
        $this->assertNotNull($attempt);
        $this->assertEquals('started', $attempt->status);

        $answers = ['q1' => 'ans1', 'q2' => 'ans2'];
        $response = $this->actingAs($this->student)->post(route('student.exams.submit', $this->publishedExam), [
            'answers' => $answers
        ]);

        $response->assertRedirect(route('student.results.index'));
        $response->assertSessionHas('success');

        $attempt->refresh();
        $this->assertEquals('submitted', $attempt->status);
        $this->assertNotNull($attempt->end_time);
        // Note: We are not testing answers_payload saving here as it's done by a job.
    }

    /** @test */
    public function student_cannot_submit_exam_without_active_attempt()
    {
        $response = $this->actingAs($this->student)->post(route('student.exams.submit', $this->publishedExam), [
            'answers' => ['q1' => 'ans1']
        ]);
        $response->assertRedirect(route('student.exams.index'));
        $response->assertSessionHas('error');
    }

    /** @test */
    public function student_can_autosave_exam_answers()
    {
        Queue::fake(); // Fake the queue

        // Start an attempt
        $this->actingAs($this->student)->get(route('student.exams.take', $this->publishedExam));
        $attempt = ExamAttempt::where('user_id', $this->student->id)->where('exam_id', $this->publishedExam->id)->first();

        $answers = ['q1' => 'autosaved_ans1'];
        $response = $this->actingAs($this->student)->post(route('student.exams.autosave', $this->publishedExam), [
            'answers' => $answers
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);
        Queue::assertPushed(ProcessExamAutosave::class, function ($job) use ($attempt, $answers) {
            return $job->attemptId === $attempt->id && $job->answers === $answers;
        });
    }

    /** @test */
    public function autosave_returns_error_if_no_active_attempt()
    {
        Queue::fake();
        $response = $this->actingAs($this->student)->post(route('student.exams.autosave', $this->publishedExam), [
            'answers' => ['q1' => 'ans']
        ]);
        $response->assertStatus(404);
        $response->assertJson(['status' => 'error']);
        Queue::assertNotPushed(ProcessExamAutosave::class);
    }
}

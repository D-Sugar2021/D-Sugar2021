<?php

namespace Tests\Feature\Http\Controllers\Student;

use App\Models\Exam;
use App\Models\ExamAttempt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $student;
    protected User $otherStudent;
    protected User $admin;
    protected Exam $exam1;
    protected Exam $exam2;
    protected ExamAttempt $attempt1;
    protected ExamAttempt $attempt2; // Belongs to student
    protected ExamAttempt $otherStudentAttempt; // Belongs to otherStudent

    protected function setUp(): void
    {
        parent::setUp();
        $this->student = User::factory()->create(['role' => 'student']);
        $this->otherStudent = User::factory()->create(['role' => 'student']);
        $this->admin = User::factory()->admin()->create();

        $this->exam1 = Exam::factory()->recycle($this->admin)->published()->create();
        $this->exam2 = Exam::factory()->recycle($this->admin)->published()->create();

        $this->attempt1 = ExamAttempt::factory()
            ->for($this->student)
            ->for($this->exam1)
            ->graded() // Creates a graded attempt
            ->create(['score' => 80]);

        $this->attempt2 = ExamAttempt::factory()
            ->for($this->student)
            ->for($this->exam2)
            ->submitted() // Creates a submitted attempt
            ->create();

        $this->otherStudentAttempt = ExamAttempt::factory()
            ->for($this->otherStudent)
            ->for($this->exam1)
            ->graded()
            ->create(['score' => 70]);
    }

    /** @test */
    public function student_can_view_their_results_index()
    {
        $response = $this->actingAs($this->student)->get(route('student.results.index'));

        $response->assertStatus(200);
        $response->assertViewIs('student.results.index');
        $response->assertViewHas('attempts');

        $viewAttempts = $response->viewData('attempts');
        $this->assertTrue($viewAttempts->contains($this->attempt1));
        $this->assertTrue($viewAttempts->contains($this->attempt2));
        $this->assertFalse($viewAttempts->contains($this->otherStudentAttempt));
    }

    /** @test */
    public function student_can_view_details_of_their_own_attempt()
    {
        $response = $this->actingAs($this->student)->get(route('student.results.show', $this->attempt1));

        $response->assertStatus(200);
        $response->assertViewIs('student.results.show');
        $response->assertViewHas('attempt', $this->attempt1);
    }

    /** @test */
    public function student_cannot_view_details_of_another_students_attempt()
    {
        $response = $this->actingAs($this->student)->get(route('student.results.show', $this->otherStudentAttempt));
        $response->assertStatus(403); // Abort 403
    }

    /** @test */
    public function admin_cannot_access_student_results_via_student_routes()
    {
        $response = $this->actingAs($this->admin)->get(route('student.results.index'));
        $response->assertRedirect(route('admin.dashboard'));

        $response = $this->actingAs($this->admin)->get(route('student.results.show', $this->attempt1));
        // This will also be caught by RoleMiddleware first, then the controller's check if middleware was different.
        $response->assertRedirect(route('admin.dashboard'));
    }

    /** @test */
    public function guest_cannot_access_student_results()
    {
        $response = $this->get(route('student.results.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('student.results.show', $this->attempt1));
        $response->assertRedirect(route('login'));
    }
}

<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected User $studentUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->adminUser = User::factory()->admin()->create();
        $this->studentUser = User::factory()->create(['role' => 'student']); // Default factory role is student
    }

    /** @test */
    public function admin_can_view_exams_index_page()
    {
        Exam::factory()->count(3)->recycle($this->adminUser)->create();

        $response = $this->actingAs($this->adminUser)->get(route('admin.exams.index'));

        $response->assertStatus(200);
        $response->assertViewIs('admin.exams.index');
        $response->assertViewHas('exams');
        $this->assertCount(3, $response->viewData('exams'));
    }

    /** @test */
    public function student_cannot_view_exams_index_page()
    {
        $response = $this->actingAs($this->studentUser)->get(route('admin.exams.index'));
        // Expect redirect to student dashboard or login if middleware customized that way,
        // or 403 if it just aborts. Default is redirect to their own dashboard.
        $response->assertRedirect(route('student.dashboard'));
    }

    /** @test */
    public function guest_cannot_view_exams_index_page()
    {
        $response = $this->get(route('admin.exams.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_view_create_exam_page()
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.exams.create'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.exams.create');
    }

    /** @test */
    public function admin_can_store_a_new_exam()
    {
        $examData = [
            'title' => 'New Sample Exam',
            'description' => 'This is a description.',
            'duration' => 75,
            'status' => 'published',
        ];

        $response = $this->actingAs($this->adminUser)->post(route('admin.exams.store'), $examData);

        $response->assertRedirect(route('admin.exams.index'));
        $response->assertSessionHas('success', 'Exam created successfully.');
        $this->assertDatabaseHas('exams', [
            'title' => 'New Sample Exam',
            'duration' => 75,
            'status' => 'published',
            'created_by' => $this->adminUser->id,
        ]);
    }

    /** @test */
    public function store_exam_requires_valid_data()
    {
        $response = $this->actingAs($this->adminUser)->post(route('admin.exams.store'), []);
        $response->assertSessionHasErrors(['title', 'duration', 'status']);
    }

    /** @test */
    public function admin_can_view_edit_exam_page()
    {
        $exam = Exam::factory()->recycle($this->adminUser)->create();
        $response = $this->actingAs($this->adminUser)->get(route('admin.exams.edit', $exam));
        $response->assertStatus(200);
        $response->assertViewIs('admin.exams.edit');
        $response->assertViewHas('exam', $exam);
    }

    /** @test */
    public function admin_can_update_an_exam_and_allocate_students()
    {
        $exam = Exam::factory()->recycle($this->adminUser)->create();
        $students = User::factory()->count(3)->create();
        $studentIds = $students->pluck('id')->toArray();

        $updateData = [
            'title' => 'Updated Exam Title',
            'description' => $exam->description,
            'duration' => $exam->duration,
            'status' => 'published',
            'students' => $studentIds,
        ];

        $response = $this->actingAs($this->adminUser)->put(route('admin.exams.update', $exam), $updateData);

        $response->assertRedirect(route('admin.exams.index'));
        $response->assertSessionHas('success', 'Exam updated successfully.');
        $this->assertDatabaseHas('exams', ['id' => $exam->id, 'title' => 'Updated Exam Title']);
        $this->assertCount(3, $exam->refresh()->allocatedStudents);
        $this->assertEquals($studentIds, $exam->allocatedStudents->pluck('id')->toArray());
    }

    /** @test */
    public function admin_can_delete_an_exam()
    {
        $exam = Exam::factory()->recycle($this->adminUser)->create();
        $this->assertDatabaseCount('exams', 1);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.exams.destroy', $exam));

        $response->assertRedirect(route('admin.exams.index'));
        $response->assertSessionHas('success', 'Exam deleted successfully.');
        $this->assertDatabaseCount('exams', 0);
    }
}

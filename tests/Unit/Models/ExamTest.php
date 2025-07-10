<?php

namespace Tests\Unit\Models;

use App\Models\Exam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExamTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function exam_can_be_created_with_attributes()
    {
        $admin = User::factory()->admin()->create();
        $exam = Exam::create([
            'title' => 'Test Exam Title',
            'description' => 'Test exam description.',
            'duration' => 60,
            'status' => 'published',
            'created_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('exams', ['title' => 'Test Exam Title', 'duration' => 60]);
        $this->assertEquals('Test Exam Title', $exam->title);
        $this->assertEquals(60, $exam->duration);
        $this->assertEquals('published', $exam->status);
        $this->assertEquals($admin->id, $exam->created_by);
    }

    /** @test */
    public function exam_belongs_to_a_creator()
    {
        $admin = User::factory()->admin()->create();
        $exam = Exam::factory()->recycle($admin)->create(); // Use recycle to associate with existing admin

        $this->assertInstanceOf(User::class, $exam->creator);
        $this->assertEquals($admin->id, $exam->creator->id);
    }

    /** @test */
    public function exam_status_can_be_draft_published_or_archived()
    {
        $admin = User::factory()->admin()->create();
        $exam = Exam::factory()->recycle($admin)->create(['status' => 'draft']);
        $this->assertEquals('draft', $exam->status);

        $exam->status = 'published';
        $exam->save();
        $this->assertEquals('published', $exam->fresh()->status);

        $exam->status = 'archived';
        $exam->save();
        $this.assertEquals('archived', $exam->fresh()->status);
    }
}

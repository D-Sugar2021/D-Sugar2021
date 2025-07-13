<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Exam;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Exam $exam;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->exam = Exam::factory()->recycle($this->admin)->create();
    }

    /** @test */
    public function admin_can_view_questions_for_an_exam()
    {
        Question::factory()->recycle($this->exam)->count(3)->create();
        $response = $this->actingAs($this->admin)->get(route('admin.exams.questions.index', $this->exam));

        $response->assertStatus(200);
        $response->assertViewIs('admin.questions.index');
        $response->assertViewHas('exam', $this->exam);
        $response->assertViewHas('questions');
        $this->assertCount(3, $response->viewData('questions'));
    }

    /** @test */
    public function admin_can_view_create_question_page()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.exams.questions.create', $this->exam));
        $response->assertStatus(200);
        $response->assertViewIs('admin.questions.create');
    }

    /** @test */
    public function admin_can_store_a_multiple_choice_question()
    {
        $questionData = [
            'question_text' => 'What is the capital of France?',
            'type' => 'multiple_choice',
            'points' => 5,
            'options' => [
                ['text' => 'London'],
                ['text' => 'Berlin'],
                ['text' => 'Paris'],
                ['text' => 'Madrid'],
            ],
            'correct_option' => 2, // Index of 'Paris'
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.exams.questions.store', $this->exam), $questionData);

        $response->assertRedirect(route('admin.exams.questions.index', $this->exam));
        $this->assertDatabaseHas('questions', ['question_text' => 'What is the capital of France?', 'points' => 5]);
        $this->assertDatabaseHas('options', ['option_text' => 'Paris', 'is_correct' => true]);
        $this->assertDatabaseHas('options', ['option_text' => 'London', 'is_correct' => false]);
    }

    /** @test */
    public function admin_can_store_an_essay_question()
    {
        $questionData = [
            'question_text' => 'Explain the concept of MVC.',
            'type' => 'essay',
            'points' => 20,
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.exams.questions.store', $this->exam), $questionData);
        $response->assertRedirect(route('admin.exams.questions.index', $this->exam));
        $this->assertDatabaseHas('questions', ['question_text' => 'Explain the concept of MVC.', 'type' => 'essay']);
    }

    /** @test */
    public function admin_can_delete_a_question()
    {
        $question = Question::factory()->recycle($this->exam)->create();
        $this->assertDatabaseCount('questions', 1);

        $response = $this->actingAs($this->admin)->delete(route('questions.destroy', $question)); // Using shallow route
        $response->assertRedirect(route('admin.exams.questions.index', $this->exam));
        $this->assertDatabaseCount('questions', 0);
    }
}

<?php

namespace Tests\Unit\Models;

use App\Models\Exam;
use App\Models\Option;
use App\Models\Question;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionOptionTest extends TestCase
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
    public function a_question_can_be_created()
    {
        $question = Question::create([
            'exam_id' => $this->exam->id,
            'question_text' => 'What is 2+2?',
            'type' => 'multiple_choice',
            'points' => 5,
        ]);

        $this->assertDatabaseHas('questions', ['question_text' => 'What is 2+2?', 'points' => 5]);
        $this->assertEquals($this->exam->id, $question->exam->id);
    }

    /** @test */
    public function a_question_can_have_options()
    {
        $question = Question::factory()->recycle($this->exam)->create();
        Option::factory()->recycle($question)->create(['option_text' => 'Answer A']);
        Option::factory()->recycle($question)->create(['option_text' => 'Answer B', 'is_correct' => true]);

        $this->assertCount(2, $question->options);
        $this->assertEquals('Answer A', $question->options[0]->option_text);
        $this->assertTrue($question->options[1]->is_correct);
    }

    /** @test */
    public function an_option_belongs_to_a_question()
    {
        $question = Question::factory()->recycle($this->exam)->create();
        $option = Option::factory()->recycle($question)->create();

        $this->assertInstanceOf(Question::class, $option->question);
        $this->assertEquals($question->id, $option->question->id);
    }
}

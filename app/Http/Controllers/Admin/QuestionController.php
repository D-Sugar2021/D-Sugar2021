<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\Option;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class QuestionController extends Controller
{
    /**
     * Display a listing of the questions for a specific exam.
     */
    public function index(Exam $exam)
    {
        $questions = $exam->questions()->with('options')->paginate(10);
        return view('admin.questions.index', compact('exam', 'questions'));
    }

    /**
     * Show the form for creating a new question for a specific exam.
     */
    public function create(Exam $exam)
    {
        return view('admin.questions.create', compact('exam'));
    }

    /**
     * Store a newly created question in storage.
     */
    public function store(Request $request, Exam $exam)
    {
        $request->validate([
            'question_text' => 'required|string',
            'type' => ['required', Rule::in(['multiple_choice', 'essay'])],
            'points' => 'required|integer|min:1',
            // Validation for multiple choice options
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.text' => 'required_if:type,multiple_choice|string',
            'correct_option' => 'required_if:type,multiple_choice|integer',
        ]);

        $question = $exam->questions()->create([
            'question_text' => $request->question_text,
            'type' => $request->type,
            'points' => $request->points,
        ]);

        if ($request->type === 'multiple_choice' && $request->has('options')) {
            foreach ($request->options as $index => $optionData) {
                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => ($index == $request->correct_option),
                ]);
            }
        }

        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Question created successfully.');
    }

    /**
     * Show the form for editing the specified question.
     */
    public function edit(Exam $exam, Question $question)
    {
        $question->load('options'); // Eager load options
        return view('admin.questions.edit', compact('exam', 'question'));
    }

    /**
     * Update the specified question in storage.
     */
    public function update(Request $request, Exam $exam, Question $question)
    {
        $request->validate([
            'question_text' => 'required|string',
            'points' => 'required|integer|min:1',
            // You can't change the type of a question once created to keep things simple
            // If type is multiple_choice, validate options
            'options' => 'required_if:type,multiple_choice|array|min:2',
            'options.*.text' => 'required_if:type,multiple_choice|string',
            'correct_option' => 'required_if:type,multiple_choice|integer',
        ]);

        $question->update([
            'question_text' => $request->question_text,
            'points' => $request->points,
        ]);

        if ($question->type === 'multiple_choice' && $request->has('options')) {
            // A simple way to update: delete old options and create new ones.
            // A more complex implementation could try to update existing options by ID.
            $question->options()->delete();
            foreach ($request->options as $index => $optionData) {
                $question->options()->create([
                    'option_text' => $optionData['text'],
                    'is_correct' => ($index == $request->correct_option),
                ]);
            }
        }

        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Question updated successfully.');
    }

    /**
     * Remove the specified question from storage.
     */
    public function destroy(Exam $exam, Question $question)
    {
        $question->delete();
        return redirect()->route('admin.exams.questions.index', $exam)->with('success', 'Question deleted successfully.');
    }
}

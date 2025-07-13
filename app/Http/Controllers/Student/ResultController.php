<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\ExamAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    /**
     * Display a listing of the student's exam attempts and results.
     */
    public function index()
    {
        $user = Auth::user();
        $attempts = ExamAttempt::where('user_id', $user->id)
            ->whereIn('status', ['submitted', 'graded', 'completed']) // Show exams that are finished
            ->with('exam') // Eager load exam details
            ->orderBy('updated_at', 'desc')
            ->paginate(10);

        return view('student.results.index', compact('attempts'));
    }

    /**
     * Display the specified exam attempt result.
     * We'll use implicit route model binding and then check ownership.
     */
    public function show(ExamAttempt $attempt)
    {
        // Ensure the logged-in student owns this attempt
        if ($attempt->user_id !== Auth::id()) {
            // Or redirect to results index with an error
            abort(403, 'You are not authorized to view this result.');
        }

        // For now, we don't have detailed question-answer breakdown.
        // We can pass the attempt (which includes score and status) and the exam details.
        // Eager load the questions and the student's answers for this attempt.
        $attempt->load(['exam.questions.options', 'answers.option']);

        return view('student.results.show', compact('attempt'));
    }
}

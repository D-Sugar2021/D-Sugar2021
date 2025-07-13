<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
// Will need Question model later
// use App\Models\Question;
use App\Models\ExamAttempt; // Now using ExamAttempt
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon; // For time manipulation
use App\Jobs\ProcessExamAutosave; // Import the job
use Illuminate\Support\Facades\Log; // For logging

class StudentExamController extends Controller
{
    /**
     * Display a listing of available exams for the student.
     */
    public function index()
    {
        $availableExams = Exam::where('status', 'published')
                                ->orderBy('title')
                                ->paginate(10); // Paginate if list can be long

        return view('student.exams.index', compact('availableExams'));
    }

    /**
     * Show the exam taking interface for a specific exam.
     * This method will also handle starting the exam session for the student.
     */
    public function show(Exam $exam)
    {
        // Ensure the exam is published
        if ($exam->status !== 'published') {
            return redirect()->route('student.exams.index')->with('error', 'This exam is not currently available.');
        }

        $user = Auth::user();
        $now = Carbon::now();

        // Find existing attempt or create a new one
        $attempt = ExamAttempt::firstOrCreate(
            ['user_id' => $user->id, 'exam_id' => $exam->id],
            ['start_time' => $now, 'status' => 'started']
        );

        // If attempt was already completed/submitted, redirect (or show results view)
        if (in_array($attempt->status, ['completed', 'submitted', 'graded'])) {
            // For now, redirect to exam list with a message. Later, could redirect to a results page.
            return redirect()->route('student.exams.index')->with('info', "You have already completed the exam '{$exam->title}'.");
        }

        // If the attempt was 'started' but start_time is somehow null (should not happen with firstOrCreate logic above), set it.
        if ($attempt->status === 'started' && is_null($attempt->start_time)) {
            $attempt->start_time = $now;
            $attempt->save();
        }

        // Calculate remaining time based on attempt's start_time and exam duration
        $elapsedSeconds = $now->diffInSeconds($attempt->start_time);
        $totalDurationSeconds = $exam->duration * 60;
        $initialSecondsRemaining = max(0, $totalDurationSeconds - $elapsedSeconds);

        if ($initialSecondsRemaining <= 0 && $attempt->status === 'started') {
            // Time is already up, mark as completed (auto-submit logic will handle this on client, but server should also be aware)
            // This scenario might occur if user reopens page after time expired but before auto-submit fully processed
            // $attempt->status = 'completed';
            // $attempt->end_time = $attempt->start_time->addSeconds($totalDurationSeconds);
            // $attempt->save();
            // For now, let client-side timer handle immediate auto-submit.
            // If they land here and time is 0, the timer will immediately trigger submit.
        }

        // Eager load questions and their options
        $questions = $exam->questions()->with('options')->get();

        return view('student.exams.take', compact('exam', 'attempt', 'initialSecondsRemaining', 'questions'));
    }

    /**
     * Handle the submission of the exam by the student.
     * This will be called by the form when student submits or timer auto-submits.
     */
    public function submit(Request $request, Exam $exam)
    {
        $user = Auth::user();
        $attempt = ExamAttempt::where('user_id', $user->id)
                              ->where('exam_id', $exam->id)
                              ->where('status', 'started') // Only submit if it was started
                              ->first();

        if (!$attempt) {
            return redirect()->route('student.exams.index')->with('error', 'No active attempt found for this exam or it has already been submitted.');
        }

        $answers = $request->input('answers', []);

        // Dispatch one final job to save all answers upon submission
        if (!empty($answers)) {
            ProcessExamAutosave::dispatchSync($attempt->id, $answers); // Use dispatchSync for immediate processing
        }

        // Calculate score for multiple choice questions
        $attempt->refresh(); // Refresh to get the newly saved answers
        $score = 0;
        $hasEssayQuestions = false;

        foreach ($attempt->answers as $answer) {
            if ($answer->question->type === 'multiple_choice') {
                if ($answer->option && $answer->option->is_correct) {
                    $score += $answer->question->points;
                }
            } elseif ($answer->question->type === 'essay') {
                $hasEssayQuestions = true;
            }
        }

        $attempt->score = $score;
        $attempt->end_time = Carbon::now();
        // If there are essay questions, status is 'submitted' for manual grading.
        // Otherwise, it's 'graded'.
        $attempt->status = $hasEssayQuestions ? 'submitted' : 'graded';
        $attempt->save();

        // Remove the onbeforeunload warning as the exam is now submitted
        // This is ideally done client-side upon successful submission too.
        // Session::flash('remove_onbeforeunload', true); // Example server-side flag for view

        return redirect()->route('student.results.index')->with('success', "Exam '{$exam->title}' submitted successfully!");
    }

    /**
     * Handle autosaving of exam answers.
     */
    public function autosave(Request $request, Exam $exam)
    {
        $user = Auth::user();
        $attempt = ExamAttempt::where('user_id', $user->id)
                              ->where('exam_id', $exam->id)
                              ->where('status', 'started') // Can only autosave for 'started' exams
                              ->first();

        if (!$attempt) {
            Log::warning("Autosave attempt for non-existent or non-started exam. User ID: {$user->id}, Exam ID: {$exam->id}");
            return response()->json(['status' => 'error', 'message' => 'No active attempt found.'], 404);
        }

        $answers = $request->input('answers', []);

        if (empty($answers)) {
            return response()->json(['status' => 'no_data', 'message' => 'No answers provided to save.'], 200);
        }

        try {
            ProcessExamAutosave::dispatch($attempt->id, $answers);
            Log::info("Autosave job dispatched for attempt ID: {$attempt->id}");
            return response()->json(['status' => 'success', 'message' => 'Answers queued for saving.'], 200);
        } catch (\Exception $e) {
            Log::error("Autosave dispatch error for attempt ID: {$attempt->id}. Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Failed to queue answers for saving.'], 500);
        }
    }
}

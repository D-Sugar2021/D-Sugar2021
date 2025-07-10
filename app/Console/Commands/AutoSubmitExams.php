<?php

namespace App\Console\Commands;

use App\Models\ExamAttempt;
use Illuminate\Console\Command;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AutoSubmitExams extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'exams:auto-submit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically submits exam attempts that have passed their duration.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting auto-submission process for exams...');
        Log::info('Scheduled Task: Starting auto-submission process for exams.');

        $now = Carbon::now();
        $submittedCount = 0;

        // Find attempts that are still 'started'
        $attemptsToSubmit = ExamAttempt::where('status', 'started')
            ->with('exam') // Eager load exam to get duration
            ->whereNotNull('start_time') // Ensure start_time is set
            ->get();

        if ($attemptsToSubmit->isEmpty()) {
            $this->info('No active exam attempts found that require auto-submission check.');
            Log::info('Scheduled Task: No active exam attempts found for auto-submission.');
            return Command::SUCCESS;
        }

        foreach ($attemptsToSubmit as $attempt) {
            if (!$attempt->exam) {
                Log::warning("Scheduled Task: ExamAttempt ID {$attempt->id} is missing related Exam data. Skipping.");
                continue;
            }

            $examDurationMinutes = $attempt->exam->duration;
            $expectedEndTime = Carbon::parse($attempt->start_time)->addMinutes($examDurationMinutes);

            if ($now->greaterThanOrEqualTo($expectedEndTime)) {
                $this->line("Attempt ID {$attempt->id} for Exam '{$attempt->exam->title}' by User ID {$attempt->user_id} has expired.");

                $attempt->status = 'completed'; // Or 'submitted' depending on workflow
                // Set end_time to the calculated expiry time, not 'now', to be accurate
                $attempt->end_time = $expectedEndTime;

                // If answers_payload is null (meaning no autosave happened or it failed),
                // we might want to log this or handle it. For now, just submit.
                if(is_null($attempt->answers_payload)){
                    // Set an empty answers payload if none exists, so it's clear it was auto-submitted without student input saved.
                    $attempt->answers_payload = json_encode(['auto_submitted_empty' => true, 'reason' => 'Time expired, no answers autosaved.']);
                     Log::info("Scheduled Task: Attempt ID {$attempt->id} auto-submitted with no prior saved answers.");
                }

                $attempt->save();
                $submittedCount++;
                Log::info("Scheduled Task: Auto-submitted ExamAttempt ID {$attempt->id}. Status set to 'completed', end_time set to " . $expectedEndTime->toDateTimeString());

                // TODO: Optionally, dispatch a job here for grading or further processing
                // dispatch(new ProcessExamGradingJob($attempt->id));
            }
        }

        if ($submittedCount > 0) {
            $this->info("Successfully auto-submitted {$submittedCount} exam attempts.");
            Log::info("Scheduled Task: Successfully auto-submitted {$submittedCount} exam attempts.");
        } else {
            $this->info('No exam attempts were due for auto-submission at this time.');
            Log::info('Scheduled Task: No exam attempts met criteria for auto-submission.');
        }

        $this.info('Auto-submission process finished.');
        Log::info('Scheduled Task: Auto-submission process finished.');
        return Command::SUCCESS;
    }
}

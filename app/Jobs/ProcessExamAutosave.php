<?php

namespace App\Jobs;

use App\Models\ExamAttempt;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessExamAutosave implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $attemptId;
    protected array $answers;

    /**
     * Create a new job instance.
     *
     * @param int $attemptId
     * @param array $answers
     */
    public function __construct(int $attemptId, array $answers)
    {
        $this->attemptId = $attemptId;
        $this->answers = $answers;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $attempt = ExamAttempt::find($this->attemptId);

        if (!$attempt) {
            Log::warning("ProcessExamAutosave: ExamAttempt with ID {$this->attemptId} not found.");
            return;
        }

        // In a full implementation, this is where you'd iterate through $this->answers
        // and save them to a dedicated 'attempt_answers' table, linking them to $this->attemptId.
        // For example:
        // foreach ($this->answers as $questionId => $answerValue) {
        //   AttemptAnswer::updateOrCreate(
        //     ['exam_attempt_id' => $this->attemptId, 'question_id' => $questionId],
        //     ['answer_value' => $answerValue] // Adjust 'answer_value' based on answer type
        //   );
        // }

        // For now, we'll store the raw payload in the new column.
        // This is not ideal for querying individual answers but serves the autosave purpose for now.
        $attempt->answers_payload = json_encode($this->answers);
        $attempt->save();

        Log::info("ProcessExamAutosave: Successfully processed autosave for ExamAttempt ID {$this->attemptId}. Answers: ", $this->answers);
    }
}

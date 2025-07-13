<?php

namespace App\Jobs;

use App\Models\ExamAttempt;
use App\Models\AttemptAnswer;
use App\Models\Question;
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

        foreach ($this->answers as $questionId => $answerValue) {
            // The key from the form is `answers[question_id]`. We need to extract the ID.
            if (preg_match('/\[(\d+)\]/', $questionId, $matches)) {
                $qId = (int) $matches[1];

                $question = Question::find($qId);
                if (!$question) {
                    Log::warning("ProcessExamAutosave: Question with ID {$qId} not found for attempt {$this->attemptId}.");
                    continue;
                }

                $dataToUpdate = [
                    'exam_attempt_id' => $this->attemptId,
                    'question_id' => $qId,
                ];

                if ($question->type === 'multiple_choice') {
                    $dataToUpdate['option_id'] = (int) $answerValue;
                    $dataToUpdate['answer_text'] = null; // Or you could store the option text here
                } elseif ($question->type === 'essay') {
                    $dataToUpdate['option_id'] = null;
                    $dataToUpdate['answer_text'] = $answerValue;
                }

                AttemptAnswer::updateOrCreate(
                    ['exam_attempt_id' => $this->attemptId, 'question_id' => $qId],
                    $dataToUpdate
                );
            }
        }

        Log::info("ProcessExamAutosave: Successfully processed autosave for ExamAttempt ID {$this->attemptId}.");
    }
}

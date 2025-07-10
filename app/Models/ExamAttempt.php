<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'exam_id',
        'start_time',
        'end_time',
        'score',
        'status',
        'answers_payload', // Added here
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'score' => 'integer',
        // 'answers_payload' => 'array', // Cast to array if storing as JSON and want auto-conversion
    ];

    /**
     * Get the student (user) who made this attempt.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the exam that was attempted.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    // Later, you might add a relationship for stored answers
    // public function answers()
    // {
    //     return $this->hasMany(AttemptAnswer::class); // Assuming an AttemptAnswer model
    // }

    /**
     * Calculate remaining time in seconds.
     * Returns null if start_time or exam duration is not set.
     * Returns 0 if time has expired.
     *
     * @return int|null
     */
    public function getRemainingTimeInSecondsAttribute(): ?int
    {
        if (!$this->start_time || !$this->exam || !$this->exam->duration) {
            return null;
        }

        $elapsedSeconds = now()->diffInSeconds($this->start_time);
        $totalDurationSeconds = $this->exam->duration * 60;
        $remainingSeconds = $totalDurationSeconds - $elapsedSeconds;

        return max(0, $remainingSeconds); // Ensure it doesn't go negative
    }
}

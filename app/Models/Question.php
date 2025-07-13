<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_text',
        'type',
        'points',
    ];

    /**
     * Get the exam that this question belongs to.
     */
    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * Get the options for this question (if it's multiple-choice).
     */
    public function options(): HasMany
    {
        return $this->hasMany(Option::class);
    }
}

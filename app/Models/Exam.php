<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'duration',
        'status',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'integer',
    ];

    /**
     * Get the user who created the exam.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all questions for the exam.
     */
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    /**
     * The students that are allocated to this exam.
     */
    public function allocatedStudents()
    {
        return $this->belongsToMany(User::class, 'exam_user', 'exam_id', 'user_id');
    }
}

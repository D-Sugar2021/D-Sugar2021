<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('exam_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Student who took the exam
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade'); // The exam being attempted
            $table->timestamp('start_time')->nullable();
            $table->timestamp('end_time')->nullable(); // Actual submission time or time up
            $table->integer('score')->nullable(); // Calculated score
            $table->enum('status', ['started', 'completed', 'submitted', 'graded'])->default('started');
            // 'started': exam initiated, timer running
            // 'completed': student finished, or time ran out (pending submission processing)
            // 'submitted': answers processed and saved
            // 'graded': score assigned (if manual grading involved, or just to mark scoring complete)
            $table->timestamps(); // For created_at and updated_at

            $table->unique(['user_id', 'exam_id']); // A student can attempt an exam only once (can be adjusted if re-attempts are allowed)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_attempts');
    }
};

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
        Schema::create('attempt_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_attempt_id')->constrained('exam_attempts')->onDelete('cascade');
            $table->foreignId('question_id')->constrained('questions')->onDelete('cascade');
            // For multiple choice, this will be the ID of the selected option.
            // For essay questions, this can be null.
            $table->foreignId('option_id')->nullable()->constrained('options')->onDelete('cascade');
            // For essay questions, the answer is stored here.
            // For multiple choice, this is redundant but can be used for logging or if option text changes.
            $table->text('answer_text')->nullable();
            $table->timestamps();

            // A student should only have one answer per question in an attempt
            $table->unique(['exam_attempt_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attempt_answers');
    }
};

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
        Schema::table('exam_attempts', function (Blueprint $table) {
            // Check if the column exists before trying to drop it
            if (Schema::hasColumn('exam_attempts', 'answers_payload')) {
                $table->dropColumn('answers_payload');
            }
        });
    }

    /**
     * Reverse the migrations.
     * Re-add the column on rollback.
     */
    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->text('answers_payload')->nullable()->after('status');
        });
    }
};

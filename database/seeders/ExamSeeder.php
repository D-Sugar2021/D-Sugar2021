<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Exam;
use App\Models\User;

class ExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if ($admin) {
            Exam::firstOrCreate(
                ['title' => 'Basic Mathematics Quiz'],
                [
                    'description' => 'A simple quiz covering basic arithmetic and algebra.',
                    'duration' => 30, // minutes
                    'status' => 'published',
                    'created_by' => $admin->id,
                ]
            );

            Exam::firstOrCreate(
                ['title' => 'Laravel Fundamentals Test'],
                [
                    'description' => 'Test your knowledge of Laravel basics, including MVC, Eloquent, and Blade.',
                    'duration' => 60, // minutes
                    'status' => 'published',
                    'created_by' => $admin->id,
                ]
            );

            Exam::firstOrCreate(
                ['title' => 'History Challenge (Draft)'],
                [
                    'description' => 'A quiz on world history. Still under development.',
                    'duration' => 45, // minutes
                    'status' => 'draft',
                    'created_by' => $admin->id,
                ]
            );
        } else {
            $this->command->warn('Admin user not found. Skipping ExamSeeder.');
        }
    }
}

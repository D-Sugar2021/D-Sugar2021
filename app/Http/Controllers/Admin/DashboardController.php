<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User; // Assuming User model is used for students as well
use App\Models\ExamAttempt;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalExams = Exam::count();
        // Assuming 'student' role is used to identify students
        $totalStudents = User::where('role', 'student')->count();
        $totalAttempts = ExamAttempt::count();

        // Fetch some recent activity, e.g., latest attempts
        $recentAttempts = ExamAttempt::with(['user', 'exam'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalExams',
            'totalStudents',
            'totalAttempts',
            'recentAttempts'
        ));
    }
}

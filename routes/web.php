<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    // return view('welcome'); // Default Laravel welcome view
    return view('auth.login'); // Or redirect to login
});

// General dashboard (fallback) - requires authentication
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        // This could be a generic dashboard or redirect further based on role again if needed
        $user = auth()->user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isStudent()) {
            return redirect()->route('student.dashboard');
        }
        // Fallback for users with no specific role dashboard or if they land here directly
        return view('dashboard'); // Assumes a 'dashboard.blade.php' view exists
    })->name('dashboard');
});


// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

    // Exam CRUD routes
    Route::resource('exams', \App\Http\Controllers\Admin\ExamController::class);
    // Nested Question CRUD routes
    Route::resource('exams.questions', \App\Http\Controllers\Admin\QuestionController::class)->except(['show'])->shallow();
});

// Student routes
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', function () {
        return view('student.dashboard'); // Assumes 'student.dashboard.blade.php' view exists
    })->name('dashboard');

    // Student Exam Routes
    Route::get('/exams', [\App\Http\Controllers\Student\ExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam}/take', [\App\Http\Controllers\Student\ExamController::class, 'show'])->name('exams.take');
    Route::post('/exams/{exam}/submit', [\App\Http\Controllers\Student\ExamController::class, 'submit'])->name('exams.submit');
    Route::post('/exams/{exam}/autosave', [\App\Http\Controllers\Student\ExamController::class, 'autosave'])->name('exams.autosave');

    // Student Results Routes
    Route::get('/results', [\App\Http\Controllers\Student\ResultController::class, 'index'])->name('results.index');
    Route::get('/results/{attempt}', [\App\Http\Controllers\Student\ResultController::class, 'show'])->name('results.show');
});


// Include auth routes (login, logout, register etc.)
require __DIR__.'/auth.php';

// Placeholder for profile routes (used in app.blade.php)
Route::middleware('auth')->group(function () {
    Route::get('/profile', function() {
        // Dummy view for profile, actual implementation would use a controller
        return response("Profile Page Placeholder. User: " . auth()->user()->name);
    })->name('profile.edit');
});

// Temporary Style Test Route
Route::get('/style-test', function() {
    return view('style-test');
});

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\User; // Import User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $exams = Exam::with('creator')->latest()->paginate(10);
        return view('admin.exams.index', compact('exams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.exams.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
        ]);

        Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'duration' => $request->duration,
            'status' => $request->status,
            'created_by' => Auth::id(),
        ]);

        return redirect()->route('admin.exams.index')->with('success', 'Exam created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Exam $exam)
    {
        // Typically, for admin CRUD, 'show' might redirect to 'edit' or be a detailed view.
        // For now, let's make it useful for viewing details if needed, or simply redirect to edit.
        // return view('admin.exams.show', compact('exam'));
        return redirect()->route('admin.exams.edit', $exam);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Exam $exam)
    {
        $students = User::where('role', 'student')->orderBy('name')->get();
        $exam->load('allocatedStudents'); // Eager load currently allocated students
        return view('admin.exams.edit', compact('exam', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Exam $exam)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'duration' => 'required|integer|min:1',
            'status' => ['required', Rule::in(['draft', 'published', 'archived'])],
            'students' => 'nullable|array', // Validate that students is an array if present
            'students.*' => 'exists:users,id', // Validate that each student ID exists in the users table
        ]);

        $exam->update($request->only(['title', 'description', 'duration', 'status']));

        // Sync the allocated students
        if ($request->has('students')) {
            $exam->allocatedStudents()->sync($request->input('students', []));
        } else {
            // If no students are selected, detach all
            $exam->allocatedStudents()->sync([]);
        }

        return redirect()->route('admin.exams.index')->with('success', 'Exam updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Exam $exam)
    {
        // Add any checks here, e.g., if exam has submissions, prevent deletion or soft delete.
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Exam deleted successfully.');
    }
}

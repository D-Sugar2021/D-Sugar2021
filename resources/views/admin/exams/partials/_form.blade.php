@csrf
<div class="mb-4">
    <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Title</label>
    <input type="text" name="title" id="title" value="{{ old('title', $exam->title ?? '') }}" required
           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
    @error('title')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Description</label>
    <textarea name="description" id="description" rows="4"
              class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">{{ old('description', $exam->description ?? '') }}</textarea>
    @error('description')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="duration" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Duration (minutes)</label>
    <input type="number" name="duration" id="duration" value="{{ old('duration', $exam->duration ?? 60) }}" required min="1"
           class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
    @error('duration')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

<div class="mb-4">
    <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
    <select name="status" id="status" required
            class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
        @php $currentStatus = old('status', $exam->status ?? 'draft'); @endphp
        <option value="draft" @if($currentStatus == 'draft') selected @endif>Draft</option>
        <option value="published" @if($currentStatus == 'published') selected @endif>Published</option>
        <option value="archived" @if($currentStatus == 'archived') selected @endif>Archived</option>
    </select>
    @error('status')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>

{{-- This section will only be shown on the edit page where $students is passed --}}
@if (isset($students))
<div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
    <label for="students" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Allocate Students</label>
    <p class="text-xs text-gray-500 mb-2">Select one or more students to assign to this exam. Use Ctrl+Click (or Cmd+Click on Mac) to select multiple students.</p>
    <select name="students[]" id="students" multiple
            class="mt-1 block w-full h-60 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
        @php
            $allocatedStudentIds = $exam->allocatedStudents->pluck('id')->toArray();
        @endphp
        @foreach($students as $student)
            <option value="{{ $student->id }}" @if(in_array($student->id, $allocatedStudentIds)) selected @endif>
                {{ $student->name }} ({{ $student->username ?? $student->email }})
            </option>
        @endforeach
    </select>
    @error('students')
        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
    @enderror
</div>
@endif

<div class="mt-8">
    <button type="submit"
            class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
        {{ $submitButtonText ?? 'Save Exam' }}
    </button>
    <a href="{{ route('admin.exams.index') }}" class="ml-2 inline-flex justify-center py-2 px-4 border border-gray-300 dark:border-gray-500 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
        Cancel
    </a>
</div>

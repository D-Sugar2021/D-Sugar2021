<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Result for: ') }} {{ $attempt->exam->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 md:p-8 text-gray-900 dark:text-gray-100">
                    <div class="flex justify-between items-start mb-6">
                        <div>
                            <h3 class="text-2xl md:text-3xl font-bold text-gray-800 dark:text-gray-100">{{ $attempt->exam->title }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                Attempted on: {{ $attempt->start_time ? $attempt->start_time->format('M d, Y H:i A') : 'N/A' }}
                            </p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Submitted on: {{ $attempt->end_time ? $attempt->end_time->format('M d, Y H:i A') : ($attempt->updated_at ? $attempt->updated_at->format('M d, Y H:i A') : 'N/A') }}
                            </p>
                        </div>
                        <a href="{{ route('student.results.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Back to All Results</a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</h4>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200 mt-1">
                                <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                    @switch($attempt->status)
                                        @case('submitted') bg-blue-100 text-blue-800 dark:bg-blue-700 dark:text-blue-100 @break
                                        @case('completed') bg-yellow-100 text-yellow-800 dark:bg-yellow-700 dark:text-yellow-100 @break
                                        @case('graded') bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100 @break
                                        @default bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-100 @break
                                    @endswitch">
                                    {{ ucfirst($attempt->status) }}
                                </span>
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Your Score</h4>
                            <p class="text-2xl font-bold text-indigo-600 dark:text-indigo-400 mt-1">
                                {{ $attempt->score ?? 'Pending' }}
                                {{-- @if($attempt->exam->max_score) / {{ $attempt->exam->max_score }} @endif --}}
                            </p>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg shadow">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400">Exam Duration</h4>
                            <p class="text-lg font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ $attempt->exam->duration }} minutes</p>
                        </div>
                    </div>

                    <div class="mt-8">
                        <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Attempt Details</h4>
                        @if($attempt->answers_payload)
                            <div class="prose dark:prose-invert max-w-none p-4 bg-gray-50 dark:bg-gray-700 rounded-md">
                                <h5 class="font-semibold">Submitted Answers (Raw Data):</h5>
                                <pre class="text-xs whitespace-pre-wrap break-all">{{ json_encode(json_decode($attempt->answers_payload), JSON_PRETTY_PRINT) }}</pre>
                                <p class="text-xs italic mt-2">Note: This is a raw view of your saved answers. Detailed question-by-question feedback will be available if implemented by the instructor.</p>
                            </div>
                        @else
                            <p class="text-gray-600 dark:text-gray-400">No detailed answer data available for this attempt, or answers were not saved in this format.</p>
                        @endif
                    </div>

                    {{-- Placeholder for future detailed feedback or question review --}}
                    <div class="mt-10 border-t border-gray-200 dark:border-gray-700 pt-6">
                         <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Feedback</h4>
                         <p class="text-gray-600 dark:text-gray-400">
                            @if($attempt->status === 'graded' && $attempt->score !== null)
                                Your exam has been graded. If your instructor provided specific feedback, it would appear here.
                            @elseif($attempt->status === 'submitted')
                                Your exam has been submitted and is awaiting grading.
                            @else
                                Further details or feedback may become available once the exam is fully processed.
                            @endif
                         </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-student-layout>

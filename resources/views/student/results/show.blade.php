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
                        <h4 class="text-xl font-semibold text-gray-800 dark:text-gray-200 mb-4">Your Answers</h4>
                        <div class="space-y-6">
                            @php
                                // Create a map of student's answers for easy lookup
                                $studentAnswers = $attempt->answers->keyBy('question_id');
                            @endphp

                            @foreach($attempt->exam->questions as $index => $question)
                                <div class="p-4 border rounded-lg
                                    @if(isset($studentAnswers[$question->id]))
                                        @if($question->type === 'multiple_choice' && $studentAnswers[$question->id]->option && $studentAnswers[$question->id]->option->is_correct)
                                            border-green-300 dark:border-green-600 bg-green-50 dark:bg-green-800/20
                                        @else
                                            border-gray-200 dark:border-gray-700
                                        @endif
                                    @else
                                        border-gray-200 dark:border-gray-700
                                    @endif">
                                    <p class="font-semibold text-lg text-gray-900 dark:text-gray-100">Q{{ $index + 1 }}: {{ $question->question_text }}</p>

                                    @if($question->type === 'multiple_choice')
                                        <div class="mt-4 pl-4 space-y-2">
                                            @foreach($question->options as $option)
                                                @php
                                                    $studentAnswerOptionId = $studentAnswers[$question->id]->option_id ?? null;
                                                    $isThisOptionChosen = $studentAnswerOptionId == $option->id;
                                                @endphp
                                                <div class="flex items-center">
                                                    @if($option->is_correct)
                                                        <span class="text-green-500 mr-2">&#10003;</span> <!-- Correct tick -->
                                                    @elseif($isThisOptionChosen && !$option->is_correct)
                                                         <span class="text-red-500 mr-2">&#10007;</span> <!-- Incorrect cross -->
                                                    @else
                                                        <span class="mr-2 text-gray-400">&ndash;</span>
                                                    @endif
                                                    <p class="{{ $isThisOptionChosen ? 'font-bold' : '' }} {{ $option->is_correct ? 'text-green-700 dark:text-green-400' : ($isThisOptionChosen ? 'text-red-700 dark:text-red-400' : 'text-gray-700 dark:text-gray-300') }}">
                                                        {{ $option->option_text }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                        @if(!isset($studentAnswers[$question->id]))
                                            <p class="mt-3 text-sm text-yellow-600 dark:text-yellow-500">Not Answered</p>
                                        @endif
                                    @elseif($question->type === 'essay')
                                        <div class="mt-3 prose dark:prose-invert max-w-none p-3 bg-gray-50 dark:bg-gray-900/50 rounded-md">
                                            <p class="text-sm font-semibold text-gray-600 dark:text-gray-400">Your Answer:</p>
                                            <p>{{ $studentAnswers[$question->id]->answer_text ?? 'Not Answered' }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
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

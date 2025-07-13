<x-admin-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Questions for Exam:') }} "{{ $exam->title }}"
            </h2>
            <a href="{{ route('admin.exams.questions.create', $exam) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 dark:focus:ring-offset-gray-800">
                Create New Question
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    <div class="mb-4">
                        <a href="{{ route('admin.exams.index') }}" class="text-sm text-indigo-600 dark:text-indigo-400 hover:underline">&larr; Back to All Exams</a>
                    </div>

                    @if (session('success'))
                        <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @forelse ($questions as $question)
                        <div class="mt-6 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold text-lg text-gray-900 dark:text-gray-100">Q{{ $loop->iteration }}: {{ $question->question_text }}</p>
                                    <span class="text-xs font-mono px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded">{{ $question->type }}</span>
                                    <span class="text-xs font-mono px-2 py-1 bg-blue-100 dark:bg-blue-700 rounded">{{ $question->points }} point(s)</span>
                                </div>
                                <div class="flex-shrink-0 flex items-center space-x-2">
                                    <a href="{{ route('admin.exams.questions.edit', [$exam, $question]) }}" class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Edit</a>
                                    <form action="{{ route('admin.exams.questions.destroy', [$exam, $question]) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this question?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">Delete</button>
                                    </form>
                                </div>
                            </div>
                            @if($question->type === 'multiple_choice' && $question->options->isNotEmpty())
                                <div class="mt-4 pl-4 space-y-2">
                                    @foreach($question->options as $option)
                                        <p class="{{ $option->is_correct ? 'font-bold text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300' }}">
                                            - {{ $option->option_text }} @if($option->is_correct) (Correct Answer) @endif
                                        </p>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                            No questions found for this exam. <a href="{{ route('admin.exams.questions.create', $exam) }}" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200">Create one now</a>.
                        </p>
                    @endforelse

                    <div class="mt-6">
                        {{ $questions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>

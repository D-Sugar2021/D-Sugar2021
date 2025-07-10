<x-student-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Available Exams') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('error'))
                <div class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg dark:bg-red-200 dark:text-red-800" role="alert">
                    {{ session('error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="mb-4 p-4 text-sm text-green-700 bg-green-100 rounded-lg dark:bg-green-200 dark:text-green-800" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-2xl font-semibold mb-6">Exams Open for You</h3>
                    @if($availableExams->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($availableExams as $exam)
                                <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg shadow">
                                    <h4 class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ $exam->title }}</h4>
                                    <p class="text-gray-600 dark:text-gray-300 mt-2">{{ Str::limit($exam->description, 100) }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-3">
                                        Duration: {{ $exam->duration }} minutes
                                    </p>
                                    <div class="mt-4">
                                        {{-- TODO: Add check if student has already attempted/completed this exam --}}
                                        <a href="{{ route('student.exams.take', $exam) }}"
                                           class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">
                                            Start Exam
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="mt-6">
                            {{ $availableExams->links() }}
                        </div>
                    @else
                        <p class="text-gray-600 dark:text-gray-400">There are currently no exams available for you to take.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-student-layout>

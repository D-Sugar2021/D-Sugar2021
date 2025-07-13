<x-student-layout>
    {{-- This content will be placed into the $slot of the student-layout --}}

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900 dark:text-gray-100">
            <h3 class="text-2xl font-semibold mb-2">Welcome, {{ Auth::user()->name }}!</h3>
            <p class="text-gray-600 dark:text-gray-400">This is your personal dashboard. From here, you can access available exams and view your past results.</p>
        </div>
    </div>

    <!-- Quick Action Cards -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Available Exams Card -->
        <a href="{{ route('student.exams.index') }}" class="block transform hover:scale-105 transition-transform duration-200">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <!-- Heroicon: academic-cap -->
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-lg font-medium text-gray-800 dark:text-gray-100 truncate">
                                    Take an Exam
                                </dt>
                                <dd class="text-sm text-gray-500 dark:text-gray-400">
                                    Browse and start any available exams.
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </a>

        <!-- My Results Card -->
        <a href="{{ route('student.results.index') }}" class="block transform hover:scale-105 transition-transform duration-200">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <!-- Heroicon: collection -->
                            <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-lg font-medium text-gray-800 dark:text-gray-100 truncate">
                                    View My Results
                                </dt>
                                <dd class="text-sm text-gray-500 dark:text-gray-400">
                                    Check your scores and review past attempts.
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</x-student-layout>

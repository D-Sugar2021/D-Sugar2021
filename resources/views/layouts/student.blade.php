<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <!-- Student specific navigation could go here if needed, or rely on the main app navigation -->
                    <!-- For example, a sub-navigation bar: -->
                    <nav class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                        <ul class="flex space-x-4">
                            <li>
                                <a href="{{ route('student.dashboard') }}"
                                   class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('student.dashboard') ? 'font-semibold border-b-2 border-indigo-500' : '' }}">
                                    Dashboard
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('student.exams.index') }}"
                                   class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('student.exams.index') ? 'font-semibold border-b-2 border-indigo-500' : '' }}">
                                    Available Exams
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('student.results.index') }}"
                                   class="text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white {{ request()->routeIs('student.results.index') ? 'font-semibold border-b-2 border-indigo-500' : '' }}">
                                    My Results
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <!-- Page Content for Student -->
                    @if (isset($slot))
                        {{ $slot }}
                    @else
                        <h3 class="text-gray-700 dark:text-gray-200 text-2xl font-medium">Welcome, Student!</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Select an option from the navigation to get started.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Exams</h3>
                        <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $totalExams }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Students</h3>
                        <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $totalStudents }}</p>
                    </div>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Total Exam Attempts</h3>
                        <p class="mt-1 text-3xl font-semibold text-indigo-600 dark:text-indigo-400">{{ $totalAttempts }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Links/Actions -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        <a href="{{ route('admin.exams.index') }}" class="block p-6 bg-indigo-500 hover:bg-indigo-600 text-white rounded-lg shadow-md transition ease-in-out duration-150">
                            <h4 class="text-lg font-semibold">Manage Exams</h4>
                            <p class="text-sm opacity-90">View, create, edit, and delete exams.</p>
                        </a>
                        {{-- Placeholder for Manage Students --}}
                        <a href="#" class="block p-6 bg-blue-500 hover:bg-blue-600 text-white rounded-lg shadow-md transition ease-in-out duration-150 opacity-50 cursor-not-allowed" title="Manage Students (Coming Soon)">
                            <h4 class="text-lg font-semibold">Manage Students</h4>
                            <p class="text-sm opacity-90">View and manage student accounts.</p>
                        </a>
                        {{-- Placeholder for View Results --}}
                        <a href="#" class="block p-6 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-md transition ease-in-out duration-150 opacity-50 cursor-not-allowed" title="View All Results (Coming Soon)">
                            <h4 class="text-lg font-semibold">View Results</h4>
                            <p class="text-sm opacity-90">Review all student exam attempts and scores.</p>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-xl font-semibold mb-4">Recent Exam Attempts</h3>
                    @if($recentAttempts->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Student</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Exam</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Score</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Attempted At</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentAttempts as $attempt)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $attempt->user->name ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">{{ $attempt->exam->title ?? 'N/A' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ ucfirst($attempt->status) }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $attempt->score ?? 'Pending' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">{{ $attempt->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-center text-gray-500 dark:text-gray-400 py-4">No recent exam attempts found.</p>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-admin-layout>

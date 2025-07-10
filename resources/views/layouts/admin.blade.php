<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="flex h-screen bg-gray-100 dark:bg-gray-900">
        <!-- Sidebar -->
        <aside x-data="{ open: true }"
               class="w-64 bg-white dark:bg-gray-800 shadow-md hidden sm:block transition-all duration-300 ease-in-out"
               :class="{'w-64': open, 'w-20': !open}">
            <div class="p-4 flex justify-between items-center">
                <span x-show="open" class="text-lg font-semibold text-gray-700 dark:text-gray-200">Admin Menu</span>
                <button @click="open = !open" class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg x-show="open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    <svg x-show="!open" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
            </div>
            <nav class="mt-4">
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span x-show="open">Dashboard</span>
                </a>
                <a href="{{ route('admin.exams.index') }}"
                   class="mt-2 flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 {{ request()->routeIs('admin.exams.index') || request()->routeIs('admin.exams.create') || request()->routeIs('admin.exams.edit') ? 'bg-gray-200 dark:bg-gray-700' : '' }}">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5V3a2 2 0 012-2h2a2 2 0 012 2v2m-6 9l2 2 4-4"></path></svg>
                    <span x-show="open">Exams</span>
                </a>
                <!-- Add more admin links here: Users, Results, Settings etc. -->
                 <a href="#"
                   class="mt-2 flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span x-show="open">Students</span>
                </a>
                 <a href="#"
                   class="mt-2 flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    <span x-show="open">Results</span>
                </a>
            </nav>
        </aside>

        <!-- Main content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 dark:bg-gray-900">
                <div class="container mx-auto px-6 py-8">
                    @if (isset($slot))
                        {{ $slot }}
                    @else
                        <!-- This is where the content of admin pages will go -->
                        <h3 class="text-gray-700 dark:text-gray-200 text-3xl font-medium">Welcome, Admin!</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">This is your main dashboard content area.</p>
                    @endif
                </div>
            </main>
        </div>
    </div>

    <!-- Responsive Sidebar (for mobile) - This is a simplified example -->
    <!-- You might want a different mobile navigation approach for admin -->
    <div x-data="{ sidebarOpen: false }" class="sm:hidden">
        <div class="fixed inset-0 flex z-40" x-show="sidebarOpen" @click.away="sidebarOpen = false">
            <!-- Sidebar -->
            <aside class="w-64 bg-white dark:bg-gray-800 shadow-md">
                <div class="p-4">
                    <span class="text-lg font-semibold text-gray-700 dark:text-gray-200">Admin Menu</span>
                </div>
                <nav class="mt-4">
                    <a href="{{ route('admin.dashboard') }}" class="block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">Dashboard</a>
                    <a href="{{ route('admin.exams.index') }}" class="mt-2 block px-4 py-2 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700">Exams</a>
                    <!-- Add more links -->
                </nav>
            </aside>
        </div>
        <!-- Button to toggle mobile sidebar can be placed in the app.blade.php or here if specific -->
    </div>

</x-app-layout>

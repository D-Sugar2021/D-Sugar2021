<x-student-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $exam->title }}
            </h2>
            <!-- Countdown Timer Display is now part of the larger x-data scope -->
            <div class="text-right">
                <span class="text-lg font-medium text-red-600 dark:text-red-400" x-show="secondsRemaining > 0">
                    Time Remaining: <span x-text="timeRemaining"></span>
                </span>
                <span class="text-lg font-medium text-red-600 dark:text-red-400" x-show="secondsRemaining <= 0 && totalSeconds > 0">
                    Time Up!
                </span>
                 <span class="text-lg font-medium text-gray-600 dark:text-gray-400" x-show="totalSeconds <= 0">
                    No Time Limit
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-12" x-data="examTimer({{ $initialSecondsRemaining }})" x-init="init()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <form id="examForm" method="POST" action="{{ route('student.exams.submit', $exam) }}" @submit.prevent="submitForm">
                @csrf
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-6 prose dark:prose-invert max-w-none">
                            <h3 class="text-2xl font-semibold">{{ $exam->title }}</h3>
                            @if($exam->description)
                                <p class="text-gray-700 dark:text-gray-300">{{ $exam->description }}</p>
                            @endif
                            <p class="text-sm text-gray-600 dark:text-gray-400">Duration: {{ $exam->duration }} minutes.</p>
                            <p class="text-sm text-yellow-600 dark:text-yellow-400">Please do not refresh the page during the exam. Your progress will be autosaved periodically.</p>
                        </div>

                        <!-- Questions Area -->
                        <div class="space-y-8">
                            @if($questions->count() > 0)
                                @foreach($questions as $index => $question)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg" id="question-{{ $question->id }}">
                                    <div class="flex justify-between items-baseline">
                                        <h4 class="font-semibold text-lg">{{ $index + 1 }}. {{ $question->question_text }}</h4>
                                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">({{ $question->points }} point{{ $question->points > 1 ? 's' : '' }})</span>
                                    </div>

                                    <div class="mt-4">
                                        @if($question->type === 'multiple_choice')
                                            <div class="space-y-3">
                                                @foreach($question->options as $option)
                                                <label class="flex items-center p-3 rounded-md border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                                    <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" class="form-radio h-5 w-5 text-indigo-600 dark:bg-gray-900 border-gray-300 dark:border-gray-600 focus:ring-indigo-500">
                                                    <span class="ml-3 text-gray-700 dark:text-gray-300">{{ $option->option_text }}</span>
                                                </label>
                                                @endforeach
                                            </div>
                                        @elseif($question->type === 'essay')
                                            <textarea name="answers[{{ $question->id }}]" rows="6" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-gray-200" placeholder="Your answer..."></textarea>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            @else
                                <div class="text-center py-12 text-gray-500 dark:text-gray-400">
                                    <p class="text-lg font-medium">This exam currently has no questions.</p>
                                    <p class="text-sm">Please contact your administrator.</p>
                                </div>
                            @endif
                        </div>
                        <!-- End Questions Area -->
                    </div>
                </div>

                <!-- Submit Button and Autosave Status -->
                <div class="mt-8 flex justify-between items-center">
                    <div>
                        <button type="submit" <!-- Changed from @click to type=submit, form has @submit.prevent -->
                                :disabled="isSubmitting"
                                class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-md shadow-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                                :class="{ 'opacity-50 cursor-not-allowed': isSubmitting }">
                            <span x-show="!isSubmitting">Submit Exam</span>
                            <span x-show="isSubmitting">Submitting...</span>
                        </button>
                    </div>
                    <div class="text-sm text-gray-600 dark:text-gray-300" x-show="lastSaveStatus" x-text="lastSaveStatus" style="display:none;"></div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function examTimer(initialSeconds) {
            return {
                examId: {{ $exam->id }},
                autosaveInterval: 30000, // 30 seconds
                autosaveTimerId: null,
                isSubmitting: false,
                lastSaveStatus: '',

                totalSeconds: initialSeconds,
                secondsRemaining: initialSeconds, // Initialize here
                intervalId: null,
                timeRemaining: '',

                init() {
                    // this.secondsRemaining = this.totalSeconds; // Already done above
                    this.formatTime();
                    if (this.secondsRemaining > 0) {
                        this.startTimer();
                        this.startAutosaveTimer();
                    } else {
                        this.autoSubmit(); // If loaded with 0 seconds, submit.
                    }
                    window.onbeforeunload = () => {
                        if (!this.isSubmitting && this.secondsRemaining > 0) {
                            return "Are you sure you want to leave? Your exam progress might not be saved if not autosaved recently.";
                        }
                    };
                },

                startTimer() {
                    if (this.intervalId) clearInterval(this.intervalId);
                    this.intervalId = setInterval(() => {
                        if (this.secondsRemaining > 0) {
                            this.secondsRemaining--;
                            this.formatTime();
                        }
                        if (this.secondsRemaining <= 0) { // Check strictly less than or equal
                            this.stopTimer();
                            this.autoSubmit();
                        }
                    }, 1000);
                },

                stopTimer() {
                    clearInterval(this.intervalId);
                    this.stopAutosaveTimer();
                },

                formatTime() {
                    const hours = Math.floor(this.secondsRemaining / 3600);
                    const minutes = Math.floor((this.secondsRemaining % 3600) / 60);
                    const seconds = this.secondsRemaining % 60;
                    this.timeRemaining = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                },

                submitForm() {
                    if (this.isSubmitting) return;
                    this.isSubmitting = true;
                    this.stopTimer();
                    window.onbeforeunload = null;
                    // Consider one final autosave attempt *before* submitting the form.
                    // await this.triggerAutosave(true); // pass a flag if it's a final save
                    document.getElementById('examForm').submit();
                },

                autoSubmit() {
                    // alert('Time is up! Submitting your exam automatically.'); // Alert can be annoying if page reloaded at 0
                    console.log('Auto-submitting due to time up...');
                    this.submitForm();
                },

                manualSubmit() {
                    console.log('Manual submit clicked...');
                    this.submitForm();
                },

                startAutosaveTimer() {
                    if (this.autosaveTimerId) clearInterval(this.autosaveTimerId);
                    this.autosaveTimerId = setInterval(() => {
                        this.triggerAutosave();
                    }, this.autosaveInterval);
                },

                stopAutosaveTimer() {
                    clearInterval(this.autosaveTimerId);
                },

                getFormData() {
                    const formData = new FormData(document.getElementById('examForm'));
                    const answers = {};
                    for (let [key, value] of formData.entries()) {
                        if (key.startsWith('answers[')) {
                            answers[key] = value;
                        }
                    }
                    return answers;
                },

                async triggerAutosave(isFinalSave = false) {
                    if (this.isSubmitting && !isFinalSave) return; // Allow final save even if submitting
                    if (this.secondsRemaining <= 0 && !isFinalSave) return;

                    const answers = this.getFormData();
                    if (Object.keys(answers).length === 0) {
                        if (!isFinalSave) this.lastSaveStatus = 'No answers to save yet.';
                        return;
                    }

                    if (!isFinalSave) this.lastSaveStatus = 'Saving...';
                    try {
                        const response = await fetch(`{{ route('student.exams.autosave', $exam) }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ answers: answers })
                        });
                        const data = await response.json();
                        if (response.ok && data.status === 'success') {
                            if (!isFinalSave) this.lastSaveStatus = `Last saved: ${new Date().toLocaleTimeString()}`;
                        } else {
                            if (!isFinalSave) this.lastSaveStatus = `Save failed: ${data.message || 'Unknown error'}`;
                        }
                    } catch (error) {
                        console.error('Autosave error:', error);
                        if (!isFinalSave) this.lastSaveStatus = 'Save failed: Network error.';
                    }
                }
            };
        }
    </script>
</x-student-layout>

<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Question for Exam:') }} "{{ $exam->title }}"
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 dark:bg-gray-800 dark:border-gray-700" x-data="questionForm(@json($question->options), '{{ $question->type }}')">
                    <form method="POST" action="{{ route('admin.exams.questions.update', [$exam, $question]) }}">
                        @csrf
                        @method('PUT')
                        {{-- Question Details --}}
                        <div class="mb-4">
                            <label for="question_text" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question Text</label>
                            <textarea name="question_text" id="question_text" rows="4" required class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">{{ old('question_text', $question->question_text) }}</textarea>
                            @error('question_text') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Question Type</label>
                                <input type="text" id="type" name="type" value="{{ $question->type }}" readonly class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm sm:text-sm dark:bg-gray-800 dark:text-gray-400 cursor-not-allowed" title="Question type cannot be changed.">
                                <p class="text-xs text-gray-500 mt-1">Type cannot be changed after creation.</p>
                            </div>
                            <div>
                                <label for="points" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Points</label>
                                <input type="number" name="points" id="points" value="{{ old('points', $question->points) }}" required min="1" class="mt-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:text-gray-200">
                                @error('points') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- Options for Multiple Choice --}}
                        <div x-show="questionType === 'multiple_choice'" class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">Options</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Update options and select the correct one.</p>

                            <div id="options-container" class="space-y-3">
                                <template x-for="(option, index) in options" :key="index">
                                    <div class="flex items-center space-x-3 p-2 rounded-md bg-gray-50 dark:bg-gray-700/50">
                                        <input type="radio" name="correct_option" :value="index" :checked="option.is_correct" class="form-radio h-5 w-5 text-indigo-600 focus:ring-indigo-500" required>
                                        <input type="text" :name="'options[' + index + '][text]'" x-model="option.option_text" placeholder="Option text" class="flex-1 block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-900 dark:text-gray-200" required>
                                        <button type="button" @click="removeOption(index)" x-show="options.length > 2" class="p-2 text-red-500 hover:text-red-700">&times;</button>
                                    </div>
                                </template>
                            </div>
                             @error('options') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                             @error('correct_option') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror

                            <button type="button" @click="addOption()" class="mt-4 text-sm text-indigo-600 hover:text-indigo-800">+ Add Another Option</button>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <a href="{{ route('admin.exams.questions.index', $exam) }}" class="mr-4 inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-500 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-200 uppercase tracking-widest shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 disabled:opacity-25 transition ease-in-out duration-150">Cancel</a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150">Update Question</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function questionForm(existingOptions = [], questionType = 'multiple_choice') {
            return {
                questionType: questionType,
                options: existingOptions.length > 0 ? existingOptions : [{ text: '' }, { text: '' }],
                addOption() {
                    this.options.push({ option_text: '', is_correct: false });
                },
                removeOption(index) {
                    if (this.options.length > 2) {
                        this.options.splice(index, 1);
                    }
                }
            }
        }
    </script>
</x-admin-layout>

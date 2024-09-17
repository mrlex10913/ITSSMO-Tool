<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2 lg:p-4 bg-gray-100 dark:bg-gray-800">
                <div class="flex justify-between p-2">
                    <div class="flex gap-2 items-center">
                        <h1 class="text-gray-100">Code: <span class="font-bold italic">JHASD-12AKJ-1234</span></h1>
                        <x-button>
                            Generate Code
                        </x-button>
                    </div>
                    <div>
                        <x-button wire:click="createNewQuestions">
                            Add Question's
                        </x-button>
                    </div>

                </div>
                <div class="text-gray-800 dark:text-gray-100">
                    <h1 class="text-2xl font-semibold">Subject: {{$subject->subject}}</h1>
                    <p>Instruction: <span class="italic">Please read carefully and choose the correct answer.</span></p>
                </div>
                <div class="space-y-2">
                    @foreach ($questions as $questionIndex => $question)
                    <div class="max-h-[65vh]">
                        <div class="px-4 py-2 w-full bg-green-400 border border-transparent rounded-md font-semibold text-sm text-white dark:text-gray-800 tracking-widest">
                            <div class="flex justify-between">
                                <h1>{{ $questionIndex + 1 }}. {{ $question->questions }}</h1>
                                <div>
                                    <x-dropdown align="right" width="48">
                                        <x-slot name="trigger">
                                            <button class="flex items-center text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:text-gray-700 focus:border-gray-300 transition duration-150 ease-in-out">
                                                <div class="material-symbols-sharp">more_vert</div>
                                            </button>
                                        </x-slot>

                                        <x-slot name="content" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded shadow-lg z-50">
                                            <x-dropdown-link wire:click="editQuestion({{ $question->id }})">
                                                <div class="flex items-center gap-2 hover:cursor-pointer">
                                                    <span class="material-symbols-sharp">
                                                        edit_square
                                                    </span>
                                                    Edit
                                                </div>
                                            </x-dropdown-link>
                                            <x-dropdown-link wire:click.prevent="confirmDelete({{ $question->id }})">
                                                <div class="flex items-center gap-2 hover:cursor-pointer">
                                                    <span class="material-symbols-sharp">
                                                        delete
                                                    </span>
                                                    Delete
                                                </div>

                                            </x-dropdown-link>
                                        </x-slot>
                                    </x-dropdown>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2 mt-2 ml-2">
                                @foreach ($question->questionChoices as $choiceIndex => $choices)
                                <div class="flex gap-2 items-center justify-between">
                                    @if ($choices->is_correct)
                                        <p class="font-bold text-green-500">{{ chr(65 + $choiceIndex) }}. {{ $choices->choices }} (Correct Answer)</p>
                                    @else
                                        <p>{{ chr(65 + $choiceIndex) }}. {{ $choices->choices }}</p>
                                    @endif
                                </div>
                            @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
                </div>


            </div>
        </div>
    </div>

    {{-- Modal --}}

    <x-dialog-modal wire:model="NewQuestions">
        <x-slot name="title">
            {{ $editMode ? 'Edit Question' : 'Create Question' }}
        </x-slot>
        <x-slot name="content">
            <div class="space-y-2">
                <x-button wire:click="addAnswer" :disabled="$maxAnswersReached">
                    Add Answer
                </x-button>
                @if($maxAnswersReached)
                <p class="text-red-500 text-sm">You have reached the maximum number of answers (6).</p>
                @endif
                <input type="hidden" value="{{$subject->id}}" id="" wire:model="subject_id">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="question_text" value="{{ __('Question') }}" />
                    <x-input id="question_text" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="question_text" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 place-content-center gap-2">
                    @foreach($answers as $index => $answer)
                        <div class="flex gap-2 items-center">
                            <input type="radio" wire:model="correctAnswer" value="{{ $index }}">
                            <div class="flex flex-col">
                                <x-input type="text" class="mt-1 block w-full text-sm" wire:model="answers.{{ $index }}.text"/>
                                @error('answers.'.$index.'.text')
                                    <span class="text-red-500 text-xs italic">{{ $message }}</span>
                                @enderror
                            </div>
                            <x-secondary-button wire:click="removeAnswer({{ $index }})">
                                Remove
                            </x-secondary-button>
                        </div>
                    @endforeach
                    @error('correctAnswer')
                        <p class="text-red-500 text-sm">{{$message}}</p>
                    @enderror
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-button wire:click="{{ $editMode ? 'saveEditedQuestion' : 'saveQuestions' }}">
                    {{ __('Save') }}
                </x-button>
                <x-secondary-button wire:click="$set('NewQuestions', false)" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="confirmDeletion">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <span class="material-symbols-sharp">
                    warning
                </span>
                {{ __('Deletion of Question') }}
            </div>
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete this Questions? Once your question is deleted, all of its resources and data will be permanently deleted.') }}

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('confirmDeletion', false)" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="deleteQuestion({{$confirmDeletion}})" wire:loading.attr="disabled">
                {{ __('Delete Question') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>

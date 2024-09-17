<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[86vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-2 lg:p-4 bg-gray-100 dark:bg-gray-800">
                <div class="flex justify-end p-2">
                    <x-button wire:click="createNewSubject">
                        Add Subject
                    </x-button>
                </div>
                <div class="grid grid-cols-6 gap-2 max-h-[65vh] overflow-auto">
                    @foreach ($subjects as $subject)
                    <div class="px-4 py-2 bg-green-400 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest">
                        <div class="flex justify-center gap-2 flex-col text-center">
                            <span class="material-symbols-sharp">
                                library_books
                            </span>
                            <div class="text-center space-y-2">
                                <h1>{{$subject->subject}}</h1>
                                <x-button wire:click="viewQuestions({{ $subject->id }})">
                                    View
                                </x-button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    {{-- Modal --}}

    <x-dialog-modal wire:model="NewSubject">
        <x-slot name="title">
           Create Subject
        </x-slot>
        <x-slot name="content">
         <div class="space-y-2">
            <div class="col-span-6 sm:col-span-4">
                <x-label for="subject" value="{{ __('Subject Name') }}" />
                <x-input id="subject" type="text" class="mt-1 block w-full" wire:model="subject" />
                <x-input-error for="subject" class="mt-2" />
            </div>
         </div>
        </x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-button wire:click="saveSubject">
                    {{ __('Save') }}
                </x-button>
                <x-secondary-button wire:click="$set('NewSubject', false)" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
</div>

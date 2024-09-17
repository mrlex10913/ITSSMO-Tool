<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
           @include("livewire.assets.hero._hero")

            <div class="p-4">
                <table class="table-auto w-full">
                    <thead class="text-left border-b">
                        <tr>
                            <th>Category</th>
                            <th>Item Model</th>
                            <th>ITSS Serial</th>
                            <th>Purch. Serial</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Assign To</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td>Keyboard</td>
                            <td>A4tech</td>
                            <td>ITSS-KEY213</td>
                            <td>55-00424-2</td>
                            <td>Premise</td>
                            <td>Active</td>
                            <td>Alexander</td>
                        </tr>
                        <tr>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                            <td>Test</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal --}}

    <x-dialog-modal wire:model="NewAssets">
        <x-slot name="title">
          Create New Assets
        </x-slot>
        <x-slot name="content">
            <div class="space-y-2">
                <input type="hidden" value="" id="" wire:model="subject_id">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="question_text" value="{{ __('Category') }}" />
                    <x-input id="question_text" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="question_text" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="question_text" value="{{ __('Item Model') }}" />
                    <x-input id="question_text" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="question_text" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="question_text" value="{{ __('ITSS Serial') }}" />
                    <x-input id="question_text" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="question_text" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="question_text" value="{{ __('Purch. Serial') }}" />
                    <x-input id="question_text" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="question_text" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="question_text" value="{{ __('Location') }}" />
                    <x-input id="question_text" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="question_text" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="question_text" value="{{ __('Status') }}" />
                    <x-input id="question_text" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="question_text" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="question_text" value="{{ __('Asigned To') }}" />
                    <x-input id="question_text" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="question_text" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 place-content-center gap-2">
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-button wire:click="">
                    {{ __('Save') }}
                </x-button>
                <x-secondary-button wire:click="$set('NewAssets', false)" wire:loading.attr="disabled">
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
            <x-secondary-button wire:click="" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="" wire:loading.attr="disabled">
                {{ __('Delete Question') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>

<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
           @include("livewire.assets.hero._hero")

           <div class="mr-4 float-end">
            <x-button wire:click="createNewCategory">
                Add new category
            </x-button>
           </div>
           <div>
                <table class="table-auto w-full">
                    <thead class="border-b">
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        <tr class="border-b">
                            <td>1.</td>
                            <td>Test</td>
                            <td>
                                <button>Update</button>
                                <button>Delete</button>
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td>1.</td>
                            <td>Test</td>
                            <td>
                                <button>Update</button>
                                <button>Delete</button>
                            </td>
                        </tr>
                        <tr class="border-b">
                            <td>1.</td>
                            <td>Test</td>
                            <td>
                                <button>Update</button>
                                <button>Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

           </div>
        </div>
    </div>

    {{-- Modal --}}

    <x-dialog-modal wire:model="NewCategory">
        <x-slot name="title">
          Create New Category
        </x-slot>
        <x-slot name="content">
            <div class="space-y-2">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="category_name" value="{{ __('Name') }}" />
                    <x-input id="category_name" type="text" class="mt-1 block w-full" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 place-content-center gap-2">
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-button wire:click="saveCategory">
                    {{ __('Save') }}
                </x-button>
                <x-secondary-button wire:click="$set('NewCategory', false)" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>

    {{-- <x-dialog-modal wire:model="confirmDeletion">
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
    </x-dialog-modal> --}}


</div>

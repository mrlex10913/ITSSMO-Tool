<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[75vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
           @include("livewire.borrowers.hero.herobrf")

           <div class="mr-2 mb-2 flex justify-end gap-2">
            <x-input type="search" wire:model.live.debounce.300ms="search" class="w-72 rounded-3xl" placeholder="Search value"/>
            <x-button wire:click="createNewCategory">
                Add new category
            </x-button>
           </div>
           <div class="p-4 max-h-[60vh] overflow-y-auto">
                <table class="table-auto w-full">
                    <thead class="border-b">
                        <tr>
                            <th>No.</th>
                            <th>Doc.Tracker</th>
                            <th>Name</th>
                            <th>Event</th>
                            <th>Location</th>
                            <th>Item</th>
                            <th>Received By</th>
                        </tr>
                    </thead>
                    <tbody class="text-center">
                        @foreach ($brfLogs as $brf)
                        <tr class="border-b border-gray-500">
                            <td>{{$loop->iteration}}</td>
                            <td>{{$brf->doc_tracker}}</td>
                            <td>{{$brf->name}}</td>
                            <td>{{$brf->event}}</td>
                            <td>{{$brf->location}}</td>
                            <td>
                                @foreach ($brf->itemBorrow as $item)
                                    <li>{{ $item->assetCategory->name }}</li>
                                @endforeach
                            </td>
                            <td>{{$brf->receivedby}}</td>
                            <td class="p-2">
                                <x-button wire:click="">Update</x-button>
                                <x-secondary-button wire:click="">Delete</x-secondary-button>
                            </td>
                        </tr>
                        @endforeach
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
                <x-button wire:click="">
                    {{ __('Save') }}
                </x-button>
                <x-secondary-button wire:click="$set('NewCategory', false)" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="DeleteCategory">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <span class="material-symbols-sharp">
                    warning
                </span>
                {{ __('Deletion of Category') }}
            </div>
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete this Category? Once your category is deleted, all of its resources and data will be permanently deleted.') }}

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('DeleteCategory', false)" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="deleteCategory" wire:loading.attr="disabled">
                {{ __('Delete Question') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>


</div>


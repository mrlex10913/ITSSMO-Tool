<div class="overflow-y-auto bg-gray-100 dark:bg-gray-900">
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-4">Category Record</h2>
        @include('livewire.assets.hero._hero')
        <div class="flex justify-between items-center mb-4">
            <input type="search" wire:model.live.debounce.300ms="search" placeholder="Search Value" class="px-3 py-2 bg-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button wire:click="createNewCategory" class="bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-1 focus:ring-green-500 focus:ring-opacity-50 px-4 py-2">
                ADD NEW CATEGORY
            </button>
        </div>
    </div>
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">No.</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($categories as $category)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$loop->iteration}}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$category->name}}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        <button wire:click="updateCategoryModal({{$category->id}})">
                            <span class="material-symbols-sharp">
                                update
                            </span>
                        </button>
                        <button wire:click="deleteCategoryModal({{$category->id}})">
                            <span class="material-symbols-sharp">
                                delete
                            </span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$categories->links()}}
    </div>
    {{-- Modal --}}

    <x-dialog-modal wire:model="NewCategory">
        <x-slot name="title">
          {{$editMode ? 'Edit Category' : 'Create New Category'}}
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
                <x-button wire:click="{{ $editMode ? 'updateCategory' : 'saveCategory'}}">
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


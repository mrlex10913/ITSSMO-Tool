<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
           <div class="p-4 max-h-[70vh] overflow-y-auto">
            <h1 class="text-2xl font-bold">Asset Transfer Form</h1>
            <h1 class="mt-2 italic text-sm text-gray-500">Borrower's Data:</h1>
           <hr class="mb-1">
            <div class="flex justify-between">
                <div>
                    <x-label for="id_number" value="{{ __('ID Number / RF-ID') }}" />
                    <x-input id="id_number" type="text" class="mt-2" wire:model="id_number" />
                    <x-input-error for="id_number" class="mt-2" />
                </div>
                <div>
                    <x-label for="doc_tracker" value="{{ __('Doc. Tracker') }}" />
                    <x-input id="doc_tracker" type="text" class="mt-2" wire:model="doc_tracker" />
                    <x-input-error for="doc_tracker" class="mt-2" />
                </div>
            </div>
            <div class="mt-4 flex justify-between">
                <div>
                    <x-label for="category_name" value="{{ __('Name') }}" />
                    <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
                <div>
                    <x-label for="category_name" value="{{ __('Dept.Local # / Mobile #') }}" />
                    <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
                <div>
                    <x-label for="category_name" value="{{ __('Department') }}" />
                    <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
                <div>
                    <x-label for="category_name" value="{{ __('Authorized By:') }}" />
                    <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
            </div>
            <div class="mt-4 flex justify-between">
                <div>
                    <x-label for="category_name" value="{{ __('Date Borrowed') }}" />
                    <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
                <div>
                    <x-label for="category_name" value="{{ __('Date Return') }}" />
                    <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
                <div>
                    <x-label for="category_name" value="{{ __('Location') }}" />
                    <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
                <div>
                    <x-label for="category_name" value="{{ __('Event') }}" />
                    <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                    <x-input-error for="category_name" class="mt-2" />
                </div>
            </div>
            <h1 class="mt-2 italic text-sm text-gray-500">Item List to be borrowed:</h1>
            <hr>
           <table class="w-full text-center mb-2">
            <thead class="">
                <th>Item Name</th>
                <th>Brand</th>
                <th>Serial</th>
                <th>Action</th>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                    <td>
                        <x-input id="item_name" type="text" class="mt-2" wire:model="item_name" />
                        <x-input-error for="item_name" class="mt-2"/>
                    </td>
                </tr>
            </tbody>
           </table>
           <h1 class="mt-2 italic text-sm text-gray-500">Other Data:</h1>
           <hr>
           <div class="mt-4 flex justify-between">
            <div>
                <x-label for="category_name" value="{{ __('Received By:') }}" />
                <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                <x-input-error for="category_name" class="mt-2" />
            </div>
            <div>
                <x-label for="category_name" value="{{ __('Status') }}" />
                <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                <x-input-error for="category_name" class="mt-2" />
            </div>
            <div>
                <x-label for="category_name" value="{{ __('Released By:') }}" />
                <x-input id="category_name" type="text" class="mt-2" wire:model="category_name" />
                <x-input-error for="category_name" class="mt-2" />
            </div>
            <div>
                <x-label for="category_name" value="{{ __('Approved By:') }}" />
                <x-input id="category_name" type="text" class="mt-2" value="Beau Villanueva" wire:model="category_name" />
                <x-input-error for="category_name" class="mt-2" />
            </div>
            </div>
        </div>
            <div class="px-2 mb-2">
                <x-button class="">
                    Save
                </x-button>
            </div>
        </div>
    </div>
</div>


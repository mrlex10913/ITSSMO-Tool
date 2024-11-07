<div class="overflow-y-auto bg-gray-100 dark:bg-gray-900">
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-4">Borrower's Record</h2>
        @include("livewire.borrowers.hero.herobrf")
        <div class="flex justify-between items-center mb-4">
            <input type="search" wire:model.live.debounce.300m="search" placeholder="Search Value" class="px-3 py-2 bg-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            {{-- <button wire:click="createNewAssets" class="bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-1 focus:ring-green-500 focus:ring-opacity-50 px-4 py-2">
                ADD NEW ASSET
            </button> --}}
        </div>
    </div>
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">No.</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Doc.Tracker</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Event</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Location</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Item</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Borrowed Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Return Date</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Received By</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($brfLogs as $brf)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$loop->iteration}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$brf->doc_tracker}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$brf->name}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$brf->event}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$brf->location}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                        @foreach ($brf->itemBorrow as $item)
                            <li class="list-none mb-2"><span class="bg-green-700 text-xs p-1 rounded-lg text-white font-bold">{{ $item->assetCategory->name }}</span>: {{$item->serial}}</li>
                        @endforeach
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$brf->date_to_borrow}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$brf->date_to_return}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$brf->status}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$brf->receivedby}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2" wire:click="updateBorrower({{$brf->id}})"><span class="material-symbols-sharp">
                            update
                            </span></button>
                        <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" wire:click=""><span class="material-symbols-sharp">
                            delete
                            </span></button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{$brfLogs->links()}}
    </div>
    <x-dialog-modal wire:model="updateBorrowed" class="w-[70vw]">
        <x-slot name="title">
        </x-slot>
        <x-slot name="content">
            <div class="grid grid-cols-5 grid-rows-5 gap-4">
                <div class="col-span-2">
                    <x-label for="id_number" value="{{ __('ID Number') }}" />
                    <x-input id="id_number" type="text" class="mt-1 block w-full" wire:model="id_number" readonly/>
                    <x-input-error for="id_number" class="mt-2" />
                </div>
                <div class="col-span-2 col-start-4">
                    <x-label for="doc_tracker" value="{{ __('Doc.Tracker') }}" />
                    <x-input id="doc_tracker" type="text" class="mt-1 block w-full" wire:model="doc_tracker" readonly/>
                    <x-input-error for="doc_tracker" class="mt-2" />
                </div>
                <div class="col-span-5 row-start-2">
                    <div class="flex gap-2">
                        <div>
                            <x-label for="brfLogs_name" value="{{ __('Name') }}" />
                            <x-input id="brfLogs_name" type="text" class="mt-1 block w-full" wire:model="brfLogs_name" readonly/>
                            <x-input-error for="brfLogs_name" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_contact" value="{{ __('Dept.Local # / Mobile #') }}" />
                            <x-input id="brfLogs_contact" type="text" class="mt-1 block w-full" wire:model="brfLogs_contact" readonly/>
                            <x-input-error for="brfLogs_contact" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_department" value="{{ __('Department') }}" />
                            <x-input id="brfLogs_department" type="text" class="mt-1 block w-full" wire:model="brfLogs_department" readonly/>
                            <x-input-error for="brfLogs_department" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_authorizedby" value="{{ __('Authorized By') }}" />
                            <x-input id="brfLogs_authorizedby" type="text" class="mt-1 block w-full" wire:model="brfLogs_authorizedby" readonly/>
                            <x-input-error for="brfLogs_authorizedby" class="mt-2" />
                        </div>
                    </div>
                </div>
                <div class="col-span-5 row-start-3">
                    <div class="flex gap-2">
                        <div>
                            <x-label for="brfLogs_dateborrowed" value="{{ __('Date Borrowed') }}" />
                            <x-input id="brfLogs_dateborrowed" type="date" class="mt-1 block w-full" wire:model="brfLogs_dateborrowed" readonly/>
                            <x-input-error for="brfLogs_dateborrowed" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_datereturn" value="{{ __('Date Return') }}" />
                            <x-input id="brfLogs_datereturn" type="date" class="mt-1 block w-full" wire:model="brfLogs_datereturn" readonly/>
                            <x-input-error for="brfLogs_datereturn" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_location" value="{{ __('Location') }}" />
                            <x-input id="brfLogs_location" type="text" class="mt-1 block w-full" wire:model="brfLogs_location" readonly/>
                            <x-input-error for="brfLogs_location" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_event" value="{{ __('Event') }}" />
                            <x-input id="brfLogs_event" type="text" class="mt-1 block w-full" wire:model="brfLogs_event" readonly/>
                            <x-input-error for="brfLogs_event" class="mt-2" />
                        </div>
                    </div>

                </div>
                <div class="col-span-5 row-start-4">
                    <div class="overflow-x-auto bg-gray-100 dark:bg-gray-800">
                        <table class="min-w-full text-center text-xs whitespace-nowrap">
                            <thead class="uppercase tracking-wider border-b-2 dark:border-neutral-600">
                                <tr>
                                    <th scope="col" class="">Item Name</th>
                                    <th scope="col" class="">Serial</th>
                                    <th scope="col" class="">Brand</th>
                                    <th scope="col" class="">Released Remarks</th>
                                    <th scope="col" class="">Return Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($brfLogs_items as $index => $item)
                                <tr class="border-b dark:border-neutral-600">
                                    <th scope="row" class="">
                                        {{$item['asset_category_name']}}
                                    </th>
                                    <td class="">{{$item['serial']}}</td>
                                    <td class="">{{$item['brand']}}</td>
                                    <td class="">{{$item['remarks']}}</td>
                                    <td class="p-2">
                                        <input
                                        wire:model="brfLogs_returnremarks.{{ $index }}.logs"
                                        type="text"
                                        class="px-2 py-1 text-base border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                    />
                                    <x-input-error for="brfLogs_returnremarks.{{ $index }}.logs" class="mt-2"/>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-span-5 row-start-5">
                    <div class="flex gap-2">
                        <div>
                            <x-label for="brfLogs_receivedby" value="{{ __('Received By:') }}" />
                            <x-input id="brfLogs_receivedby" type="text" class="mt-1 block w-full" wire:model="brfLogs_receivedby" readonly/>
                            <x-input-error for="brfLogs_receivedby" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_releasecheckedby" value="{{ __('Released/Checked By') }}" />
                            <x-input id="brfLogs_releasecheckedby" type="text" class="mt-1 block w-full" wire:model="brfLogs_releasecheckedby" readonly/>
                            <x-input-error for="brfLogs_releasecheckedby" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_notedby" value="{{ __('Noted By') }}" />
                            <x-input id="brfLogs_notedby" type="text" class="mt-1 block w-full" wire:model="brfLogs_notedby" readonly/>
                            <x-input-error for="brfLogs_notedby" class="mt-2" />
                        </div>
                    </div>

                </div>
                <div class="col-span-5 row-start-6">
                    <div class="flex gap-2">
                        <div>
                            <x-label for="brfLogs_retrunedBy" value="{{ __('Return By:') }}" />
                            <x-input id="brfLogs_retrunedBy" type="text" class="mt-1 block w-full" wire:model="brfLogs_retrunedBy" />
                            <x-input-error for="brfLogs_retrunedBy" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_status" value="{{ __('Status') }}" />
                            <select wire:model="brfLogs_status" id="" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-48 mt-1">
                                <option value="Borrowed">Borrowed</option>
                                <option value="Return">Return</option>
                            </select>
                            <x-input-error for="brfLogs_status" class="mt-2" />
                        </div>
                        <div>
                            <x-label for="brfLogs_receivedcheckedby" value="{{ __('Received/Checked By') }}" />
                            <x-input id="brfLogs_receivedcheckedby" type="text" class="mt-1 block w-full" wire:model="brfLogs_receivedcheckedby" readonly/>
                            <x-input-error for="brfLogs_receivedcheckedby" class="mt-2" />
                        </div>
                    </div>

                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-button wire:click="submitUpdatedBorrowers">
                    {{ __('Save') }}
                </x-button>
                <x-secondary-button wire:click="$set('updateBorrowed', false)" wire:loading.attr="disabled">
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


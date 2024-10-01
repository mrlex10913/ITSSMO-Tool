<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
           <div class="p-4 max-h-[70vh] overflow-y-auto">
                <h1 class="text-2xl font-bold">Borrower's Form</h1>
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
                        <x-input id="doc_tracker" type="text" class="mt-2" wire:model="doc_tracker" readonly/>
                        <x-input-error for="doc_tracker" class="mt-2"/>
                    </div>
                </div>
                <div class="mt-4 flex justify-between">
                    <div>
                        <x-label for="brf_name" value="{{ __('Name') }}" />
                        <x-input id="brf_name" type="text" class="mt-2" wire:model.live="brf_name" />
                        <x-input-error for="brf_name" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="brf_contact" value="{{ __('Dept.Local # / Mobile #') }}" />
                        <x-input id="brf_contact" type="text" class="mt-2" wire:model="brf_contact" />
                        <x-input-error for="brf_contact" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="brf_department" value="{{ __('Department') }}" />
                        <x-input id="brf_department" type="text" class="mt-2" wire:model="brf_department" />
                        <x-input-error for="brf_department" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="brf_authorizedby" value="{{ __('Authorized By:') }}" />
                        <x-input id="brf_authorizedby" type="text" class="mt-2" wire:model="brf_authorizedby" />
                        <x-input-error for="brf_authorizedby" class="mt-2" />
                    </div>
                </div>
                <div class="mt-4 flex justify-between">
                    <div>
                        <x-label for="brf_dateborrowed" value="{{ __('Date Borrowed') }}" />
                        <x-input id="brf_dateborrowed" type="date" class="mt-2 w-48" wire:model="brf_dateborrowed"/>
                        <x-input-error for="brf_dateborrowed" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="brf_datereturn" value="{{ __('Date Return') }}" />
                        <x-input id="brf_datereturn" type="date" class="mt-2 w-48" wire:model="brf_datereturn" />
                        <x-input-error for="brf_datereturn" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="brf_location" value="{{ __('Location') }}" />
                        <x-input id="brf_location" type="text" class="mt-2" wire:model="brf_location" />
                        <x-input-error for="brf_location" class="mt-2" />
                    </div>
                    <div>
                        <x-label for="brf_event" value="{{ __('Event') }}" />
                        <x-input id="brf_event" type="text" class="mt-2" wire:model="brf_event" />
                        <x-input-error for="brf_event" class="mt-2" />
                    </div>
                </div>
                <h1 class="mt-2 italic text-sm text-gray-500">Item List to be borrowed:</h1>
                <hr>
                <table class="w-full text-center mb-2">
                    <thead class="">
                        <th>Item Name</th>
                        <th>Serial</th>
                        <th>Brand</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </thead>
                    <tbody>
                        @foreach ($items as $index => $item)
                        <tr>
                            <td>
                                <select wire:model.live="items.{{$index}}.name" id="items.{{$index}}.name" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-48 mt-2">
                                    <option value="">Select</option>
                                    @foreach ($availableAssets as $asset)
                                        <option value="{{$asset->asset_categories_id}}">{{$asset->assetList->name}}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="items.{{$index}}.name" class="mt-2"/>
                            </td>
                            <td>
                                <select id="items.{{$index}}.serial" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-48 mt-2" wire:model.live="items.{{$index}}.serial" {{ empty($item['name']) ? 'disabled' : '' }}>
                                    <option value="">Select Serial</option>
                                    @if (!empty($availableSerials[$index]))
                                        @foreach ($availableSerials[$index] as $serial)
                                            <option value="{{ $serial }}">{{ $serial }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                <x-input-error for="items.{{$index}}.serial" class="mt-2"/>
                            </td>
                            <td>
                                <x-input id="items.{{$index}}.brand" type="text" class="mt-2" wire:model="items.{{$index}}.brand" readonly/>
                                <x-input-error for="items.{{$index}}.brand" class="mt-2"/>
                            </td>
                            <td>
                                <x-input id="items.{{$index}}.remarks" type="text" class="mt-2" wire:model="items.{{$index}}.remarks" />
                                <x-input-error for="items.{{$index}}.remarks" class="mt-2"/>
                            </td>
                            <td class="text-left">
                            <button wire:click="addItem"><span class="material-symbols-sharp">
                                add_circle
                                </span></button>
                                <button wire:click="removeItem({{$index}})"><span class="material-symbols-sharp">
                                    cancel
                                    </span></button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <h1 class="mt-2 italic text-sm text-gray-500">Other Data:</h1>
                <hr>
                <div class="mt-4 flex justify-between">
                    <div>
                        <x-label for="brf_receivedby" value="{{ __('Received By:') }}" />
                        <x-input id="brf_receivedby" type="text" class="mt-2" wire:model="brf_receivedby" />
                        <x-input-error for="brf_receivedby" class="mt-2" />
                    </div>
                <div>
                    <x-label for="brf_status" value="{{ __('Status') }}" />
                    <x-input id="brf_status" type="text" class="mt-2" wire:model="brf_status" readonly/>
                    <x-input-error for="brf_status" class="mt-2"/>
                </div>
                <div>
                    <x-label for="brf_releasedcheckedby" value="{{ __('Released and Checked By:') }}" />
                    <x-input id="brf_releasedcheckedby" type="text" class="mt-2" wire:model="brf_releasedcheckedby" readonly/>
                    <x-input-error for="brf_releasedcheckedby" class="mt-2"/>
                    {{-- <select wire:model="brf_releasedcheckedby" id="" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-48 mt-2">
                        <option value="">Select</option>
                        <option value="Alexander">Alexander</option>
                        <option value="Angel Bea">Angel Bea</option>
                        <option value="Maria Patricia">Maria Patricia</option>
                        <option value="Alvin">Alvin</option>
                        <option value="Melvin">Melvin</option>
                    </select> --}}
                    {{-- <x-input id="brf_releasedcheckedby" type="text" class="mt-2" wire:model="brf_releasedcheckedby" /> --}}
                    <x-input-error for="brf_releasedcheckedby" class="mt-2" />
                </div>
                <div>
                    <x-label for="brf_notedby" value="{{ __('Noted By:') }}" />
                    <x-input id="brf_notedby" type="text" class="mt-2" wire:model="brf_notedby" readonly/>
                    <x-input-error for="brf_notedby" class="mt-2" />
                </div>
            </div>
            </div>
            <div class="px-2 mb-2">
                <x-button wire:click="saveBorrowers">
                    Save
                </x-button>
            </div>
        </div>
    </div>
</div>

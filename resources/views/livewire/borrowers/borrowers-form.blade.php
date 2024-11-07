<div class="container mx-auto px-4 py-4">
    <h1 class="text-3xl font-bold mb-6 text-center">Borrower's Form</h1>
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 space-y-6">
        <div class="flex items-center mb-4">
            <input type="checkbox" id="link-checkbox" class="form-checkbox h-5 w-5 text-[#3b82f6]" wire:model.live="editMode" autofocus>
            <label for="link-checkbox" class="ml-2 text-sm">Not Existing? (Check to enable edit mode)</label>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div>
                <x-label for="id_number" value="{{ __('ID Number / RF-ID') }}" />
                <x-input id="id_number" type="text" wire:model.live="id_number" autofocus/>
                <x-input-error for="id_number" class="mt-2" />
            </div>
            <div :class="{ 'text-gray-500': !$editMode }">
                <x-label for="id_number" value="{{ __('RF-ID Only') }}" />
                <x-input id="id_number" type="text" wire:model.live="rfid" autofocus :readonly="!$editMode"/>
                <x-input-error for="id_number" class="mt-2" />
            </div>
            <div :class="{ 'text-gray-500': !$editMode }">
                <x-label for="contact_email" value="{{ __('Email') }}" />
                <x-input id="contact_email" type="text" wire:model.live="contact_email" autofocus :readonly="!$editMode"/>
                <x-input-error for="contact_email" class="mt-2" />
            </div>
            <div :class="{ 'text-gray-500': !$editMode }">
                <x-label for="doc_tracker" value="{{ __('Doc. Tracker') }}" />
                <x-input id="doc_tracker" type="text" wire:model.live="doc_tracker" autofocus :readonly="!$editMode"/>
                <x-input-error for="doc_tracker" class="mt-2" />
            </div>
            <div :class="{ 'text-gray-500': !$editMode }">
                <x-label for="brf_name" value="{{ __('Name') }}" />
                <x-input id="brf_name" type="text" wire:model.live="brf_name" autofocus :readonly="!$editMode"/>
                <x-input-error for="brf_name" class="mt-2" />
            </div>
            <div :class="{ 'text-gray-500': !$editMode }">
                <x-label for="brf_contact" value="{{ __('Dept.Local # / Mobile #') }}" />
                <x-input id="brf_contact" type="text" wire:model.live="brf_contact" autofocus :readonly="!$editMode"/>
                <x-input-error for="brf_contact" class="mt-2" />
            </div>
            <div :class="{ 'text-gray-500': !$editMode }">
                <x-label for="brf_department" value="{{ __('Department') }}" />
                <x-input id="brf_department" type="text" wire:model.live="brf_department" autofocus :readonly="!$editMode"/>
                <x-input-error for="brf_department" class="mt-2" />
            </div>
            <div :class="{ 'text-gray-500': !$editMode }">
                <x-label for="brf_authorizedby" value="{{ __('Authorized By:') }}" />
                <x-input id="brf_authorizedby" type="text" wire:model="brf_authorizedby" :readonly="!$editMode"/>
                <x-input-error for="brf_authorizedby" class="mt-2"/>
            </div>
            <div>
                <x-label for="brf_dateborrowed" value="{{ __('Date Borrowed') }}" />
                <x-input id="brf_dateborrowed" type="date" wire:model="brf_dateborrowed"/>
                <x-input-error for="brf_dateborrowed" class="mt-2" />
            </div>
            <div>
                <x-label for="brf_datereturn" value="{{ __('Date Return') }}" />
                <x-input id="brf_datereturn" type="date" wire:model="brf_datereturn" />
                <x-input-error for="brf_datereturn" class="mt-2" />
            </div>
            <div>
                <x-label for="brf_location" value="{{ __('Location') }}" />
                <x-input id="brf_location" type="text" wire:model="brf_location" />
                <x-input-error for="brf_location" class="mt-2" />
            </div>
            <div>
                <x-label for="brf_event" value="{{ __('Event') }}" />
                <x-input id="brf_event" type="text" wire:model="brf_event" />
                <x-input-error for="brf_event" class="mt-2" />
            </div>
        </div>
        <div class="mt-6">
            <h2 class="text-lg font-semibold mb-2">Item List to be borrowed:</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-200 dark:bg-gray-700">
                            <th class="px-4 py-2 text-left">Item Name</th>
                            <th class="px-4 py-2 text-left">Serial</th>
                            <th class="px-4 py-2 text-left">Brand</th>
                            <th class="px-4 py-2 text-left">Remarks</th>
                            <th class="px-4 py-2 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $selectedSerials = collect($items)->pluck('serial')->filter(); // Collect the already selected serials
                        @endphp

                        @foreach ($items as $index => $item)
                        <tr>
                            <td class="px-4 py-2">
                                <select wire:model.live="items.{{$index}}.name" id="items.{{$index}}.name" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]">
                                    <option value="">Select</option>
                                    @foreach ($availableAssets as $asset)
                                        <option value="{{$asset->asset_categories_id}}">{{$asset->assetList->name}}</option>
                                    @endforeach
                                </select>
                                <x-input-error for="items.{{$index}}.name" class="mt-2"/>
                            </td>
                            <td class="px-4 py-2">
                                <select id="items.{{$index}}.serial" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]" wire:model.live="items.{{$index}}.serial" {{ empty($item['name']) ? 'disabled' : '' }}>
                                    <option value="">Select Serial</option>
                                    @if (!empty($availableSerials[$index]))
                                        @foreach ($availableSerials[$index] as $serial)
                                            @if ($serial == $item['serial'])
                                                <!-- Keep the selected serial even if it's used -->
                                                <option value="{{ $serial }}" selected>{{ $serial }}</option>
                                            @elseif (!$selectedSerials->contains($serial))
                                                <!-- Show other serials if not already selected -->
                                                <option value="{{ $serial }}">{{ $serial }}</option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                <x-input-error for="items.{{$index}}.serial" class="mt-2"/>
                            </td>
                            <td class="px-4 py-2">
                                <x-input id="items.{{$index}}.brand" type="text" wire:model="items.{{$index}}.brand" readonly/>
                                <x-input-error for="items.{{$index}}.brand" class="mt-2"/>
                            </td>
                            <td class="px-4 py-2">
                                <x-input id="items.{{$index}}.remarks" type="text" class="mt-2" wire:model="items.{{$index}}.remarks" />
                                <x-input-error for="items.{{$index}}.remarks" class="mt-2"/>
                            </td>
                            <td class="px-4 py-2">
                                <button wire:click="addItem" class="text-green-500 hover:text-green-600 mr-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <button wire:click="removeItem({{$index}})" class="text-red-500 hover:text-red-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-6">
            <div>
                <x-label for="brf_receivedby" value="{{ __('Received By:') }}" />
                <x-input id="brf_receivedby" type="text" wire:model="brf_receivedby" />
                <x-input-error for="brf_receivedby" class="mt-2" />
            </div>
            <div>
                <x-label for="brf_status" value="{{ __('Status') }}" />
                <x-input id="brf_status" type="text" wire:model="brf_status" readonly/>
                <x-input-error for="brf_status" class="mt-2"/>
            </div>
            <div>
                <x-label for="brf_releasedcheckedby" value="{{ __('Released and Checked By:') }}" />
                <x-input id="brf_releasedcheckedby" type="text" wire:model="brf_releasedcheckedby" readonly/>
                <x-input-error for="brf_releasedcheckedby" class="mt-2"/>
            </div>
            <div>
                <x-label for="brf_notedby" value="{{ __('Noted By:') }}" />
                <x-input id="brf_notedby" type="text" wire:model="brf_notedby" readonly/>
                <x-input-error for="brf_notedby" class="mt-2" />
            </div>
        </div>
        <div class="flex justify-end mt-6">
            <button wire:click="saveBorrowers" type="submit" class="px-6 py-2 bg-[#3b82f6] text-white rounded-md hover:bg-[#3b82f6]-dark focus:outline-none focus:ring-2 focus:ring-[#3b82f6] focus:ring-opacity-50">
                Save
            </button>
        </div>
    </div>
</div>

{{-- <div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
           <div class="p-4 max-h-[70vh] overflow-y-auto">
                <h1 class="text-2xl font-bold">Borrower's Form</h1>
                <h1 class="mt-2 italic text-sm text-gray-500">Borrower's Data:</h1>
                <hr class="mb-1">
                <div>
                    <x-label for="id_number" value="{{ __('Not Existing?') }}" />
                    <div class="flex items-center">
                        <input id="link-checkbox" type="checkbox" wire:model.live="editMode" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="link-checkbox" class="ms-2 text-sm font-medium text-gray-900 dark:text-gray-300">Kindly check the box to enable edit mode</label>
                    </div>
                </div>

                <div class="flex justify-between">
                    <div>
                        <x-label for="id_number" value="{{ __('ID Number / RF-ID') }}" />
                        <x-input id="id_number" type="text" class="mt-2" wire:model.live="id_number" autofocus/>
                        <x-input-error for="id_number" class="mt-2" />
                    </div>
                    <div :class="{ 'text-gray-500': !$editMode }">
                        <x-label for="id_number" value="{{ __('RF-ID Only') }}" />
                        <x-input id="id_number" type="text" class="mt-2" wire:model.live="rfid" autofocus :readonly="!$editMode"/>
                        <x-input-error for="id_number" class="mt-2" />
                    </div>

                    <div :class="{ 'text-gray-500': !$editMode }">
                        <x-label for="contact_email" value="{{ __('Email') }}" />
                        <x-input id="contact_email" type="text" class="mt-2" wire:model.live="contact_email" autofocus :readonly="!$editMode"/>
                        <x-input-error for="contact_email" class="mt-2"/>
                    </div>
                    <div>
                        <x-label for="doc_tracker" value="{{ __('Doc. Tracker') }}" />
                        <x-input id="doc_tracker" type="text" class="mt-2" wire:model="doc_tracker" readonly/>
                        <x-input-error for="doc_tracker" class="mt-2"/>
                    </div>
                </div>
                <div class="mt-4 flex justify-between">
                    <div :class="{ 'text-gray-500': !$editMode }">
                        <x-label for="brf_name" value="{{ __('Name') }}" />
                        <x-input id="brf_name" type="text" class="mt-2" wire:model.live="brf_name" :readonly="!$editMode"/>
                        <x-input-error for="brf_name" class="mt-2"/>
                    </div>
                    <div>
                        <x-label for="brf_contact" value="{{ __('Dept.Local # / Mobile #') }}" />
                        <x-input id="brf_contact" type="text" class="mt-2" wire:model="brf_contact" :readonly="!$editMode"/>
                        <x-input-error for="brf_contact" class="mt-2" />
                    </div>
                    <div :class="{ 'text-gray-500': !$editMode }">
                        <x-label for="brf_department" value="{{ __('Department') }}" />
                        <x-input id="brf_department" type="text" class="mt-2" wire:model="brf_department" :readonly="!$editMode"/>
                        <x-input-error for="brf_department" class="mt-2"/>
                    </div>
                    <div :class="{ 'text-gray-500': !$editMode }">
                        <x-label for="brf_authorizedby" value="{{ __('Authorized By:') }}" />
                        <x-input id="brf_authorizedby" type="text" class="mt-2" wire:model="brf_authorizedby" :readonly="!$editMode"/>
                        <x-input-error for="brf_authorizedby" class="mt-2"/>
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
                        @php
                        $selectedSerials = collect($items)->pluck('serial')->filter(); // Collect the already selected serials
                    @endphp

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
                                            @if ($serial == $item['serial'])
                                                <!-- Keep the selected serial even if it's used -->
                                                <option value="{{ $serial }}" selected>{{ $serial }}</option>
                                            @elseif (!$selectedSerials->contains($serial))
                                                <!-- Show other serials if not already selected -->
                                                <option value="{{ $serial }}">{{ $serial }}</option>
                                            @endif
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
</div> --}}

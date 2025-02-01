
<div x-data="{ show: @entangle('showOverlay') }" x-show="show" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-800 overflow-auto">
    <div class="container mx-auto px-4 py-4">
            <h1 class="text-3xl font-bold mb-6 text-center">Borrower's Form</h1>
            <div x-data="{ nextField: @entangle('nextField') }" class="bg-white dark:bg-gray-800 rounded-lg p-6 space-y-6">
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
            <div x-data="{ scannedCode: '' }"
            x-init="$watch('scannedCode', async value => {
                if (value) {
                    await $wire.addScannedItem(value);

                }
            })">
            <div class="mt-6">
                <div class="flex gap-2 items-center">
                    <div>
                        <label>Scan the item here!</label>
                        <x-input type="text" class="w-1/4" x-model="scannedCode" wire:model="scanned" />
                    </div>
                <div>
                        <label for="">Select Category</label>
                        <select class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-[#3b82f6]">
                            <option value="">Select</option>
                            <option value="Consumables">Consumables</option>
                        </select>
                </div>
                </div>
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
                            @foreach ($items as $index => $item)
                                <tr>
                                    <td class="px-4 py-2" wire:model.live="items.{{$index}}.name">
                                        {{ $item['name'] }}
                                    </td>

                                    <td class="px-4 py-2" wire:model.live="items.{{$index}}.serial">
                                        {{ $item['serial'] }}

                                    </td>
                                    <td class="px-4 py-2" wire:model="items.{{$index}}.brand">
                                        {{ $item['brand'] }}

                                    </td>
                                    <td class="px-4 py-2">
                                        <x-input id="items.{{$index}}.remarks" type="text" wire:model="items.{{$index}}.remarks" />
                                        <x-input-error for="items.{{$index}}.remarks" class="mt-2"/>
                                    </td>
                                    <td class="px-4 py-2">
                                        <button wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-600">
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
        </div>
                <div>
                    <x-label for="verfiedPersonel" value="{{ __('Authorized Personel ID Verification') }}" />
                    <x-input id="verfiedPersonel" type="password" wire:model.live="verfiedPersonel" />
                    <x-input-error for="verfiedPersonel" class="mt-2" />
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
            </div>
        </div>
</div>

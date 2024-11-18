<div class="overflow-y-auto bg-gray-100 dark:bg-gray-900">
    <div class="mb-6">
        <h2 class="text-2xl font-bold mb-4">Asset's Record</h2>
        @include('livewire.assets.hero._hero')
        <div class="flex justify-between items-center mb-4">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search Value" class="px-3 py-2 bg-white dark:bg-gray-700 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button wire:click="createNewAssets" class="bg-green-500 text-white rounded hover:bg-green-600 focus:outline-none focus:ring-1 focus:ring-green-500 focus:ring-opacity-50 px-4 py-2">
                ADD NEW ASSET
            </button>
        </div>
    </div>
    <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Category</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Item Barcode</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Item Name</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Item Model</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">ITSS Serial</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Purch. Serial</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Specification</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Location</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Status</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Assign To</th>
                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium uppercase dark:text-gray-300 tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @foreach ($assets as $asset)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->assetList->name}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->item_barcode}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->item_name}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->item_model}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->item_serial_itss}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->item_serial_purch}}
                    </td>
                    <td class="px-6 py-4 whitespace-break-spaces text-sm text-gray-500 dark:text-gray-400">{{$asset->specification}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->location}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->status}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{$asset->assigned_to}}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-2" wire:click="updateAssetId({{$asset->id}})">
                            <span class="material-symbols-sharp">
                                update
                            </span>
                        </button>
                        <button class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" wire:click="deleteAssetId({{$asset->id}})">
                            <span class="material-symbols-sharp">
                                delete
                            </span>
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <x-dialog-modal wire:model="NewAssets">
        <x-slot name="title">
          {{$editMode ? 'Edit Assets' : 'Create New Asset'}}
        </x-slot>
        <x-slot name="content">
            <div class="space-y-2">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="category" value="{{ __('Category') }}" />
                    <select wire:model.live="category" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                            <option value="">SELECT</option>
                        @foreach ($categoryOption as $option)
                            <option value="{{$option->id}}">{{$option->name}}</option>
                        @endforeach
                    </select>
                    <x-input-error for="category" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="item_brand" value="{{ __('Item Brand') }}" />
                    <x-input id="item_brand" type="text" class="mt-1 block w-full" wire:model="item_brand" />
                    <x-input-error for="item_brand" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="item_model" value="{{ __('Item Model') }}" />
                    <x-input id="item_model" type="text" class="mt-1 block w-full" wire:model="item_model" />
                    <x-input-error for="item_model" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="itss_serial" value="{{ __('ITSS Serial') }}" />
                    <x-input id="itss_serial" type="text" class="mt-1 block w-full" wire:model="itss_serial" />
                    <x-input-error for="itss_serial" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="purch_serial" value="{{ __('Purch. Serial') }}" />
                    <x-input id="purch_serial" type="text" class="mt-1 block w-full" wire:model="purch_serial" />
                </div>
                @if ($category == 9 || $category == 12)
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="specification" value="{{ __('Specification') }}" />
                        <textarea wire:model="specification" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm w-full" cols="30" rows="10">
                        </textarea>
                    </div>
                @endif
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="location" value="{{ __('Location') }}" />
                    <select wire:model="location" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">SELECT</option>
                        <option value="ITSSMO(Premise)">ITSSMO(Premise)</option>
                        <option value="Corporate Office">Corporate Office</option>
                        <option value="Registrar's Office">Registrar's Office</option>
                        <option value="CAS/COED Office">CAS/COED Office</option>
                        <option value="CHTM Office">CHTM Office</option>
                        <option value="CICT Office">CICT Office</option>
                        <option value="CCJE Office">CCJE Office</option>
                        <option value="CMBA Office">CMBA Office</option>
                        <option value="GradSchool Office">GradSchool Office</option>
                        <option value="RPO Office">RPO Office</option>
                        <option value="ENGR Faculty Office">ENGR Faculty Office</option>
                        <option value="Scholarship Office">Scholarship Office</option>
                        <option value="SBE Faculty">SBE Faculty</option>
                        <option value="College Faculty">College Faculty</option>
                    </select>
                    <x-input-error for="location" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="status" value="{{ __('Status') }}" />
                    <select wire:model="status" class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                        <option value="">SELECT</option>
                        <option value="Working">Working</option>
                        <option value="Defective">Defective</option>
                        <option value="Borrowed">Borrowed</option>
                        <option value="Available">Available</option>
                        <option value="Asset Transferred">Asset Transferred</option>
                    </select>
                    <x-input-error for="status" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="assign_to" value="{{ __('Asigned To') }}" />
                    <x-input id="assign_to" type="text" class="mt-1 block w-full" wire:model="assign_to" readonly/>
                    <x-input-error for="assign_to" class="mt-2" />
                </div>
                <x-label for="item_barcode" value="{{ __('Barcode Number') }}" />
                <x-input id="item_barcode" type="text" class="mt-1 block w-full" wire:model="item_barcode" />
                <x-input-error for="item_barcode" class="mt-2" />
                {{-- <div x-data="{ barcode: '', triggered: false }"
                x-init="$watch('barcode', value => {
                    $wire.{{ $editMode ? 'updateAsset' : 'saveAsset' }}();
                    })">
                    <x-label for="item_barcode" value="{{ __('Barcode Number') }}" />
                    <x-input id="item_barcode" type="text" class="mt-1 block w-full"
                            x-model="barcode"
                            wire:model="item_barcode" />
                    <x-input-error for="item_barcode" class="mt-2" />
                </div> --}}
                <div class="grid grid-cols-2 place-content-center gap-2">
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-button wire:click="{{$editMode ? 'updateAsset':'saveAsset'}}">
                    {{ __('Save') }}
                </x-button>
                <x-secondary-button wire:click="$set('NewAssets', false)" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>

    <x-dialog-modal wire:model="deleteAsset">
        <x-slot name="title">
            <div class="flex items-center gap-2">
                <span class="material-symbols-sharp">
                    warning
                </span>
                {{ __('Deletion of Question') }}
            </div>
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete this Asset? Once your asset is deleted, all of its resources and data will be permanently deleted.') }}

        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('deleteAsset', false)" wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-secondary-button>

            <x-danger-button class="ms-3" wire:click="deleteToAsset" wire:loading.attr="disabled">
                {{ __('Delete Assets') }}
            </x-danger-button>
        </x-slot>
    </x-dialog-modal>
</div>

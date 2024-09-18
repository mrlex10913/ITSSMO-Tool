<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
           @include("livewire.assets.hero._hero")
           <div class="mr-2 mb-2 flex justify-end gap-2">
            <x-input type="search" wire:model.live.debounce.300ms="search" class="w-72 rounded-3xl" placeholder="Search value"/>
            <x-button wire:click="createNewAssets">
                Add new asset
            </x-button>
           </div>
            <div class="p-4">
                <table class="table-auto w-full">
                    <thead class="text-left border-b">
                        <tr>
                            <th>Category</th>
                            <th>Item Name</th>
                            <th>Item Model</th>
                            <th>ITSS Serial</th>
                            <th>Purch. Serial</th>
                            <th>Specification</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Assign To</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($assets as $asset)
                        <tr class="border-b">
                            <td>{{$asset->assetList->name}}</td>
                            <td>{{$asset->item_name}}</td>
                            <td>{{$asset->item_model}}</td>
                            <td>{{$asset->item_serial_itss}}</td>
                            <td>{{$asset->item_serial_purch}}</td>
                            <td>{{$asset->specification}}</td>
                            <td>{{$asset->location}}</td>
                            <td>{{$asset->status}}</td>
                            <td>{{$asset->assigned_to}}</td>
                            <td class="p-2 text-center">
                                <x-button wire:click="updateAssetId({{$asset->id}})">Update</x-button>
                                <x-secondary-button wire:click="deleteAssetId({{$asset->id}})">Delete</x-secondary-button>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal --}}
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
                @if ($category == 20)
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
                    <x-input id="assign_to" type="text" class="mt-1 block w-full" wire:model="assign_to" />
                    <x-input-error for="assign_to" class="mt-2" />
                </div>
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

    {{-- <x-dialog-modal wire:model="NewAssets">
        <x-slot name="title">
          Create New Assets
        </x-slot>
        <x-slot name="content">
            <div class="space-y-2">
                <input type="hidden" value="" id="" wire:model="subject_id">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="category" value="{{ __('Category') }}" />
                    <x-input id="category" type="text" class="mt-1 block w-full" wire:model="question_text" />
                    <x-input-error for="category" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="item_brand" value="{{ __('Item Model') }}" />
                    <x-input id="item_brand" type="text" class="mt-1 block w-full" wire:model="question_text" />
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
    </x-dialog-modal> --}}

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

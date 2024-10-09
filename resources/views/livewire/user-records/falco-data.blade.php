<div class="py-6">
    <div class="max-w-full mx-auto sm:px-6 lg:px-4 max-h-[80vh] overflow-auto">
        <div class="bg-gray-100 dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg text-gray-100">
           <div class="p-4 max-h-[70vh] overflow-y-auto">
            <h1 class="text-2xl font-bold">Falco Records</h1>
        </div>
        {{-- <form wire:submit="import" enctype="multipart/form-data">
            @csrf
            <div class="space-y-2">
                <div class="col-span-6 sm:col-span-4">
                <input type="file" wire:model.live="file" id="file">
                </div>
                <button type="submit">Test</button>
            </div>
        </form> --}}
        <!-- Table responsive wrapper -->
            <div class="overflow-x-auto bg-white dark:bg-gray-800 h-[70vh] overflow-y-auto p-4">
                <!-- Search input -->
                <div>
                    <div class="relative m-[2px] mb-3 mr-5 float-left">
                        <label for="inputSearch" class="sr-only">Search </label>
                        <input id="inputSearch" type="text" placeholder="Search..." class="block w-64 rounded-lg border dark:border-none dark:bg-neutral-600 py-2 pl-10 pr-4 text-sm focus:border-blue-400 focus:outline-none focus:ring-1 focus:ring-blue-400" />
                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 transform">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 text-neutral-500 dark:text-neutral-200">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </span>
                    </div>
                    <div>
                        <x-button wire:click="addNewStaffRecord">
                            Add new category
                        </x-button>
                        <x-button wire:click="uploadBulkRecord">
                            Bulk
                        </x-button>
                    </div>
                </div>

                <!-- Table -->
                <table class="min-w-full text-left text-xs whitespace-nowrap">
                <!-- Table head -->
                <thead class="uppercase tracking-wider sticky top-0 bg-white dark:bg-gray-800 outline outline-2 outline-neutral-200 dark:outline-neutral-600">
                    <tr>
                    <th scope="col" class="px-6 py-4">
                        ID Number
                    </th>
                    <th scope="col" class="px-6 py-4">
                        Name
                    </th>
                    <th scope="col" class="px-6 py-4">
                        Department
                    </th>
                    <th scope="col" class="px-6 py-4">
                        Status
                    </th>
                    </tr>
                </thead>
                <!-- Table body -->
                <tbody>
                    @foreach ($falcoData as $user)
                        <tr class="border-b dark:border-neutral-600 hover:bg-neutral-100 dark:hover:bg-neutral-600">
                            <th scope="row" class="px-6 py-4">
                                {{$user->id_number}}
                            </th>
                            <td class="px-6 py-4">{{$user->name}}</td>
                            <td class="px-6 py-4">{{$user->department}}</td>
                            <td class="px-6 py-4">
                                <x-button>
                                    Update
                                </x-button>
                            </td>
                        </tr>
                    @endforeach

                </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-dialog-modal wire:model="NewStaffRecord">
        <x-slot name="title">
          {{$editMode ? 'Edit Staff Record' : 'Create New Record'}}
        </x-slot>
        <x-slot name="content">
            <div class="space-y-2">
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="rf_id" value="{{ __('Card No.') }}" />
                    <x-input id="rf_id" type="text" class="mt-1 block w-full" wire:model="rf_id" />
                    <x-input-error for="rf_id" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="id_number" value="{{ __('ID Number') }}" />
                    <x-input id="id_number" type="text" class="mt-1 block w-full" wire:model="id_number" />
                    <x-input-error for="id_number" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="staff_name" value="{{ __('Name') }}" />
                    <x-input id="staff_name" type="text" class="mt-1 block w-full" wire:model="staff_name" />
                    <x-input-error for="staff_name" class="mt-2" />
                </div>
                <div class="col-span-6 sm:col-span-4">
                    <x-label for="staff_department" value="{{ __('Department') }}" />
                    <x-input id="staff_department" type="text" class="mt-1 block w-full" wire:model="staff_department" />
                    <x-input-error for="staff_department" class="mt-2" />
                </div>
                <div class="grid grid-cols-2 place-content-center gap-2">
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-button wire:click="{{ $editMode ? 'updateStaffRecord' : 'saveStaffRecord'}}">
                    {{ __('Save') }}
                </x-button>
                <x-secondary-button wire:click="$set('NewStaffRecord', false)" wire:loading.attr="disabled">
                    {{ __('Close') }}
                </x-secondary-button>
            </div>
        </x-slot>
    </x-dialog-modal>
    <x-dialog-modal wire:model="bulkUpload">
        <x-slot name="title">
          CSV Upload
        </x-slot>

            <x-slot name="content">
                <form wire:submit.prevent="import" method="POST" enctype="multipart/form-data">
                    @csrf
                <div class="space-y-2">
                    <div class="col-span-6 sm:col-span-4">
                        <x-label for="file" value="{{ __('Browse Your file') }}" />
                        <x-input id="file" type="file" class="mt-1 block w-full" wire:model="file" accept=".xls,.xlsx,.csv"/>
                        <x-input-error for="file" class="mt-2" />
                        <small>Note<b>*</b>: File type .xlsx,.csv,.xls</small>
                    </div>
                </div>


            </x-slot>
            <x-slot name="footer">
                <div class="space-x-2">
                    <x-button>
                        {{ __('Save') }}
                    </x-button>
                    <x-secondary-button wire:click="$set('bulkUpload', false)" wire:loading.attr="disabled">
                        {{ __('Close') }}
                    </x-secondary-button>
                </div>
            </x-slot>
        </form>
    </x-dialog-modal>
</div>




<div x-show="$wire.showScanModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;" x-cloak>
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" x-show="$wire.showScanModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"></span>&#8203;

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" x-show="$wire.showScanModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
            <div class="absolute top-0 right-0 pt-4 pr-4">
                <button @click="$wire.showScanModal = false" type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <span class="sr-only">Close</span>
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>

            <div class="px-4 pt-5 pb-4 bg-white sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="flex items-center justify-center flex-shrink-0 w-12 h-12 mx-auto bg-yellow-100 rounded-full sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-qrcode text-yellow-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg font-medium leading-6 text-gray-900">
                            Scan Asset Barcode
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Scan a barcode or property tag to quickly find an asset in the system.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mt-5">
                    <div class="mb-4">
                        <label for="barcode" class="block text-sm font-medium text-gray-700">Enter Barcode/Serial/Tag Number</label>
                        <div class="mt-1 flex rounded-md shadow-sm">
                            <input type="text" id="barcode" wire:model.defer="barcodeInput" class="focus:ring-blue-500 focus:border-blue-500 flex-1 block w-full rounded-md sm:text-sm border-gray-300" placeholder="Scan or type barcode">
                            <button wire:click="processScan($event.target.previousElementSibling.value)" class="ml-3 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <i class="fas fa-search mr-2"></i>
                                Search
                            </button>
                        </div>
                    </div>

                    <div class="relative mt-6">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="w-full border-t border-gray-300"></div>
                        </div>
                        <div class="relative flex justify-center">
                            <span class="px-2 bg-white text-sm text-gray-500">or</span>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-center">
                        <button x-data @click="$refs.fileInput.click()" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-camera mr-2"></i>
                            Scan Using Camera
                        </button>
                        <input type="file" accept="image/*" capture="environment" class="hidden" x-ref="fileInput">
                    </div>

                    <div class="mt-8">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-100 rounded-lg flex items-center justify-center">
                            <div class="text-center p-4">
                                <span class="material-symbols-sharp text-4xl text-gray-400">qr_code_scanner</span>
                                <p class="mt-2 text-sm text-gray-500">Camera feed will appear here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 sm:px-6 sm:flex">
                <button wire:click="$set('showScanModal', false)" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

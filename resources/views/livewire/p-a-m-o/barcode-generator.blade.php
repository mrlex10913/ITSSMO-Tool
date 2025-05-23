<div class="p-6 bg-gray-100 min-h-screen">
    <div class="mx-auto">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Barcode Generator</h1>
            <p class="text-gray-600">Generate and manage unique barcodes for inventory management</p>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <!-- Total Generated -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Generated</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $totalGeneratedCode }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Available (Unused) -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Available Barcodes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $availableBarcodes }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Printed -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Printed Barcodes</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $printedBarcodes }}</p>
                    </div>
                    <div class="p-3 rounded-full bg-purple-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Generate Barcodes Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Generate New Barcodes</h2>
                <div class="flex flex-col sm:flex-row items-end gap-4">
                    <div class="w-full sm:w-auto">
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input
                            type="number"
                            id="quantity"
                            wire:model="qty"
                            min="1"
                            max="1000"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter quantity"
                        >
                        @error('qty') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button
                        wire:click="generateBarcodes"
                        wire:loading.attr="disabled"
                        class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg shadow-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300 transition duration-150 ease-in-out flex items-center justify-center"
                    >
                        <svg wire:loading wire:target="generateBarcodes" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Generate Barcodes
                    </button>
                </div>
                <p class="mt-2 text-sm text-gray-600">Generate unique barcodes for inventory tracking. Each barcode will be assigned a unique identifier.</p>
            </div>

            <!-- Print Barcodes Card -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Print Barcodes</h2>
                <div class="flex flex-col sm:flex-row items-end gap-4">
                    <div class="w-full sm:w-auto">
                        <label for="print-quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity to Print</label>
                        <input
                            type="number"
                            id="print-quantity"
                            wire:model="printQty"
                            min="1"
                            max="{{ $availableBarcodes }}"
                            class="block w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Enter quantity"
                        >
                        @error('printQty') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>
                    <button
                        wire:click="printModalClick"
                        class="px-6 py-2.5 bg-purple-600 text-white font-medium rounded-lg shadow-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-300 transition duration-150 ease-in-out flex items-center justify-center"
                        {{ $availableBarcodes == 0 ? 'disabled' : '' }}
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Print Barcodes
                    </button>
                </div>
                <p class="mt-2 text-sm text-gray-600">Print available barcodes for immediate use. Printed barcodes will be marked as used.</p>
            </div>
        </div>

        <!-- Barcode Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b flex justify-between items-center">
                <h2 class="text-xl font-bold text-gray-900">Barcode List</h2>

                <!-- Search -->
                <div class="relative">
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search barcodes..."
                        class="pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm"
                    >
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No.</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode Number</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Barcode</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Generated Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($barcodeList as $barcode)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $loop->iteration }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $barcode->number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{!! $barcode->barcode_html !!}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($barcode->is_used)
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Printed</span>
                                @else
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Available</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $barcode->created_at->format('M d, Y h:i A') }}</td>
                        </tr>
                        @endforeach

                        @if($barcodeList->isEmpty())
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                No barcodes generated yet. Use the form above to generate barcodes.
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t">
                {{ $barcodeList->links() }}
            </div>
        </div>
    </div>

    <!-- Print Modal -->
    <x-dialog-modal wire:model="printingBarcodeModal">
        <x-slot name="title">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-purple-600 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print Barcodes
            </div>
        </x-slot>
        <x-slot name="content">
            <div class="space-y-4">
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                This will mark the barcodes as printed and they will no longer be available for future print jobs.
                            </p>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="print-quantity-modal" class="block text-sm font-medium text-gray-700">Number of Barcodes to Print</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <input
                            type="number"
                            id="print-quantity-modal"
                            wire:model="printQuantity"
                            min="1"
                            max="{{ $availableBarcodes }}"
                            class="mt-1 block w-full pl-4 pr-12 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        >
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <span class="text-gray-500 sm:text-sm">
                                / {{ $availableBarcodes }}
                            </span>
                        </div>
                    </div>
                    @error('printQuantity') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="print-size" class="block text-sm font-medium text-gray-700">Label Size</label>
                    <select
                        id="print-size"
                        wire:model="printSize"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                        <option value="small">Small (1.0" x 0.5")</option>
                        <option value="medium">Medium (1.5" x 1.0")</option>
                        <option value="large">Large (2.0" x 1.0")</option>
                    </select>
                </div>

                <div>
                    <label for="print-layout" class="block text-sm font-medium text-gray-700">Layout</label>
                    <select
                        id="print-layout"
                        wire:model="printLayout"
                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm rounded-md"
                    >
                        <option value="standard">Standard (Code + Number)</option>
                        <option value="compact">Compact (Code Only)</option>
                        <option value="detailed">Detailed (With Company Logo)</option>
                    </select>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="flex justify-end space-x-3">
                <x-secondary-button wire:click="$set('printingBarcodeModal', false)" class="bg-gray-200 hover:bg-gray-300">
                    Cancel
                </x-secondary-button>
                <x-button
                    wire:click="printBarcodes"
                    wire:loading.attr="disabled"
                    class="bg-purple-600 hover:bg-purple-700 focus:ring-purple-500"
                >
                    <svg wire:loading wire:target="printBarcodes" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Print {{ $printQuantity ?? 0 }} Barcode(s)
                </x-button>
            </div>
        </x-slot>
    </x-dialog-modal>

<!-- Add this script at the end of the file -->
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('printBarcodes', (data) => {
            console.log('Received data:', data);

            // In Livewire 3, the data might be the direct HTML string, not an object
            let htmlContent = typeof data === 'string' ? data : data.html;

            if (!htmlContent) {
                console.error('No HTML content received');
                alert('Error: No printable content received');
                return;
            }

            try {
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                if (!printWindow) {
                    alert('Your browser blocked the popup window. Please allow popups for this site.');
                    return;
                }

                printWindow.document.open();
                printWindow.document.write(htmlContent);
                printWindow.document.close();

                setTimeout(() => {
                    try {
                        printWindow.print();
                        printWindow.onafterprint = function() {
                            printWindow.close();
                        };
                    } catch (printError) {
                        console.error('Print error:', printError);
                    }
                }, 1500);

            } catch (error) {
                console.error('Error in print handler:', error);
            }
        });
    });
</script>
</div>



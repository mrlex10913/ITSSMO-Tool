<div>
    <!-- Modal -->
    <div x-data="{ showModal: @entangle('showModal') }">
        <!-- Backdrop -->
        <div x-show="showModal"
             class="fixed inset-0 bg-black bg-opacity-50 z-50"
             @click="$wire.closeModal()"></div>

        <!-- Modal Content -->
        <div x-show="showModal"
             class="fixed inset-0 z-50 overflow-y-auto"
             @click="$wire.closeModal()">

            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="bg-white rounded-lg shadow-xl max-w-4xl w-full" @click.stop>

                    <!-- Header -->
                    <div class="bg-gradient-to-r from-yellow-600 to-yellow-700 px-6 py-4 rounded-t-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <i class="fas fa-upload text-white text-2xl mr-3"></i>
                                <div>
                                    <h3 class="text-xl font-semibold text-white">Payee Management</h3>
                                    <p class="text-yellow-100 text-sm">Upload Excel file or manage payees</p>
                                </div>
                            </div>
                            <button wire:click="closeModal" class="text-white hover:text-yellow-300">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto">

                        <!-- Flash Messages -->
                        @if (session()->has('success'))
                            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                                {{ session('success') }}
                            </div>
                        @endif

                        @if (session()->has('error'))
                            <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                                {{ session('error') }}
                            </div>
                        @endif

                        <!-- Upload Section -->
                        <div class="mb-6 p-4 border-2 border-dashed border-gray-300 rounded-lg bg-gray-50">
                            <div class="text-center">
                                <i class="fas fa-file-excel text-green-600 text-4xl mb-3"></i>
                                <h4 class="text-lg font-semibold text-gray-700 mb-2">Upload Excel File</h4>

                                <div class="flex items-center justify-center gap-4 mb-4">
                                    <label class="cursor-pointer bg-white border border-yellow-600 text-yellow-600 px-4 py-2 rounded-lg hover:bg-yellow-50">
                                        <i class="fas fa-cloud-upload-alt mr-2"></i>Choose File
                                        <input type="file" wire:model="excelFile" accept=".xlsx,.xls,.csv" class="hidden">
                                    </label>
                                    <button wire:click="downloadTemplate" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                        <i class="fas fa-download mr-2"></i>Download Template
                                    </button>
                                </div>

                                @if($excelFile)
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-600">Selected: {{ $excelFile->getClientOriginalName() }}</p>
                                        <button wire:click="uploadExcel" class="mt-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                                            <i class="fas fa-upload mr-2"></i>Upload File
                                        </button>
                                    </div>
                                @endif

                                @error('excelFile')
                                    <p class="text-red-500 text-sm">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Add/Edit Form -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <h4 class="text-lg font-semibold mb-3">{{ $editingId ? 'Edit' : 'Add' }} Payee</h4>
                            <div class="flex gap-3">
                                <input type="text"
                                       wire:model="payee_name"
                                       placeholder="Enter payee name"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">

                                <button wire:click="savePayee" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                                    <i class="fas fa-save mr-2"></i>{{ $editingId ? 'Update' : 'Add' }}
                                </button>

                                @if($editingId)
                                    <button wire:click="$set('editingId', null); $set('payee_name', '')" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                        Cancel
                                    </button>
                                @endif
                            </div>
                            @error('payee_name')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Search -->
                        <div class="mb-4">
                            <input type="text"
                                   wire:model.live="searchTerm"
                                   placeholder="Search payees..."
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-yellow-500">
                        </div>

                        <!-- Table -->
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Payee Name
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($payees as $payee)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 rounded-full bg-yellow-100 flex items-center justify-center mr-3">
                                                        <span class="text-yellow-600 font-medium">{{ substr($payee->payee_name, 0, 1) }}</span>
                                                    </div>
                                                    <span class="text-sm font-medium text-gray-900">{{ $payee->payee_name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <button wire:click="editPayee({{ $payee->id }})"
                                                            class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50"
                                                            title="Edit">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button wire:click="deletePayee({{ $payee->id }})"
                                                            wire:confirm="Are you sure you want to delete {{ $payee->payee_name }}?"
                                                            class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50"
                                                            title="Delete">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-8 text-center text-gray-500">
                                                No payees found.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $payees->links() }}
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-4 rounded-b-lg">
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-500">
                                Showing {{ $payees->firstItem() ?? 0 }} to {{ $payees->lastItem() ?? 0 }}
                                of {{ $payees->total() }} payee(s)
                            </span>
                            <button wire:click="closeModal" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

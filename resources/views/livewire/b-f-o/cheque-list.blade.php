<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\livewire\b-f-o\cheque-list.blade.php -->
<div class="min-h-screen bg-gray-50 p-6">
    <div class="max-w-7xl mx-auto">

        <!-- Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">
                        <i class="fas fa-list-alt text-blue-600 mr-3"></i>
                        Cheque List
                    </h1>
                    <p class="text-gray-600">Manage and track all issued cheques</p>
                </div>
                <div class="flex flex-wrap gap-3 mt-4 lg:mt-0">
                    <a href="{{ route('bfo.cheque') }}"
                       class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        <i class="fas fa-plus mr-2"></i>New Cheque
                    </a>
                    <button @click="$wire.showFilters = !$wire.showFilters"
                            class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                        <i class="fas fa-filter mr-2"></i>Filters
                    </button>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-6 gap-4 mb-6">
                <div class="bg-blue-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
                    <div class="text-sm text-blue-800">Total</div>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-yellow-600">{{ $stats['draft'] }}</div>
                    <div class="text-sm text-yellow-800">Draft</div>
                </div>
                <div class="bg-green-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-green-600">{{ $stats['printed'] }}</div>
                    <div class="text-sm text-green-800">Printed</div>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-purple-600">{{ $stats['issued'] }}</div>
                    <div class="text-sm text-purple-800">Issued</div>
                </div>
                <div class="bg-indigo-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-indigo-600">{{ $stats['cleared'] }}</div>
                    <div class="text-sm text-indigo-800">Cleared</div>
                </div>
                <div class="bg-red-50 p-4 rounded-lg text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $stats['cancelled'] }}</div>
                    <div class="text-sm text-red-800">Cancelled</div>
                </div>
            </div>

            <!-- Filters Section -->
            <div x-show="$wire.showFilters" x-collapse class="border-t pt-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               placeholder="Cheque number, payee name..."
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select wire:model.live="statusFilter"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                            <option value="all">All Status</option>
                            <option value="draft">Draft</option>
                            <option value="printed">Printed</option>
                            <option value="issued">Issued</option>
                            <option value="cleared">Cleared</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
                        <input type="date"
                               wire:model.live="dateFrom"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
                        <input type="date"
                               wire:model.live="dateTo"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button wire:click="clearFilters"
                            class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                        <i class="fas fa-times mr-1"></i>Clear Filters
                    </button>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif

        <!-- Bulk Actions -->
        @if(count($selectedCheques) > 0)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <span class="text-blue-700 font-medium">
                        {{ count($selectedCheques) }} cheque(s) selected
                    </span>
                    <div class="flex gap-2">
                        <button wire:click="bulkAction('void')"
                                wire:confirm="Are you sure you want to void the selected cheques?"
                                class="px-3 py-1 bg-red-600 text-white rounded text-sm hover:bg-red-700">
                            <i class="fas fa-ban mr-1"></i>Void
                        </button>
                        <button wire:click="bulkAction('mark_issued')"
                                class="px-3 py-1 bg-purple-600 text-white rounded text-sm hover:bg-purple-700">
                            <i class="fas fa-check mr-1"></i>Mark Issued
                        </button>
                        <button wire:click="bulkAction('export')"
                                class="px-3 py-1 bg-green-600 text-white rounded text-sm hover:bg-green-700">
                            <i class="fas fa-download mr-1"></i>Export
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left">
                                <input type="checkbox"
                                       wire:model.live="selectAll"
                                       class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Cheque Details
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payee & Amount
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status & Date
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created By
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($cheques as $cheque)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <input type="checkbox"
                                           wire:model.live="selectedCheques"
                                           value="{{ $cheque->id }}"
                                           class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-money-check text-blue-600"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium text-gray-900">{{ $cheque->cheque_number }}</div>
                                            <div class="text-sm text-gray-500">{{ $cheque->cheque_date->format('M d, Y') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $cheque->payee_name }}</div>
                                    <div class="text-sm text-gray-500">₱{{ $cheque->formatted_amount }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full
                                        @if($cheque->status === 'draft') bg-yellow-100 text-yellow-800
                                        @elseif($cheque->status === 'printed') bg-green-100 text-green-800
                                        @elseif($cheque->status === 'issued') bg-purple-100 text-purple-800
                                        @elseif($cheque->status === 'cleared') bg-indigo-100 text-indigo-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ ucfirst($cheque->status) }}
                                    </span>
                                    @if($cheque->printed_at)
                                        <div class="text-xs text-gray-500 mt-1">
                                            Printed: {{ $cheque->printed_at->format('M d, Y H:i') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $cheque->creator->name ?? 'N/A' }}</div>
                                    <div class="text-sm text-gray-500">{{ $cheque->created_at->format('M d, Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex items-center justify-end space-x-2">
                                        <button wire:click="viewCheque({{ $cheque->id }})"
                                                class="text-blue-600 hover:text-blue-900 p-2 rounded-lg hover:bg-blue-50"
                                                title="View">
                                            <i class="fas fa-eye"></i>
                                        </button>

                                        @if($cheque->status === 'draft')
                                            <button wire:click="editCheque({{ $cheque->id }})"
                                                    class="text-green-600 hover:text-green-900 p-2 rounded-lg hover:bg-green-50"
                                                    title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        @endif

                                        <button wire:click="duplicateCheque({{ $cheque->id }})"
                                                class="text-purple-600 hover:text-purple-900 p-2 rounded-lg hover:bg-purple-50"
                                                title="Duplicate">
                                            <i class="fas fa-copy"></i>
                                        </button>

                                        @if($cheque->status !== 'cancelled')
                                            <button wire:click="voidCheque({{ $cheque->id }})"
                                                    wire:confirm="Are you sure you want to void this cheque?"
                                                    class="text-red-600 hover:text-red-900 p-2 rounded-lg hover:bg-red-50"
                                                    title="Void">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                                    <div class="text-lg font-medium">No cheques found</div>
                                    <div class="text-sm">Create your first cheque to get started</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="bg-white px-6 py-4 border-t">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-500">
                        Showing {{ $cheques->firstItem() ?? 0 }} to {{ $cheques->lastItem() ?? 0 }}
                        of {{ $cheques->total() }} cheque(s)
                    </div>
                    {{ $cheques->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

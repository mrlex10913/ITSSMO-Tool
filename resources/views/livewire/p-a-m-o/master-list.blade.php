<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                    <span class="material-symbols-sharp text-blue-600 mr-3">groups</span>
                    Employee Master List
                </h1>
                <p class="mt-1 text-gray-600">Manage your organization's employee directory</p>
            </div>
            <div class="mt-4 lg:mt-0 flex flex-col sm:flex-row gap-3">
                <!-- Add Employee Button -->
                <button wire:click="$set('showAddModal', true)"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <span class="material-symbols-sharp text-sm mr-2">person_add</span>
                    Add Employee
                </button>
                <!-- Bulk Upload Button -->
                <button wire:click="$set('showBulkModal', true)"
                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                    Bulk Upload
                </button>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center">
                        <span class="material-symbols-sharp text-gray-400 text-sm">search</span>
                    </span>
                    <input wire:model.live.debounce.300ms="search"
                           type="text"
                           placeholder="Search by employee number, name, or department..."
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>

            <!-- Department Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Department</label>
                <select wire:model.live="departmentFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept }}">{{ $dept }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select wire:model.live="statusFilter"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Employee Details
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Department
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Contact
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($employees as $employee)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center">
                                        <span class="text-white font-medium text-sm">
                                            {{ substr($employee->full_name, 0, 2) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $employee->full_name }}</div>
                                        <div class="text-sm text-gray-500">{{ $employee->employee_number }}</div>
                                        @if($employee->position)
                                            <div class="text-xs text-gray-400">{{ $employee->position }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $employee->department }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">
                                    @if($employee->email)
                                        <div class="flex items-center mb-1">
                                            <span class="material-symbols-sharp text-xs text-gray-400 mr-1">email</span>
                                            {{ $employee->email }}
                                        </div>
                                    @endif
                                    @if($employee->phone)
                                        <div class="flex items-center">
                                            <span class="material-symbols-sharp text-xs text-gray-400 mr-1">phone</span>
                                            {{ $employee->phone }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {!! $employee->status_badge !!}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button wire:click="editEmployee({{ $employee->id }})"
                                            class="text-blue-600 hover:text-blue-900 transition-colors">
                                        <span class="material-symbols-sharp text-sm">edit</span>
                                    </button>
                                    <button wire:click="deleteEmployee({{ $employee->id }})"
                                            wire:confirm="Are you sure you want to delete this employee?"
                                            class="text-red-600 hover:text-red-900 transition-colors">
                                        <span class="material-symbols-sharp text-sm">delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center">
                                    <span class="material-symbols-sharp text-gray-400 text-4xl mb-4">group_off</span>
                                    <h3 class="text-lg font-medium text-gray-900 mb-1">No employees found</h3>
                                    <p class="text-gray-500">Get started by adding your first employee.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($employees->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

    <!-- Add Employee Modal -->
    <x-modal wire:model="showAddModal">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Add New Employee</h3>
                <button wire:click="$set('showAddModal', false)" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>

            <form wire:submit="addEmployee">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Employee Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee Number *</label>
                        <input wire:model="employee_number" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('employee_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input wire:model="full_name" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('full_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                        <input wire:model="department" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('department') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <input wire:model="position" type="text"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('position') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input wire:model="email" type="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input wire:model="phone" type="text"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showAddModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Add Employee
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Edit Employee Modal -->
    <x-modal wire:model="showEditModal">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Edit Employee</h3>
                <button wire:click="$set('showEditModal', false)" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>

            <form wire:submit="updateEmployee">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Employee Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Employee Number *</label>
                        <input wire:model="employee_number" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('employee_number') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Full Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input wire:model="full_name" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('full_name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Department -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Department *</label>
                        <input wire:model="department" type="text" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('department') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Position -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                        <input wire:model="position" type="text"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('position') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input wire:model="email" type="email"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Phone -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                        <input wire:model="phone" type="text"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('phone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select wire:model="status"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showEditModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">
                        Update Employee
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <!-- Bulk Upload Modal -->
    <x-enduser-modal wire:model="showBulkModal">
        <div class="p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-medium text-gray-900">Bulk Upload Employees</h3>
                <button wire:click="$set('showBulkModal', false)" class="text-gray-400 hover:text-gray-600">
                    <span class="material-symbols-sharp">close</span>
                </button>
            </div>
            <!-- Download Template Section -->
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200 rounded-lg p-4 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h4 class="font-medium text-blue-900 mb-1">Need a template?</h4>
                        <p class="text-sm text-blue-800">Download our Excel template with sample data to get started quickly.</p>
                    </div>
                    <button wire:click="downloadTemplate"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                        <span class="material-symbols-sharp text-sm mr-2">download</span>
                        Download Template
                    </button>
                </div>
            </div>
            <!-- Instructions -->
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <h4 class="font-medium text-amber-900 mb-2">
                    <span class="material-symbols-sharp text-sm mr-1">info</span>
                    File Format Instructions:
                </h4>
                <div class="text-sm text-amber-800 space-y-2">
                    <p class="mb-2">Supported formats: <strong>Excel (.xlsx, .xls)</strong> or <strong>CSV (.csv)</strong></p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            <span><strong>Employee Number</strong> (required, unique)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            <span><strong>Full Name</strong> (required)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span>
                            <span><strong>Department</strong> (required)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                            <span><strong>Position</strong> (optional)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                            <span><strong>Email</strong> (optional)</span>
                        </div>
                        <div class="flex items-center">
                            <span class="w-2 h-2 bg-gray-400 rounded-full mr-2"></span>
                            <span><strong>Phone</strong> (optional)</span>
                        </div>
                    </div>
                    <div class="mt-3 p-3 bg-amber-100 rounded border-l-4 border-amber-500">
                        <p class="text-xs font-medium">
                            <span class="material-symbols-sharp text-xs mr-1">check_circle</span>
                            Unicode characters (ñ, é, ü, etc.) are fully supported. Employee numbers must be unique.
                        </p>
                    </div>
                </div>
            </div>

            <form wire:submit="processBulkUpload">
                <div class="space-y-4">
                    <!-- File Upload -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            <span class="flex items-center">
                                <span class="material-symbols-sharp text-sm mr-2">upload_file</span>
                                Choose Excel or CSV File
                            </span>
                        </label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-gray-400 transition-colors">
                            <div class="space-y-2 text-center">
                                <span class="material-symbols-sharp text-gray-400 text-3xl">cloud_upload</span>
                                <div class="flex text-sm text-gray-600">
                                    <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input wire:model="bulkFile" id="file-upload" name="file-upload" type="file" accept=".csv,.xlsx,.xls,.txt" class="sr-only">
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">Excel, CSV up to 2MB</p>
                            </div>
                        </div>
                        @if($bulkFile)
                            <div class="mt-2 p-2 bg-green-50 border border-green-200 rounded text-sm text-green-800">
                                <span class="material-symbols-sharp text-xs mr-1">check_circle</span>
                                Selected: {{ $bulkFile->getClientOriginalName() }}
                            </div>
                        @endif
                        @error('bulkFile')
                            <div class="mt-2 p-2 bg-red-50 border border-red-200 rounded text-sm text-red-800">
                                <span class="material-symbols-sharp text-xs mr-1">error</span>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" wire:click="$set('showBulkModal', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                        <span class="flex items-center">
                            <span class="material-symbols-sharp text-sm mr-2">upload</span>
                            Upload Employees
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </x-enduser-modal>
</div>

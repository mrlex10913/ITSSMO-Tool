<?php

namespace App\Livewire\PAMO;

use App\Exports\EmployeeTemplateExport;
use App\Imports\EmployeeImport;
use App\Models\PAMO\MasterList as MasterListModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Layout('layouts.pamo')]
class MasterList extends Component
{
    use WithPagination, WithFileUploads;

    // Modal states
    public $showAddModal = false;
    public $showBulkModal = false;
    public $showEditModal = false;

    // Form fields
    public $employee_number = '';
    public $full_name = '';
    public $department = '';
    public $position = '';
    public $email = '';
    public $phone = '';
    public $status = 'active';

    // Bulk upload
    public $bulkFile;

    // Search and filters
    public $search = '';
    public $departmentFilter = '';
    public $statusFilter = '';

    // Edit mode
    public $editingId;

    protected $paginationTheme = 'tailwind';

    public function rules()
    {
        return [
            'employee_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('master_lists', 'employee_number')->ignore($this->editingId)
            ],
            'full_name' => 'required|string|max:255',
            'department' => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:active,inactive'
        ];
    }

    public function addEmployee()
    {
        $this->validate();

        MasterListModel::create([
            'employee_number' => $this->employee_number,
            'full_name' => $this->full_name,
            'department' => $this->department,
            'position' => $this->position,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status
        ]);

        $this->resetForm();
        $this->showAddModal = false;

        toastr()->success('Employee added successfully!');
    }

    public function editEmployee($id)
    {
        $employee = MasterListModel::findOrFail($id);

        $this->editingId = $id;
        $this->employee_number = $employee->employee_number;
        $this->full_name = $employee->full_name;
        $this->department = $employee->department;
        $this->position = $employee->position;
        $this->email = $employee->email;
        $this->phone = $employee->phone;
        $this->status = $employee->status;

        $this->showEditModal = true;
    }

    public function updateEmployee()
    {
        $this->validate();

        $employee = MasterListModel::findOrFail($this->editingId);
        $employee->update([
            'employee_number' => $this->employee_number,
            'full_name' => $this->full_name,
            'department' => $this->department,
            'position' => $this->position,
            'email' => $this->email,
            'phone' => $this->phone,
            'status' => $this->status
        ]);

        $this->resetForm();
        $this->showEditModal = false;

        toastr()->success('Employee updated successfully!');
    }

    public function deleteEmployee($id)
    {
        MasterListModel::findOrFail($id)->delete();
        toastr()->success('Employee deleted successfully!');
    }

    public function processBulkUpload()
    {
        $this->validate([
            'bulkFile' => 'required|mimes:csv,xlsx,xls,txt|max:2048'
        ]);

        try {
            $import = new EmployeeImport();

            Excel::import($import, $this->bulkFile);

            $stats = $import->getImportStats();

            $this->showBulkModal = false;
            $this->bulkFile = null;

            // Show success message
            if ($stats['imported'] > 0) {
                toastr()->success("Successfully imported {$stats['imported']} employees!");
            }

            // Show errors if any
            if ($stats['errors'] > 0) {
                $errorCount = 0;
                foreach ($import->failures() as $failure) {
                    if ($errorCount >= 5) break; // Limit to 5 error messages

                    $errors = implode(', ', $failure->errors());
                    toastr()->error("Row {$failure->row()}: {$errors}");
                    $errorCount++;
                }

                if ($stats['errors'] > 5) {
                    toastr()->warning("And " . ($stats['errors'] - 5) . " more errors. Please check your file.");
                }
            }

            // Show warning if no data was imported
            if ($stats['imported'] === 0 && $stats['errors'] === 0) {
                toastr()->warning('No valid data found to import. Please check your file format.');
            }

        } catch (\Exception $e) {
            toastr()->error('Error processing file: ' . $e->getMessage());
            Log::error('Bulk upload error: ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
        $this->employee_number = '';
        $this->full_name = '';
        $this->department = '';
        $this->position = '';
        $this->email = '';
        $this->phone = '';
        $this->status = 'active';
        $this->editingId = null;
    }

    public function downloadTemplate()
    {
        return Excel::download(new EmployeeTemplateExport(), 'employee_bulk_upload_template.xlsx');
    }

    public function render()
    {
        $employees = MasterListModel::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('employee_number', 'like', '%' . $this->search . '%')
                      ->orWhere('full_name', 'like', '%' . $this->search . '%')
                      ->orWhere('department', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->departmentFilter, function ($query) {
                $query->where('department', $this->departmentFilter);
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $departments = MasterListModel::distinct()->pluck('department');

        return view('livewire.p-a-m-o.master-list', compact('employees', 'departments'));
    }
}

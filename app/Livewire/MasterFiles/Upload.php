<?php

namespace App\Livewire\MasterFiles;

use App\Models\MasterFiles\MasterFile;
use App\Models\MasterFiles\MasterFileCategory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

class Upload extends Component
{
    use WithFileUploads;

    public $file;
    public $title = '';
    public $description = '';
    public $document_code = '';
    public $category_id = '';
    public $effective_date = '';
    public $expiry_date = '';
    public $review_date = '';
    public $tags = '';
    public $visible_to_departments = [];
    public $is_confidential = false;

    protected $rules = [
        'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:30720', // 30MB max
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'document_code' => 'nullable|string|max:50|unique:master_files,document_code',
        'category_id' => 'required|exists:master_file_categories,id',
        'effective_date' => 'nullable|date',
        'expiry_date' => 'nullable|date|after:effective_date',
        'review_date' => 'nullable|date',
        'tags' => 'nullable|string',
        'visible_to_departments' => 'array',
        'is_confidential' => 'boolean'
    ];

    protected $messages = [
        'file.required' => 'Please select a file to upload.',
        'file.mimes' => 'Only PDF, Word, Excel, and PowerPoint files are allowed.',
        'file.max' => 'File size cannot exceed 10MB.',
        'title.required' => 'Document title is required.',
        'category_id.required' => 'Please select a category.',
        'document_code.unique' => 'This document code already exists.',
        'expiry_date.after' => 'Expiry date must be after the effective date.'
    ];


    public function mount()
    {
        $this->effective_date = now()->format('Y-m-d');
        // Default to user's department
        $this->visible_to_departments = [Auth::user()->department ?? 'ITSS'];
    }


    public function updatedCategoryId()
    {
        // Auto-generate document code if empty
        if (empty($this->document_code) && $this->category_id) {
            $category = MasterFileCategory::find($this->category_id);
            if ($category) {
                $this->generateDocumentCode($category);
            }
        }
    }
    public function removeFile()
    {
        $this->file = null;
    }
    public function resetFields()
    {
        // Reset all form fields to default values
        $this->file = null;
        $this->title = '';
        $this->description = '';
        $this->document_code = '';
        $this->category_id = '';
        $this->effective_date = now()->format('Y-m-d');
        $this->expiry_date = '';
        $this->review_date = '';
        $this->tags = '';
        $this->visible_to_departments = [Auth::user()->department ?? 'ITSS'];
        $this->is_confidential = false;

        // Clear validation errors
        $this->resetErrorBag();

        // Flash message to confirm reset
        flash()->info('Form has been reset to default values.');
    }

    private function generateDocumentCode($category)
    {
        $prefix = strtoupper(substr($category->name, 0, 3));
        $year = now()->format('Y');
        $userDept = Auth::user()->department ?? 'GEN';

        // Find the next sequential number
        $lastCode = MasterFile::where('document_code', 'like', "{$prefix}-{$userDept}-{$year}-%")
            ->orderBy('document_code', 'desc')
            ->first();

        $nextNumber = 1;
        if ($lastCode) {
            $parts = explode('-', $lastCode->document_code);
            if (count($parts) >= 4) {
                $nextNumber = (int) end($parts) + 1;
            }
        }

        $this->document_code = sprintf('%s-%s-%s-%03d', $prefix, $userDept, $year, $nextNumber);
    }

    public function uploadDocument()
    {
        $this->validate();

        try {
            // Store file
            $filePath = $this->file->store('master-files/' . date('Y/m'), 'public');

            // Process tags
            $tags = $this->tags ? array_map('trim', explode(',', $this->tags)) : [];

            // Create file record
            $masterFile = MasterFile::create([
                'category_id' => $this->category_id,
                'title' => $this->title,
                'description' => $this->description,
                'document_code' => $this->document_code ?: null,
                'file_path' => $filePath,
                'original_filename' => $this->file->getClientOriginalName(),
                'file_size' => $this->file->getSize(),
                'mime_type' => $this->file->getMimeType(),
                'version' => '1.0',
                'status' => 'active',
                'effective_date' => $this->effective_date ?: null,
                'expiry_date' => $this->expiry_date ?: null,
                'review_date' => $this->review_date ?: null,
                'tags' => $tags,
                'department' => Auth::user()->department ?? 'ITSS',
                'visible_to_departments' => $this->visible_to_departments,
                'uploaded_by' => Auth::id(),
                'is_confidential' => $this->is_confidential,
                'view_count' => 0,
                'download_count' => 0
            ]);

            // Don't log upload action since it's not allowed by the constraint
            // The upload is already tracked by the 'uploaded_by' field

            session()->flash('success', 'Document uploaded successfully!');
            $this->resetFields();

            // Optionally redirect to the uploaded file's view page
            return redirect()->route('master-file.show', $masterFile);

        } catch (\Exception $e) {
            session()->flash('error', 'Upload failed: ' . $e->getMessage());
        }
    }
    public function render()
    {
        $userDepartment = Auth::user()->department ?? 'ITSS';

        $categories = MasterFileCategory::where('is_active', true)
            ->where(function($query) use ($userDepartment) {
                // Show categories that are either for all departments or specifically for user's department
                $query->whereNull('department')
                      ->orWhere('department', $userDepartment)
                      ->orWhereJsonContains('allowed_departments', $userDepartment);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.master-files.upload', compact('categories'));
    }
}

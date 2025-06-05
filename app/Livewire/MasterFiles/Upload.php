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

     // Version-related properties
    public $is_new_version = false;
    public $parent_file_id = null;
    public $existing_files = [];
    public $selected_existing_file = null;
    public $version_notes = '';

    protected $rules = [
        'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:10240',
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'document_code' => 'nullable|string|max:50',
        'category_id' => 'required|exists:master_file_categories,id',
        'effective_date' => 'nullable|date',
        'expiry_date' => 'nullable|date|after:effective_date',
        'review_date' => 'nullable|date',
        'tags' => 'nullable|string',
        'visible_to_departments' => 'array',
        'is_confidential' => 'boolean',
        'selected_existing_file' => 'required_if:is_new_version,true|exists:master_files,id',
        'version_notes' => 'nullable|string|max:500'
    ];

    protected $messages = [
        'file.required' => 'Please select a file to upload.',
        'file.mimes' => 'Only PDF, Word, Excel, and PowerPoint files are allowed.',
        'file.max' => 'File size cannot exceed 10MB.',
        'title.required' => 'Document title is required.',
        'category_id.required' => 'Please select a category.',
        'selected_existing_file.required_if' => 'Please select the original file to create a new version.',
        'expiry_date.after' => 'Expiry date must be after the effective date.'
    ];


    public function mount($parent_id = null)
    {
        $this->effective_date = now()->format('Y-m-d');
        $this->visible_to_departments = [Auth::user()->department ?? 'ITSS'];

        // If parent_id is provided, we're creating a new version
        if ($parent_id) {
            $this->is_new_version = true;
            $this->selected_existing_file = $parent_id;
            $this->loadExistingFileData($parent_id);
        }

        $this->loadExistingFiles();
    }

    public function updatedIsNewVersion()
    {
        if ($this->is_new_version) {
            $this->loadExistingFiles();
        } else {
            $this->resetToNewFile();
        }
    }

    public function updatedSelectedExistingFile()
    {
        if ($this->selected_existing_file && $this->is_new_version) {
            $this->loadExistingFileData($this->selected_existing_file);
        }
    }

    public function updatedCategoryId()
    {
        if (!$this->is_new_version && empty($this->document_code) && $this->category_id) {
            $category = MasterFileCategory::find($this->category_id);
            if ($category) {
                $this->generateDocumentCode($category);
            }
        }
    }
    public function resetFields()
    {
        $this->file = null;
        $this->is_new_version = false;
        $this->resetToNewFile();
        $this->resetErrorBag();
        session()->flash('info', 'Form has been reset to default values.');
    }
    private function loadExistingFiles()
    {
        $userDepartment = Auth::user()->department ?? 'ITSS';

        $this->existing_files = MasterFile::with('category')
            ->where('status', 'active')
            ->where(function($query) use ($userDepartment) {
                if (!Auth::user()->hasRole(['administrator', 'developer'])) {
                    $query->where('department', $userDepartment)
                          ->orWhereJsonContains('visible_to_departments', $userDepartment);
                }
            })
            ->orderBy('title')
            ->get();
    }
    private function loadExistingFileData($fileId)
    {
        $existingFile = MasterFile::find($fileId);
        if ($existingFile) {
            $this->title = $existingFile->title;
            $this->description = $existingFile->description;
            $this->document_code = $existingFile->document_code;
            $this->category_id = $existingFile->category_id;
            $this->effective_date = $existingFile->effective_date ? $existingFile->effective_date->format('Y-m-d') : now()->format('Y-m-d');
            $this->expiry_date = $existingFile->expiry_date ? $existingFile->expiry_date->format('Y-m-d') : '';
            $this->review_date = $existingFile->review_date ? $existingFile->review_date->format('Y-m-d') : '';
            $this->tags = is_array($existingFile->tags) ? implode(', ', $existingFile->tags) : '';
            $this->visible_to_departments = $existingFile->visible_to_departments ?? [];
            $this->is_confidential = $existingFile->is_confidential;
            $this->parent_file_id = $fileId;
        }
    }
    private function resetToNewFile()
    {
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
        $this->parent_file_id = null;
        $this->selected_existing_file = null;
        $this->version_notes = '';
    }
    public function removeFile()
    {
        $this->file = null;
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
    private function getNextVersion($parentFileId)
    {
        // Get the current latest version across all versions of this document
        $latestVersion = MasterFile::where(function($query) use ($parentFileId) {
            $query->where('parent_file_id', $parentFileId)
                ->orWhere('id', $parentFileId);
        })
        ->orderByRaw('CAST(SUBSTRING(version, 1, CHARINDEX(\'.\', version + \'.\') - 1) AS INT) DESC')
        ->orderByRaw('CAST(SUBSTRING(version, CHARINDEX(\'.\', version + \'.\') + 1, LEN(version)) AS INT) DESC')
        ->first();

        if (!$latestVersion) {
            return '1.0';
        }

        // Parse version (e.g., "1.2" -> major: 1, minor: 2)
        $versionParts = explode('.', $latestVersion->version);
        $major = (int) ($versionParts[0] ?? 1);
        $minor = (int) ($versionParts[1] ?? 0);

        // Increment minor version
        $minor++;

        return "{$major}.{$minor}";
    }

    public function uploadDocument()
{
    $this->validate();

    try {
        // Store file
        $filePath = $this->file->store('master-files/' . date('Y/m'), 'public');

        // Process tags
        $tags = $this->tags ? array_map('trim', explode(',', $this->tags)) : [];

        $data = [
            'category_id' => $this->category_id,
            'title' => $this->title,
            'description' => $this->description,
            'file_path' => $filePath,
            'original_filename' => $this->file->getClientOriginalName(),
            'file_size' => $this->file->getSize(),
            'mime_type' => $this->file->getMimeType(),
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
        ];

        if ($this->is_new_version && $this->selected_existing_file) {
            // Creating a new version
            $parentFile = MasterFile::find($this->selected_existing_file);

            // Determine the actual parent file ID
            $actualParentId = $parentFile->parent_file_id ?: $parentFile->id;

            $data['parent_file_id'] = $actualParentId;
            $data['document_code'] = $parentFile->document_code; // Keep same document code
            $data['version'] = $this->getNextVersion($actualParentId);
            $data['version_notes'] = $this->version_notes;

            // **FIXED**: Archive only the CURRENT active version, not the parent
            // Find the current active version and mark it as superseded
            MasterFile::where(function($query) use ($actualParentId) {
                $query->where('parent_file_id', $actualParentId)
                      ->orWhere('id', $actualParentId);
            })
            ->where('status', 'active')
            ->update(['status' => 'superseded']);

        } else {
            // Creating a new file
            $data['document_code'] = $this->document_code ?: null;
            $data['version'] = '1.0';
            $data['parent_file_id'] = null;
        }

        $masterFile = MasterFile::create($data);

        $message = $this->is_new_version ?
            "New version {$masterFile->version} uploaded successfully!" :
            'Document uploaded successfully!';

        session()->flash('success', $message);
        $this->resetFields();

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
                $query->whereNull('department')
                      ->orWhere('department', $userDepartment)
                      ->orWhereJsonContains('allowed_departments', $userDepartment);
            })
            ->orderBy('name')
            ->get();


        return view('livewire.master-files.upload', compact('categories'));
    }
}

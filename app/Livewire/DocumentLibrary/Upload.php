<?php

namespace App\Livewire\DocumentLibrary;

use App\Models\DocumentLibrary\DocumentFolder;
use App\Models\DocumentLibrary\MasterFile;
use App\Models\DocumentLibrary\MasterFileCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.enduser')]
class Upload extends Component
{
    use WithFileUploads;

    public $file;

    public $title = '';

    public $description = '';

    public $document_code = '';

    public $category_id = '';

    public $effective_date = '';

    public $review_date = '';

    public $tags = '';

    public $visible_to_departments = [];

    public $visible_to_users = [];

    public $user_search = '';

    public $is_confidential = false;

    // Folder selection
    public $folder_id = null;

    public $available_folders = [];

    // Version-related properties
    public $is_new_version = false;

    public $parent_file_id = null;

    public $existing_files = [];

    public $selected_existing_file = null;

    public $version_notes = '';

    public $uploadError = '';

    /**
     * Handle upload errors from Livewire's file upload system
     */
    #[On('upload:errored')]
    public function handleUploadError($name, $errorsInJson, $isMultiple): void
    {
        $errors = json_decode($errorsInJson, true);
        $errorMessage = 'File upload failed. ';

        if (is_array($errors)) {
            foreach ($errors as $error) {
                if (str_contains($error, 'max') || str_contains($error, 'size')) {
                    $errorMessage = 'File size exceeds the limit. Maximum allowed size is 100MB.';
                    break;
                }
                if (str_contains($error, 'mimes') || str_contains($error, 'type')) {
                    $errorMessage = 'Invalid file type. Only PDF, Word, Excel, and PowerPoint files are allowed.';
                    break;
                }
            }
        }

        $this->uploadError = $errorMessage;
        $this->addError('file', $errorMessage);
    }

    protected function rules()
    {
        $rules = [
            'file' => 'required|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:102400',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'document_code' => 'nullable|string|max:50',
            'category_id' => 'required|exists:document_categories,id',
            'effective_date' => 'nullable|date',
            'review_date' => 'nullable|date',
            'tags' => 'nullable|string',
            'visible_to_departments' => 'array',
            'visible_to_users' => 'array',
            'is_confidential' => 'boolean',
            'version_notes' => 'nullable|string|max:500',
        ];

        // Add conditional rule for selected_existing_file
        if ($this->is_new_version === true || $this->is_new_version === 'true') {
            $rules['selected_existing_file'] = 'required|exists:documents,id';
        }

        return $rules;
    }

    protected $messages = [
        'file.required' => 'Please select a file to upload.',
        'file.mimes' => 'Only PDF, Word, Excel, and PowerPoint files are allowed.',
        'file.max' => 'File size cannot exceed 100MB.',
        'title.required' => 'Document title is required.',
        'category_id.required' => 'Please select a category.',
        'selected_existing_file.required' => 'Please select the original file to create a new version.',
    ];

    public function mount($parent_id = null)
    {
        $this->effective_date = now()->format('Y-m-d');
        $this->visible_to_departments = [Auth::user()->department ?? 'ITSS'];

        // Load from query string if present
        if (request()->has('folder')) {
            $this->folder_id = request()->query('folder');
        }

        // If parent_id is provided, we're creating a new version
        if ($parent_id) {
            $this->is_new_version = true;
            $this->selected_existing_file = $parent_id;
            $this->loadExistingFileData($parent_id);
        }

        $this->loadExistingFiles();
        $this->loadAvailableFolders();
    }

    public function loadAvailableFolders()
    {
        $userDepartment = Auth::user()->department ?? 'ITSS';

        $this->available_folders = DocumentFolder::where(function ($query) use ($userDepartment) {
            if (! Auth::user()->hasRole(['administrator', 'developer'])) {
                $query->where('department', $userDepartment)
                    ->orWhereJsonContains('visible_to_departments', $userDepartment);
            }
        })
            ->orderBy('name')
            ->get()
            ->map(function ($folder) {
                return [
                    'id' => $folder->id,
                    'name' => $folder->name,
                    'full_path' => $folder->full_path,
                    'depth' => $folder->depth,
                ];
            })
            ->toArray();
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
        if (! $this->is_new_version && empty($this->document_code) && $this->category_id) {
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
            ->where(function ($query) use ($userDepartment) {
                if (! Auth::user()->hasRole(['administrator', 'developer'])) {
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
            $this->review_date = $existingFile->review_date ? $existingFile->review_date->format('Y-m-d') : '';
            $this->tags = is_array($existingFile->tags) ? implode(', ', $existingFile->tags) : '';
            $this->visible_to_departments = $existingFile->visible_to_departments ?? [];
            $this->visible_to_users = $existingFile->visible_to_users ?? [];
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
        $this->review_date = '';
        $this->tags = '';
        $this->visible_to_departments = [Auth::user()->department ?? 'ITSS'];
        $this->visible_to_users = [];
        $this->user_search = '';
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
        $latestVersion = MasterFile::where(function ($query) use ($parentFileId) {
            $query->where('parent_file_id', $parentFileId)
                ->orWhere('id', $parentFileId);
        })
            ->orderByRaw('CAST(SUBSTRING(version, 1, CHARINDEX(\'.\', version + \'.\') - 1) AS INT) DESC')
            ->orderByRaw('CAST(SUBSTRING(version, CHARINDEX(\'.\', version + \'.\') + 1, LEN(version)) AS INT) DESC')
            ->first();

        if (! $latestVersion) {
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
        // Handle case where file might be an array (edge case from drag & drop)
        if (is_array($this->file)) {
            $this->file = $this->file[0] ?? null;
        }

        $this->validate();

        try {
            // Store file
            $filePath = $this->file->store('master-files/'.date('Y/m'), 'public');

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
                'review_date' => $this->review_date ?: null,
                'tags' => $tags,
                'department' => Auth::user()->department ?? 'ITSS',
                'visible_to_departments' => $this->visible_to_departments,
                'visible_to_users' => $this->visible_to_users,
                'uploaded_by' => Auth::id(),
                'is_confidential' => $this->is_confidential,
                'folder_id' => $this->folder_id ?: null,
                'view_count' => 0,
                'download_count' => 0,
            ];

            // Normalize is_new_version to boolean
            $isNewVersion = $this->is_new_version === true || $this->is_new_version === 'true';

            if ($isNewVersion && $this->selected_existing_file) {
                // Creating a new version
                $parentFile = MasterFile::find($this->selected_existing_file);

                // Determine the actual parent file ID
                $actualParentId = $parentFile->parent_file_id ?: $parentFile->id;

                $data['parent_file_id'] = $actualParentId;
                $data['document_code'] = $parentFile->document_code; // Keep same document code
                $data['version'] = $this->getNextVersion($actualParentId);
                $data['version_notes'] = $this->version_notes;

                // Archive only the CURRENT active version, not the parent
                // Find the current active version and mark it as superseded
                MasterFile::where(function ($query) use ($actualParentId) {
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

            $message = $isNewVersion ?
                "New version {$masterFile->version} uploaded successfully!" :
                'Document uploaded successfully!';

            session()->flash('success', $message);
            $this->resetFields();

            return redirect()->route('document-library.show', $masterFile);

        } catch (\Exception $e) {
            session()->flash('error', 'Upload failed: '.$e->getMessage());
        }
    }

    public function addUser($email)
    {
        if (! in_array($email, $this->visible_to_users)) {
            $this->visible_to_users[] = $email;
        }
        $this->user_search = '';
    }

    public function removeUser($email)
    {
        $this->visible_to_users = array_values(array_filter($this->visible_to_users, fn ($e) => $e !== $email));
    }

    public function render()
    {
        $userDepartment = Auth::user()->department ?? 'ITSS';
        $isAdmin = Auth::user()->hasRole(['administrator', 'developer']);

        $categories = MasterFileCategory::where('is_active', true)
            ->when(! $isAdmin, function ($query) use ($userDepartment) {
                $query->where(function ($q) use ($userDepartment) {
                    $q->whereNull('department')
                        ->orWhere('department', '')
                        ->orWhere('department', $userDepartment)
                        ->orWhereJsonContains('allowed_departments', $userDepartment);
                });
            })
            ->orderBy('name')
            ->get();

        // Search users for autocomplete
        $searchableUsers = [];
        if (strlen($this->user_search) >= 2) {
            $searchableUsers = User::where(function ($query) {
                $query->where('email', 'like', '%'.$this->user_search.'%')
                    ->orWhere('name', 'like', '%'.$this->user_search.'%');
            })
                ->whereNotIn('email', $this->visible_to_users)
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name', 'email', 'department']);
        }

        // Get selected users details
        $selectedUsers = collect();
        if (count($this->visible_to_users) > 0) {
            $selectedUsers = User::whereIn('email', $this->visible_to_users)
                ->get(['id', 'name', 'email', 'department']);
        }

        return view('livewire.document-library.upload', compact('categories', 'searchableUsers', 'selectedUsers'));
    }
}

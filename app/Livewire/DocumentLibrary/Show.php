<?php

namespace App\Livewire\DocumentLibrary;

use App\Models\DocumentLibrary\DocumentAttachment;
use App\Models\DocumentLibrary\MasterFile;
use App\Models\DocumentLibrary\MasterFileCategory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.enduser')]
class Show extends Component
{
    use WithFileUploads;

    public MasterFile $file;

    // Edit modal properties
    public $showEditModal = false;

    // Version history modal
    public $showVersionModal = false;

    // Attachment modal properties
    public $showAttachmentModal = false;

    public $attachmentFile;

    public $attachmentTitle = '';

    public $attachmentDescription = '';

    // Attachment preview modal
    public $showAttachmentPreviewModal = false;

    public $previewAttachment = null;

    public $editTitle = '';

    public $editDescription = '';

    public $editDocumentCode = '';

    public $editCategoryId = '';

    public $editEffectiveDate = '';

    public $editReviewDate = '';

    public $editTags = '';

    public $editVisibleToDepartments = [];

    public $editVisibleToUsers = [];

    public $editUserSearch = '';

    public $editIsConfidential = false;

    protected function rules()
    {
        return [
            'editTitle' => 'required|string|max:255',
            'editDescription' => 'nullable|string',
            'editDocumentCode' => 'nullable|string|max:50',
            'editCategoryId' => 'required|exists:document_categories,id',
            'editEffectiveDate' => 'nullable|date',
            'editReviewDate' => 'nullable|date',
            'editTags' => 'nullable|string',
            'editVisibleToDepartments' => 'array',
            'editVisibleToUsers' => 'array',
            'editIsConfidential' => 'boolean',
        ];
    }

    protected $messages = [
        'editTitle.required' => 'Document title is required.',
        'editCategoryId.required' => 'Please select a category.',
    ];

    public function mount(MasterFile $file)
    {
        // Check if user has access to this file
        if (! Auth::user()->hasRole(['administrator', 'developer'])) {
            if (! $file->canBeViewedBy(Auth::user())) {
                abort(403, 'You do not have permission to view this file.');
            }
        }

        $this->file = $file;

        // Log the view
        $file->logAccess('view');
    }

    public function download()
    {
        return redirect()->route('document-library.download', $this->file);
    }

    public function downloadAll()
    {
        return redirect()->route('document-library.download-all', $this->file);
    }

    public function openEditModal()
    {
        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $this->file->uploaded_by != Auth::id()) {
            session()->flash('error', 'You do not have permission to edit this document.');

            return;
        }

        // Load current values
        $this->editTitle = $this->file->title;
        $this->editDescription = $this->file->description ?? '';
        $this->editDocumentCode = $this->file->document_code ?? '';
        $this->editCategoryId = $this->file->category_id;
        $this->editEffectiveDate = $this->file->effective_date ? $this->file->effective_date->format('Y-m-d') : '';
        $this->editReviewDate = $this->file->review_date ? $this->file->review_date->format('Y-m-d') : '';
        $this->editTags = is_array($this->file->tags) ? implode(', ', $this->file->tags) : '';
        $this->editVisibleToDepartments = $this->file->visible_to_departments ?? [];
        $this->editVisibleToUsers = $this->file->visible_to_users ?? [];
        $this->editUserSearch = '';
        $this->editIsConfidential = $this->file->is_confidential ?? false;

        $this->showEditModal = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->resetErrorBag();
    }

    public function openVersionModal()
    {
        $this->showVersionModal = true;
    }

    public function closeVersionModal()
    {
        $this->showVersionModal = false;
    }

    public function openAttachmentModal()
    {
        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $this->file->uploaded_by != Auth::id()) {
            session()->flash('error', 'You do not have permission to add attachments to this document.');

            return;
        }

        $this->reset(['attachmentFile', 'attachmentTitle', 'attachmentDescription']);
        $this->showAttachmentModal = true;
    }

    public function closeAttachmentModal()
    {
        $this->showAttachmentModal = false;
        $this->reset(['attachmentFile', 'attachmentTitle', 'attachmentDescription']);
        $this->resetErrorBag();
    }

    public function previewAttachmentModal($attachmentId)
    {
        $this->previewAttachment = DocumentAttachment::with('uploader')->find($attachmentId);
        $this->showAttachmentPreviewModal = true;
    }

    public function closeAttachmentPreviewModal()
    {
        $this->showAttachmentPreviewModal = false;
        $this->previewAttachment = null;
    }

    public function uploadAttachment()
    {
        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $this->file->uploaded_by != Auth::id()) {
            session()->flash('error', 'You do not have permission to add attachments to this document.');

            return;
        }

        $this->validate([
            'attachmentFile' => 'required|file|max:102400',
            'attachmentTitle' => 'required|string|max:255',
            'attachmentDescription' => 'nullable|string|max:500',
        ], [
            'attachmentFile.required' => 'Please select a file to attach.',
            'attachmentFile.max' => 'File size cannot exceed 100MB.',
            'attachmentTitle.required' => 'Please enter a title for the attachment.',
        ]);

        try {
            $filePath = $this->attachmentFile->store('document-attachments/'.date('Y/m'), 'public');

            DocumentAttachment::create([
                'document_id' => $this->file->id,
                'title' => $this->attachmentTitle,
                'description' => $this->attachmentDescription,
                'file_path' => $filePath,
                'original_filename' => $this->attachmentFile->getClientOriginalName(),
                'file_size' => $this->attachmentFile->getSize(),
                'mime_type' => $this->attachmentFile->getMimeType(),
                'uploaded_by' => Auth::id(),
            ]);

            $this->closeAttachmentModal();
            $this->file->refresh();
            session()->flash('success', 'Attachment uploaded successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to upload attachment: '.$e->getMessage());
        }
    }

    public function deleteAttachment($attachmentId)
    {
        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $this->file->uploaded_by != Auth::id()) {
            session()->flash('error', 'You do not have permission to delete attachments.');

            return;
        }

        try {
            $attachment = DocumentAttachment::findOrFail($attachmentId);

            // Delete file from storage
            if (Storage::disk('public')->exists($attachment->file_path)) {
                Storage::disk('public')->delete($attachment->file_path);
            }

            $attachment->delete();
            $this->file->refresh();
            session()->flash('success', 'Attachment deleted successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete attachment: '.$e->getMessage());
        }
    }

    public function downloadAttachment($attachmentId)
    {
        $attachment = DocumentAttachment::findOrFail($attachmentId);

        return response()->download(
            storage_path('app/public/'.$attachment->file_path),
            $attachment->original_filename
        );
    }

    public function saveEdit()
    {
        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $this->file->uploaded_by != Auth::id()) {
            session()->flash('error', 'You do not have permission to edit this document.');

            return;
        }

        $this->validate();

        try {
            $tags = $this->editTags ? array_map('trim', explode(',', $this->editTags)) : [];

            $this->file->update([
                'title' => $this->editTitle,
                'description' => $this->editDescription ?: null,
                'document_code' => $this->editDocumentCode ?: null,
                'category_id' => $this->editCategoryId,
                'effective_date' => $this->editEffectiveDate ?: null,
                'review_date' => $this->editReviewDate ?: null,
                'tags' => $tags,
                'visible_to_departments' => $this->editVisibleToDepartments,
                'visible_to_users' => $this->editVisibleToUsers,
                'is_confidential' => $this->editIsConfidential,
            ]);

            $this->file->refresh();
            $this->showEditModal = false;
            session()->flash('success', 'Document updated successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update document: '.$e->getMessage());
        }
    }

    public function addEditUser($email)
    {
        if (! in_array($email, $this->editVisibleToUsers)) {
            $this->editVisibleToUsers[] = $email;
        }
        $this->editUserSearch = '';
    }

    public function removeEditUser($email)
    {
        $this->editVisibleToUsers = array_values(array_filter($this->editVisibleToUsers, fn ($e) => $e !== $email));
    }

    public function render()
    {
        $this->file->load([
            'category',
            'uploader',
            'approver',
            'attachments' => function ($query) {
                $query->with('uploader')->orderBy('created_at', 'desc');
            },
            'versions' => function ($query) {
                $query->with('uploader')->orderBy('version', 'desc');
            },
            'accessLogs' => function ($query) {
                $query->with('user')->latest()->take(10);
            },
        ]);

        $userDepartment = Auth::user()->department ?? 'ITSS';

        $categories = MasterFileCategory::where('is_active', true)
            ->where(function ($query) use ($userDepartment) {
                $query->whereNull('department')
                    ->orWhere('department', $userDepartment)
                    ->orWhereJsonContains('allowed_departments', $userDepartment);
            })
            ->orderBy('name')
            ->get();

        // Search users for autocomplete in edit modal
        $editSearchableUsers = [];
        if (strlen($this->editUserSearch) >= 2) {
            $editSearchableUsers = User::where(function ($query) {
                $query->where('email', 'like', '%'.$this->editUserSearch.'%')
                    ->orWhere('name', 'like', '%'.$this->editUserSearch.'%');
            })
                ->whereNotIn('email', $this->editVisibleToUsers)
                ->orderBy('name')
                ->limit(10)
                ->get(['id', 'name', 'email', 'department']);
        }

        // Get selected users details for edit modal
        $editSelectedUsers = collect();
        if (count($this->editVisibleToUsers) > 0) {
            $editSelectedUsers = User::whereIn('email', $this->editVisibleToUsers)
                ->get(['id', 'name', 'email', 'department']);
        }

        return view('livewire.document-library.show', compact('categories', 'editSearchableUsers', 'editSelectedUsers'));
    }
}

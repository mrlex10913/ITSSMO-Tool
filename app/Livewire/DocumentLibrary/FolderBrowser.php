<?php

namespace App\Livewire\DocumentLibrary;

use App\Models\DocumentLibrary\DocumentFolder;
use App\Models\DocumentLibrary\MasterFile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.enduser')]
class FolderBrowser extends Component
{
    public $currentFolderId = null;

    public $currentFolder = null;

    public $breadcrumbs = [];

    public $viewMode = 'grid'; // grid or list

    // Create folder modal
    public $showCreateFolderModal = false;

    public $newFolderName = '';

    public $newFolderDescription = '';

    public $newFolderColor = '#3B82F6';

    // Move item modal
    public $showMoveModal = false;

    public $moveItemType = null; // 'file' or 'folder'

    public $moveItemId = null;

    public $moveTargetFolderId = null;

    // Rename modal
    public $showRenameModal = false;

    public $renameItemType = null;

    public $renameItemId = null;

    public $renameName = '';

    // Search
    public $search = '';

    // Share modal
    public $showShareModal = false;

    public $shareFolderId = null;

    public $shareFolder = null;

    public $shareWithUsers = [];

    public $shareWithDepartments = [];

    public $shareWithDepartmentHead = false;

    public $shareUserSearch = '';

    protected $queryString = ['currentFolderId'];

    public function mount($folderId = null)
    {
        $this->currentFolderId = $folderId;
        $this->loadCurrentFolder();
    }

    public function loadCurrentFolder()
    {
        if ($this->currentFolderId) {
            $this->currentFolder = DocumentFolder::with('parent')->find($this->currentFolderId);
            $this->breadcrumbs = $this->currentFolder ? $this->currentFolder->breadcrumbs : [];
        } else {
            $this->currentFolder = null;
            $this->breadcrumbs = [];
        }
    }

    public function navigateToFolder($folderId = null)
    {
        $this->currentFolderId = $folderId;
        $this->loadCurrentFolder();
        $this->search = '';
    }

    public function navigateUp()
    {
        if ($this->currentFolder && $this->currentFolder->parent_id) {
            $this->navigateToFolder($this->currentFolder->parent_id);
        } else {
            $this->navigateToFolder(null);
        }
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'grid' ? 'list' : 'grid';
    }

    // Create Folder
    public function openCreateFolderModal()
    {
        $this->reset(['newFolderName', 'newFolderDescription', 'newFolderColor']);
        $this->newFolderColor = '#3B82F6';
        $this->showCreateFolderModal = true;
    }

    public function closeCreateFolderModal()
    {
        $this->showCreateFolderModal = false;
        $this->resetErrorBag();
    }

    public function createFolder()
    {
        $this->validate([
            'newFolderName' => 'required|string|max:255',
            'newFolderDescription' => 'nullable|string|max:500',
            'newFolderColor' => 'required|string|max:7',
        ], [
            'newFolderName.required' => 'Folder name is required.',
        ]);

        $userDepartment = Auth::user()->department ?? 'ITSS';

        // Check for duplicate folder name in same location
        $exists = DocumentFolder::where('name', $this->newFolderName)
            ->where('parent_id', $this->currentFolderId)
            ->where('department', $userDepartment)
            ->exists();

        if ($exists) {
            $this->addError('newFolderName', 'A folder with this name already exists in this location.');

            return;
        }

        $slug = DocumentFolder::generateSlug($this->newFolderName, $this->currentFolderId, $userDepartment);

        DocumentFolder::create([
            'name' => $this->newFolderName,
            'slug' => $slug,
            'description' => $this->newFolderDescription,
            'parent_id' => $this->currentFolderId,
            'color' => $this->newFolderColor,
            'icon' => 'folder',
            'department' => $userDepartment,
            'created_by' => Auth::id(),
            'is_active' => true,
        ]);

        $this->closeCreateFolderModal();
        session()->flash('success', 'Folder created successfully.');
    }

    // Delete Folder
    public function deleteFolder($folderId)
    {
        $folder = DocumentFolder::find($folderId);

        if (! $folder) {
            session()->flash('error', 'Folder not found.');

            return;
        }

        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $folder->created_by !== Auth::id()) {
            session()->flash('error', 'You do not have permission to delete this folder.');

            return;
        }

        // Check if folder has children or files
        if ($folder->children()->count() > 0) {
            session()->flash('error', 'Cannot delete folder with subfolders. Please delete subfolders first.');

            return;
        }

        if ($folder->files()->count() > 0) {
            session()->flash('error', 'Cannot delete folder with files. Please move or delete files first.');

            return;
        }

        $folder->delete();
        session()->flash('success', 'Folder deleted successfully.');
    }

    // Move Item
    public function openMoveModal($type, $id)
    {
        $this->moveItemType = $type;
        $this->moveItemId = $id;
        $this->moveTargetFolderId = $this->currentFolderId;
        $this->showMoveModal = true;
    }

    public function closeMoveModal()
    {
        $this->showMoveModal = false;
        $this->reset(['moveItemType', 'moveItemId', 'moveTargetFolderId']);
    }

    public function moveItem()
    {
        if ($this->moveItemType === 'file') {
            $file = MasterFile::find($this->moveItemId);
            if ($file) {
                $file->update(['folder_id' => $this->moveTargetFolderId]);
                session()->flash('success', 'File moved successfully.');
            }
        } elseif ($this->moveItemType === 'folder') {
            $folder = DocumentFolder::find($this->moveItemId);
            if ($folder) {
                // Prevent moving folder into itself or its children
                if ($this->moveTargetFolderId) {
                    $targetFolder = DocumentFolder::find($this->moveTargetFolderId);
                    $descendantIds = $folder->getAllDescendantIds();
                    if ($this->moveTargetFolderId == $folder->id || in_array($this->moveTargetFolderId, $descendantIds)) {
                        session()->flash('error', 'Cannot move folder into itself or its subfolders.');
                        $this->closeMoveModal();

                        return;
                    }
                }

                $folder->update(['parent_id' => $this->moveTargetFolderId]);
                session()->flash('success', 'Folder moved successfully.');
            }
        }

        $this->closeMoveModal();
    }

    // Rename
    public function openRenameModal($type, $id)
    {
        $this->renameItemType = $type;
        $this->renameItemId = $id;

        if ($type === 'folder') {
            $folder = DocumentFolder::find($id);
            $this->renameName = $folder->name ?? '';
        } elseif ($type === 'file') {
            $file = MasterFile::find($id);
            $this->renameName = $file->title ?? '';
        }

        $this->showRenameModal = true;
    }

    public function closeRenameModal()
    {
        $this->showRenameModal = false;
        $this->reset(['renameItemType', 'renameItemId', 'renameName']);
        $this->resetErrorBag();
    }

    public function renameItem()
    {
        $this->validate([
            'renameName' => 'required|string|max:255',
        ]);

        if ($this->renameItemType === 'folder') {
            $folder = DocumentFolder::find($this->renameItemId);
            if ($folder) {
                $folder->update([
                    'name' => $this->renameName,
                    'slug' => DocumentFolder::generateSlug($this->renameName, $folder->parent_id, $folder->department),
                ]);
                session()->flash('success', 'Folder renamed successfully.');
            }
        } elseif ($this->renameItemType === 'file') {
            $file = MasterFile::find($this->renameItemId);
            if ($file) {
                $file->update(['title' => $this->renameName]);
                session()->flash('success', 'File renamed successfully.');
            }
        }

        $this->closeRenameModal();
    }

    // Share Folder
    public function openShareModal($folderId)
    {
        $folder = DocumentFolder::find($folderId);

        if (! $folder) {
            session()->flash('error', 'Folder not found.');

            return;
        }

        // Check permission - only owner or admin can share
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $folder->created_by !== Auth::id()) {
            session()->flash('error', 'You do not have permission to share this folder.');

            return;
        }

        $this->shareFolderId = $folderId;
        $this->shareFolder = $folder;
        $this->shareWithUsers = $folder->shared_with_users ?? [];
        $this->shareWithDepartments = $folder->shared_with_departments ?? [];
        $this->shareWithDepartmentHead = $folder->share_with_department_head ?? false;
        $this->shareUserSearch = '';
        $this->showShareModal = true;
    }

    public function closeShareModal()
    {
        $this->showShareModal = false;
        $this->reset(['shareFolderId', 'shareFolder', 'shareWithUsers', 'shareWithDepartments', 'shareWithDepartmentHead', 'shareUserSearch']);
        $this->resetErrorBag();
    }

    public function addShareUser($userId)
    {
        $userId = (int) $userId;
        if (! in_array($userId, $this->shareWithUsers)) {
            $this->shareWithUsers[] = $userId;
        }
        $this->shareUserSearch = '';
    }

    public function removeShareUser($userId)
    {
        $this->shareWithUsers = array_values(array_filter($this->shareWithUsers, fn ($id) => $id !== (int) $userId));
    }

    public function toggleShareDepartment($department)
    {
        if (in_array($department, $this->shareWithDepartments)) {
            $this->shareWithDepartments = array_values(array_filter($this->shareWithDepartments, fn ($d) => $d !== $department));
        } else {
            $this->shareWithDepartments[] = $department;
        }
    }

    public function saveSharing()
    {
        $folder = DocumentFolder::find($this->shareFolderId);

        if (! $folder) {
            session()->flash('error', 'Folder not found.');
            $this->closeShareModal();

            return;
        }

        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $folder->created_by !== Auth::id()) {
            session()->flash('error', 'You do not have permission to share this folder.');
            $this->closeShareModal();

            return;
        }

        $folder->update([
            'shared_with_users' => $this->shareWithUsers,
            'shared_with_departments' => $this->shareWithDepartments,
            'share_with_department_head' => $this->shareWithDepartmentHead,
        ]);

        session()->flash('success', 'Folder sharing settings saved successfully.');
        $this->closeShareModal();
    }

    public function makePublic($folderId)
    {
        $folder = DocumentFolder::find($folderId);

        if (! $folder) {
            session()->flash('error', 'Folder not found.');

            return;
        }

        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $folder->created_by !== Auth::id()) {
            session()->flash('error', 'You do not have permission to change this folder.');

            return;
        }

        $folder->update(['is_private' => false]);
        session()->flash('success', 'Folder is now visible to your department.');
    }

    public function makePrivate($folderId)
    {
        $folder = DocumentFolder::find($folderId);

        if (! $folder) {
            session()->flash('error', 'Folder not found.');

            return;
        }

        // Check permission
        if (! Auth::user()->hasRole(['administrator', 'developer']) && $folder->created_by !== Auth::id()) {
            session()->flash('error', 'You do not have permission to change this folder.');

            return;
        }

        $folder->update([
            'is_private' => true,
            'shared_with_users' => [],
            'shared_with_departments' => [],
            'share_with_department_head' => false,
        ]);
        session()->flash('success', 'Folder is now private.');
    }

    public function render()
    {
        $user = Auth::user();
        $userId = Auth::id();
        $userDepartment = $user->department ?? 'ITSS';
        $userEmail = $user->email;
        $isAdmin = $user->hasRole(['administrator', 'developer']);

        // Get folders in current location with new visibility logic
        $foldersQuery = DocumentFolder::with(['creator', 'children', 'files'])
            ->where('parent_id', $this->currentFolderId)
            ->where('is_active', true)
            ->when(! $isAdmin, function ($query) use ($userId, $userDepartment) {
                $query->where(function ($q) use ($userId, $userDepartment) {
                    // User is the creator
                    $q->where('created_by', $userId)
                        // OR folder is not private and belongs to user's department
                        ->orWhere(function ($q2) use ($userDepartment) {
                            $q2->where('is_private', false)
                                ->where(function ($q3) use ($userDepartment) {
                                    $q3->where('department', $userDepartment)
                                        ->orWhereNull('department');
                                });
                        })
                        // OR shared with this specific user
                        ->orWhereJsonContains('shared_with_users', $userId)
                        // OR shared with user's department
                        ->orWhereJsonContains('shared_with_departments', $userDepartment);
                });
            })
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->orderBy('name');

        $folders = $foldersQuery->get();

        // Get files in current location
        $filesQuery = MasterFile::with(['category', 'uploader'])
            ->where('folder_id', $this->currentFolderId)
            ->where('status', 'active')
            ->when(! $isAdmin, function ($query) use ($userDepartment, $userEmail) {
                $query->where(function ($q) use ($userDepartment, $userEmail) {
                    $q->where('department', $userDepartment)
                        ->orWhereJsonContains('visible_to_departments', $userDepartment)
                        ->orWhereJsonContains('visible_to_users', $userEmail);
                });
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('document_code', 'like', '%'.$this->search.'%');
                });
            })
            ->orderBy('title');

        $files = $filesQuery->get();

        // Get all folders for move modal (excluding current item and its children)
        $allFolders = collect();
        if ($this->showMoveModal) {
            $excludeIds = [];
            if ($this->moveItemType === 'folder') {
                $excludeIds[] = $this->moveItemId;
                $folder = DocumentFolder::find($this->moveItemId);
                if ($folder) {
                    $excludeIds = array_merge($excludeIds, $folder->getAllDescendantIds());
                }
            }

            $allFolders = DocumentFolder::where('is_active', true)
                ->whereNotIn('id', $excludeIds)
                ->when(! $isAdmin, function ($query) use ($userDepartment) {
                    $query->where(function ($q) use ($userDepartment) {
                        $q->where('department', $userDepartment)
                            ->orWhereNull('department')
                            ->orWhereJsonContains('visible_to_departments', $userDepartment);
                    });
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
                });
        }

        // Get available users for sharing (excluding current user)
        $availableUsers = collect();
        $departments = [];
        if ($this->showShareModal) {
            $availableUsers = User::where('id', '!=', Auth::id())
                ->when($this->shareUserSearch, function ($query) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', '%'.$this->shareUserSearch.'%')
                            ->orWhere('email', 'like', '%'.$this->shareUserSearch.'%');
                    });
                })
                ->orderBy('name')
                ->take(10)
                ->get();

            // Get distinct departments
            $departments = User::whereNotNull('department')
                ->distinct()
                ->pluck('department')
                ->filter()
                ->sort()
                ->values()
                ->toArray();
        }

        // Get currently shared users info
        $sharedUsersInfo = collect();
        if ($this->showShareModal && ! empty($this->shareWithUsers)) {
            $sharedUsersInfo = User::whereIn('id', $this->shareWithUsers)->get();
        }

        return view('livewire.document-library.folder-browser', compact(
            'folders',
            'files',
            'allFolders',
            'availableUsers',
            'departments',
            'sharedUsersInfo'
        ));
    }
}

<?php

namespace App\Models\DocumentLibrary;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DocumentFolder extends Model
{
    protected $table = 'document_folders';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'color',
        'icon',
        'department',
        'visible_to_departments',
        'created_by',
        'is_active',
        'is_private',
        'shared_with_users',
        'shared_with_departments',
        'share_with_department_head',
    ];

    protected $casts = [
        'visible_to_departments' => 'array',
        'shared_with_users' => 'array',
        'shared_with_departments' => 'array',
        'is_active' => 'boolean',
        'is_private' => 'boolean',
        'share_with_department_head' => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(DocumentFolder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(DocumentFolder::class, 'parent_id')->orderBy('name');
    }

    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    public function files()
    {
        return $this->hasMany(MasterFile::class, 'folder_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getBreadcrumbsAttribute(): array
    {
        $breadcrumbs = [];
        $folder = $this;

        while ($folder) {
            array_unshift($breadcrumbs, [
                'id' => $folder->id,
                'name' => $folder->name,
                'slug' => $folder->slug,
            ]);
            $folder = $folder->parent;
        }

        return $breadcrumbs;
    }

    public function getFullPathAttribute(): string
    {
        return collect($this->breadcrumbs)->pluck('name')->implode(' / ');
    }

    public function getDepthAttribute(): int
    {
        $depth = 0;
        $folder = $this->parent;

        while ($folder) {
            $depth++;
            $folder = $folder->parent;
        }

        return $depth;
    }

    public function canBeViewedBy(User $user): bool
    {
        // Admins/developers can see all
        if ($user->hasRole(['administrator', 'developer'])) {
            return true;
        }

        // Creator can always see their own folder
        if ($this->created_by === $user->id) {
            return true;
        }

        // If folder is private and user is not creator, check sharing
        if ($this->is_private) {
            // Check if shared with this specific user
            if ($this->shared_with_users && in_array($user->id, $this->shared_with_users)) {
                return true;
            }

            // Check if shared with user's department
            if ($this->shared_with_departments && in_array($user->department, $this->shared_with_departments)) {
                // If share_with_department_head is true, only head can see
                if ($this->share_with_department_head) {
                    return $user->isDepartmentHead();
                }

                return true;
            }

            // Private folder, not shared with this user
            return false;
        }

        // Non-private folder - check department visibility (legacy behavior)
        // Check department match
        if ($this->department && $this->department === $user->department) {
            return true;
        }

        // Check visible_to_departments
        if ($this->visible_to_departments && in_array($user->department, $this->visible_to_departments)) {
            return true;
        }

        // No department restriction means visible to all
        if (! $this->department && (! $this->visible_to_departments || empty($this->visible_to_departments))) {
            return true;
        }

        return false;
    }

    /**
     * Get users this folder is shared with
     */
    public function sharedUsers()
    {
        if (! $this->shared_with_users || empty($this->shared_with_users)) {
            return collect();
        }

        return User::whereIn('id', $this->shared_with_users)->get();
    }

    /**
     * Check if folder is shared with anyone
     */
    public function isShared(): bool
    {
        return ! empty($this->shared_with_users) || ! empty($this->shared_with_departments);
    }

    public function getAllDescendantIds(): array
    {
        $ids = [];
        $this->collectDescendantIds($this, $ids);

        return $ids;
    }

    private function collectDescendantIds(DocumentFolder $folder, array &$ids): void
    {
        foreach ($folder->children as $child) {
            $ids[] = $child->id;
            $this->collectDescendantIds($child, $ids);
        }
    }

    public static function generateSlug(string $name, ?int $parentId, ?string $department): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (static::where('slug', $slug)
            ->where('parent_id', $parentId)
            ->where('department', $department)
            ->exists()) {
            $slug = $baseSlug.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}

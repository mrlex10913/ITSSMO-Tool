<?php

namespace App\Models\MasterFiles;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MasterFileCategory extends Model
{
    protected $table = 'master_file_categories';

    protected $fillable = [
        'name', 'slug', 'description', 'color', 'icon',
        'department', 'allowed_departments', 'is_active',
        'requires_approval', 'created_by'
    ];

    protected $casts = [
        'allowed_departments' => 'array',
        'is_active' => 'boolean',
        'requires_approval' => 'boolean',
    ];

    public function files()
    {
        return $this->hasMany(MasterFile::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isVisibleToDepartment($department): bool
    {
        if (!$this->department) return true;

        if ($this->department === $department) return true;

        return in_array($department, $this->allowed_departments ?? []);
    }

}

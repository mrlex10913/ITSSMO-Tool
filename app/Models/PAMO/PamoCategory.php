<?php

namespace App\Models\PAMO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PamoCategory extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'description',
    ];

    public function parent()
    {
        return $this->belongsTo(PamoCategory::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(PamoCategory::class, 'parent_id');
    }
}

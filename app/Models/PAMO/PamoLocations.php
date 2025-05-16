<?php

namespace App\Models\PAMO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PamoLocations extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'address',
        'type', // office, storage, warehouse, etc.
        'description',
        'is_active',
    ];
    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function assets()
    {
        return $this->hasMany(PamoAssets::class);
    }
}

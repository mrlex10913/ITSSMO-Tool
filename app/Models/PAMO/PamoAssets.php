<?php

namespace App\Models\PAMO;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PamoAssets extends Model
{
    use HasFactory;
    protected $fillable = [
        'po_number',
        'property_tag_number',
        'barcode',
        'brand',
        'model',
        'serial_number',
        'category_id',
        'status',
        'purchase_date',
        'purchase_value',
        'description',
        'location_id',
        'assigned_to',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_value' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(PamoCategory::class, 'category_id');
    }
    public function location()
    {
        return $this->belongsTo(PamoLocations::class, 'location_id');
    }
    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function movements()
    {
        return $this->hasMany(PamoAssetMovement::class, 'asset_id');
    }
    public function assignedEmployee(){
        return $this->belongsTo(MasterList::class, 'assigned_to');
    }
    public function getAssignedPersonNameAttribute(): ?string
    {
        return $this->assignedEmployee?->full_name;
    }
    public function getAssignedPersonNumberAttribute(): ?string
    {
        return $this->assignedEmployee?->employee_number;
    }
    public function getAssignedPersonDepartmentAttribute(): ?string
    {
        return $this->assignedEmployee?->department;
    }

    public function isAssigned()
    {
        return $this->location_id !== null || $this->assigned_to !== null;
    }

    public static function getUnassigned()
    {
        return self::whereNull('location_id')
            ->whereNull('assigned_to')
            ->where('status', 'available')
            ->get();
    }
    public function getCurrentValue()
    {
        if (!$this->purchase_date || !$this->purchase_value) {
            return 0;
        }

        $age = now()->diffInYears($this->purchase_date);
        $depreciationRate = 0.2; // 20% per year (example)
        $maxDepreciation = 0.8; // Maximum 80% depreciation

        $depreciation = min($age * $depreciationRate, $maxDepreciation);
        return $this->purchase_value * (1 - $depreciation);
    }

}

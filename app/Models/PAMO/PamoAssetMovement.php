<?php

namespace App\Models\PAMO;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PamoAssetMovement extends Model
{
    use HasFactory;
    protected $table = 'asset_movements';
    protected $fillable = [
        'asset_id',
        'from_location_id',
        'to_location_id',
        'assigned_by',
        'assigned_to',
        'movement_date',
        'notes',
        'movement_type',
    ];

    protected $casts = [
        'movement_date' => 'date',
    ];

    public function asset()
    {
        return $this->belongsTo(PamoAssets::class);
    }
    public function fromLocation()
    {
        return $this->belongsTo(PamoLocations::class, 'from_location_id');
    }
    public function toLocation()
    {
        return $this->belongsTo(PamoLocations::class, 'to_location_id');
    }
    public function assignedByUser()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
    public function assignedToUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    public function assignedEmployee()
    {
        return $this->belongsTo(MasterList::class, 'assigned_to');
    }
}

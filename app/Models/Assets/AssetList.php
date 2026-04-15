<?php

namespace App\Models\Assets;

use App\Models\Borrowers\BorrowerItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetList extends Model
{
    use HasFactory;

    protected $table = 'asset_lists';

    protected $guarded = [];

    public function assetList()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_categories_id');
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_categories_id');
    }

    /**
     * Get borrow history for this asset (matched by serial number)
     */
    public function borrowHistory()
    {
        return $this->hasMany(BorrowerItem::class, 'serial', 'item_serial_itss');
    }
}

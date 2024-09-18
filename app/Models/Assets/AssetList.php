<?php

namespace App\Models\Assets;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetList extends Model
{
    use HasFactory;
    protected $table = 'asset_lists';

    protected $guarded = [];

    public function assetList(){
        return $this->belongsTo(AssetCategory::class, 'asset_categories_id');
    }
}

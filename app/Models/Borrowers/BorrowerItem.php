<?php

namespace App\Models\Borrowers;

use App\Models\Assets\AssetCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowerItem extends Model
{
    use HasFactory;

    protected $table = 'borrowed_item';
    protected $guarded = [];

    public function borrower(){
        return $this->belongsTo(BorrowerDetails::class, 'borrower_id', 'id');
    }
    public function assetCategory()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id', 'id');
    }
}

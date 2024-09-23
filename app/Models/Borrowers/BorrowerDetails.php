<?php

namespace App\Models\Borrowers;

use App\Models\Assets\AssetCategory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BorrowerDetails extends Model
{
    use HasFactory;

    protected $table = 'borrowers';

    protected $guarded = [];

    public function itemBorrow(){
        return $this->hasMany(BorrowerItem::class,'borrower_id', 'id');
    }
    public function assetCategories()
    {
        return $this->hasManyThrough(AssetCategory::class, BorrowerItem::class, 'borrower_id', 'id', 'id', 'asset_category_id');
    }

}

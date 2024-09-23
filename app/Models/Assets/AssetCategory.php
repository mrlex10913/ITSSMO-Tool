<?php

namespace App\Models\Assets;

use App\Models\Borrowers\BorrowerItem;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    use HasFactory;
    protected $table = 'asset_categories';
    protected $fillable = ['name'];

    public function assetsCategory(){
        return $this->hasMany(AssetList::class, 'id');
    }

    public function borrowedItems()
    {
        return $this->hasMany(BorrowerItem::class, 'asset_category_id', 'id');
    }

}

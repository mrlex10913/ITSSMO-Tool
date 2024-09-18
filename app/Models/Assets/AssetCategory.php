<?php

namespace App\Models\Assets;

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

}

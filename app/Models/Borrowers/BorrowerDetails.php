<?php

namespace App\Models\Borrowers;

use App\Models\Assets\AssetCategory;
use App\Models\Helpdesk\Ticket;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BorrowerDetails extends Model
{
    use HasFactory;

    protected $table = 'borrowers';

    protected $guarded = [];

    /**
     * The helpdesk ticket this borrow record is linked to (if any).
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function itemBorrow()
    {
        return $this->hasMany(BorrowerItem::class, 'borrower_id', 'id');
    }

    public function assetCategories()
    {
        return $this->hasManyThrough(AssetCategory::class, BorrowerItem::class, 'borrower_id', 'id', 'id', 'asset_category_id');
    }
}

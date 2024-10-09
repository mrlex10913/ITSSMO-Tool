<?php

namespace App\Models\UserRecords;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FalcoData extends Model
{
    use HasFactory;
    protected $table = 'falco_data';
    protected $guarded = [];
}

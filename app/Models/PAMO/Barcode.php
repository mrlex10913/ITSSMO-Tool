<?php

namespace App\Models\PAMO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Barcode extends Model
{
    use HasFactory;
    protected $table = 'barcode';
    protected $guarded = [];
}

<?php

namespace App\Models\PAMO;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Picqer\Barcode\BarcodeGeneratorHTML;

class Barcode extends Model
{
    use HasFactory;
    protected $table = 'barcode';
    protected $fillable = [
        'number',
        'status',
        'is_used',
        'image_path'
    ];

}

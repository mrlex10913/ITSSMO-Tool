<?php

namespace App\Models\BFO;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChequePayee extends Model
{
    use HasFactory;

    protected $fillable = ['payee_name'];
}

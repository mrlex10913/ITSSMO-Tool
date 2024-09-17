<?php

namespace App\Models\Examination\Coordinator;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CodeGenerator extends Model
{
    use HasFactory;
    protected $table = 'generated_codes';
    protected $fillable = ['code', 'created'];
}

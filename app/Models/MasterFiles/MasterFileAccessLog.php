<?php

namespace App\Models\MasterFiles;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class MasterFileAccessLog extends Model
{
    protected $table = 'master_file_access_logs';

    protected $fillable = [
        'file_id',
        'user_id',
        'action',
        'ip_address',
        'user_agent',
        'department'
    ];

    public function file()
    {
        return $this->belongsTo(MasterFile::class, 'file_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

<?php

namespace App\Models\Examination;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QnAChoice extends Model
{
    use HasFactory;
    protected $fillable = ['qn_a_q_uestions_id', 'choices', 'is_correct'];
    public function choicesOfQuestion(){
        return $this->belongsTo(QnAQUestion::class, 'qn_a_subjects_id');
    }
}

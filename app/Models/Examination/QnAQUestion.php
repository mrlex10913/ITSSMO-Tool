<?php

namespace App\Models\Examination;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QnAQUestion extends Model
{
    use HasFactory;

    protected $fillable = ['qn_a_subjects_id', 'questions'];

    public function questionSubject(){
        return $this->belongsTo(QnASubject::class, 'id');
    }

    public function questionChoices(){
        return $this->hasMany(QnAChoice::class, 'qn_a_q_uestions_id');
    }
}

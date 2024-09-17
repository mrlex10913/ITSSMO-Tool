<?php

namespace App\Models\Examination;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QnASubject extends Model
{
    use HasFactory;

    protected $fillable = ['subject'];

    public function subjectQuestions(){
        return $this->hasMany(QnAQUestion::class, 'qn_a_subjects_id');
    }
}

<?php

namespace App\Livewire\Examination\Admin;

use App\Models\Examination\QnASubject;
use Livewire\Component;

class Subject extends Component
{
    public $NewSubject = false;

    public $subject;

    public $subjectId;



    public function viewQuestions($id){
        $this->subjectId = $id;
        return redirect()->route('examination.questions', ['id' => $this->subjectId]);
        // $this->dispatch('navigateToQuestions', $this->subjectId);
    }

    public function createNewSubject(){
        $this->NewSubject = true;
    }

    public function saveSubject(){
        $this->validate([
            'subject' => 'required|string|max:255|unique:qn_a_subjects,subject'
        ]);
        QnASubject::create([
            'subject' => $this->subject
        ]);

        session()->flash('message', 'Subject created successfully');
        $this->reset('subject');
        $this->NewSubject = false;
    }

    public function render()
    {
        $subjects = QnASubject::orderBy('created_at', 'desc')->get();
        return view('livewire.examination.admin.subject', compact('subjects'))->layout('layouts.app');
    }
}

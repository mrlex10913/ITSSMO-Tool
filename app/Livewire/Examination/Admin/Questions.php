<?php

namespace App\Livewire\Examination\Admin;

use App\Models\Examination\QnAChoice;
use App\Models\Examination\QnAQUestion;
use App\Models\Examination\QnASubject;
use Livewire\Component;

class Questions extends Component
{
    public $NewQuestions = false;

    public $subject;
    public $subject_id;
    public $question_text;
    public $answers = [];
    public $correctAnswer;
    public $maxAnswersReached = false;

    public $questions;

    public $editQuestionId;
    public $editMode = false;

    public $confirmDeletion = false;
    public $questionToDelete;

    public function mount($id)
    {

        for ($i = 0; $i < 4; $i++) {
            $this->answers[] = ['text' => '', 'is_correct' => false];
        }

        $this->subject_id = $id;
    }

    public function createNewQuestions(){
        $this->NewQuestions = true;
    }

    public function addAnswer()
    {
        if(count($this->answers) < 6){
            $this->answers[] = ['text' => '', 'is_correct' => false];
            $this->maxAnswersReached = false;
        }else{
            $this->maxAnswersReached = true;
        }

    }

    public function removeAnswer($index)
    {
        unset($this->answers[$index]);
        $this->answers = array_values($this->answers);
        $this->maxAnswersReached = false;
    }

    public function saveQuestions(){
        $this->validate([
            'question_text' => 'required|string|max:255',
            'answers.*.text' => 'required|string|max:255',
            'correctAnswer' => 'required',
        ],
        [
            'question_text.required' => 'The question field is required.',
            'answers.*.text.required' => 'This answer field is required.',
            'correctAnswer.required' => 'You must select the correct answer.',
        ]);

        $question = QnAQUestion::create([
            'qn_a_subjects_id' => $this->subject_id,
            'questions' => $this->question_text
        ]);

        foreach($this->answers as $index => $answer){
            QnAChoice::create([
                'qn_a_q_uestions_id' => $question->id,
                'choices' => $answer['text'],
                'is_correct' => $index == $this->correctAnswer
            ]);
        }

        $this->reset(['NewQuestions', 'question_text', 'answers', 'correctAnswer']);

        for ($i = 0; $i < 4; $i++) {
            $this->answers[] = ['text' => '', 'is_correct' => false];
        }

        $this->dispatch('questionsSaved');

    }

    public function editQuestion($questionId){
        $this->editMode = true;
        $this->editQuestionId = $questionId;
        $question = QnAQUestion::find($questionId);

        $this->question_text = $question->questions;
        $this->answers = $question->questionChoices->map(function($choice) {
            return ['text' => $choice->choices, 'is_correct' => $choice->is_correct];
        })->toArray();

        $this->correctAnswer = array_search(true, array_column($this->answers, 'is_correct'));

        $this->NewQuestions = true;
    }

    public function saveEditedQuestion(){
        $question = QnAQUestion::find($this->editQuestionId);
        $question->questions = $this->question_text;
        $question->save();

        foreach ($this->answers as $index => $answer) {
            $choice = $question->questionChoices[$index];
            $choice->choices = $answer['text'];
            $choice->is_correct = $index == $this->correctAnswer;
            $choice->save();
        }

        $this->reset(['editMode', 'editQuestionId', 'question_text', 'answers', 'correctAnswer', 'NewQuestions']);
    }

    public function confirmDelete($questionId){

        $this->questionToDelete = $questionId;
        $this->confirmDeletion = true;
    }

    public function deleteQuestion(){
        $question = QnAQUestion::with('questionChoices')->find($this->questionToDelete);
        if ($question) {
            $question->questionChoices()->delete(); // Delete associated answers
            $question->delete(); // Delete the question
            $this->confirmDeletion = false;
            $this->questionToDelete = null;
            // Optional: emit event or message to notify user of successful deletion
        }
    }



    public function render()
    {

        if ($this->subject_id) {
            $this->subject = QnASubject::with('subjectQuestions.questionChoices')->find($this->subject_id);
            if ($this->subject) {
                $this->questions = $this->subject->subjectQuestions;
            }
        }
        // $questionSubject = QnASubject::find($this->viewId($this->testId));
        // $subjectData = $this->subject;

        // $fetchQuestions = QnAQUestion::with('questionChoices')->get();
        // $test = $this->testId ? $this->viewId($this->testId) : null;
        $selectSubject = QnASubject::orderBy('created_at', 'desc')->get();
        return view('livewire.examination.admin.questions', compact('selectSubject'));
    }
}

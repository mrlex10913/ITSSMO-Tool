<?php

namespace App\Livewire\Examination\Coordinator;

use App\Models\Examination\Coordinator\CodeGenerator as CoordinatorCodeGenerator;
use Livewire\Component;
use Illuminate\Support\Str;

class Codegenerator extends Component
{
    public $generatedCode;

    public function generateCode(){
        $this->generatedCode = Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4)) . '-' . Str::upper(Str::random(4));
        CoordinatorCodeGenerator::create([
            'code' => $this->generatedCode,
            'create' => now()
        ]);
        session()->flash('message', 'Code generated successfully!');
    }
    public function render()
    {
        return view('livewire.examination.coordinator.codegenerator')->layout('layouts.app');
    }
}

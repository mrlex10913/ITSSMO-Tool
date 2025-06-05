<?php

namespace App\Livewire\BFO;

use App\Models\BFO\ChequePayee;
use Livewire\Component;

class PayeeAutocomplete extends Component
{
    public $search = '';
    public $showSuggestions = false;
    public $suggestions = [];

    public function updatedSearch()
    {
        if (strlen($this->search) >= 1) {
            $this->suggestions = ChequePayee::where('payee_name', 'like', '%' . $this->search . '%')
                ->limit(10)
                ->pluck('payee_name')
                ->toArray();

            $this->showSuggestions = count($this->suggestions) > 0;
        } else {
            $this->suggestions = [];
            $this->showSuggestions = false;
        }
    }

    public function selectPayee($payeeName)
    {
        $this->search = $payeeName;
        $this->showSuggestions = false;

        // Dispatch event to parent (cheque component)
        $this->dispatch('payee-selected-autocomplete', $payeeName);
    }

    public function hideSuggestions()
    {
        // Delay hiding to allow click events on suggestions
        $this->dispatch('$refresh');
        $this->showSuggestions = false;
    }

    public function render()
    {
        return view('livewire.b-f-o.payee-autocomplete');
    }
}

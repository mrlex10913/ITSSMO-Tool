<?php

namespace App\Livewire\BFO;

use App\Models\BFO\Cheque as BFOCheque;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

#[Layout('layouts.enduser')]
class Cheque extends Component
{
    public $payee = '';
    public $amount = '';
    public $date;
    public $savedChequeId = null;

    protected $listeners = [
        'payee-selected-autocomplete' => 'updatePayee',
        'payee-selected' => 'updatePayeeFromModal',
    ];

    public function mount()
    {
        $this->date = date('Y-m-d');
    }

    public function updatePayee($payeeName)
    {
        $this->payee = $payeeName;
    }

    public function updatePayeeFromModal($data)
    {
        $this->payee = $data['name'];
    }

    public function saveAndPrint($fieldPositions = null)
    {
        // Validate the form
        $this->validate([
            'payee' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date'
        ]);

        try {
            // Generate cheque number
            $chequeNumber = BFOCheque::generateChequeNumber();

            // Convert amount to words
            $amountInWords = $this->numberToWords($this->amount);

            // Save to database
            $cheque = BFOCheque::create([
                'cheque_number' => $chequeNumber,
                'payee_name' => $this->payee,
                'amount' => $this->amount,
                'amount_in_words' => $amountInWords,
                'cheque_date' => $this->date,
                'status' => 'printed',
                'printed_at' => now(),
                'created_by' => Auth::id(),
                'field_positions' => $fieldPositions
            ]);

            $this->savedChequeId = $cheque->id;

            // Flash success message
            session()->flash('success', "Cheque #{$chequeNumber} saved successfully!");

            // Dispatch event to trigger print
            $this->dispatch('cheque-saved-proceed-print', [
                'chequeId' => $cheque->id,
                'chequeNumber' => $chequeNumber
            ]);

            return $cheque;

        } catch (\Exception $e) {
            session()->flash('error', 'Error saving cheque: ' . $e->getMessage());
            return null;
        }
    }
    public function saveDraft($fieldPositions = null)
    {
        // Validate the form
        $this->validate([
            'payee' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'date' => 'required|date'
        ]);

        try {
            if ($this->savedChequeId) {
                // Update existing draft
                $cheque = BFOCheque::find($this->savedChequeId);  // Changed from ChequeModel to BFOCheque
                $cheque->update([
                    'payee_name' => $this->payee,
                    'amount' => $this->amount,
                    'amount_in_words' => $this->numberToWords($this->amount),
                    'cheque_date' => $this->date,
                    'field_positions' => $fieldPositions
                ]);
            } else {
                // Create new draft
                $chequeNumber = BFOCheque::generateChequeNumber();  // Changed from ChequeModel to BFOCheque
                $cheque = BFOCheque::create([  // Changed from ChequeModel to BFOCheque
                    'cheque_number' => $chequeNumber,
                    'payee_name' => $this->payee,
                    'amount' => $this->amount,
                    'amount_in_words' => $this->numberToWords($this->amount),
                    'cheque_date' => $this->date,
                    'status' => 'draft',
                    'created_by' => Auth::id(),
                    'field_positions' => $fieldPositions
                ]);
                $this->savedChequeId = $cheque->id;
            }

            session()->flash('success', 'Cheque draft saved successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Error saving draft: ' . $e->getMessage());
        }
    }

    private function numberToWords($num)
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        $thousands = ['', 'Thousand', 'Million', 'Billion'];

        if ($num == 0) return 'ZERO PESOS ONLY';

        $words = '';
        $parts = explode('.', number_format($num, 2, '.', ''));
        $wholePart = (int)$parts[0];
        $centsPart = (int)$parts[1];

        // Convert whole number part
        if ($wholePart === 0) {
        $words = 'Zero';
        } else {
            $thousandIndex = 0;
            while ($wholePart > 0) {
                $chunk = $wholePart % 1000;
                if ($chunk !== 0) {
                    $chunkWords = '';

                    $hundreds = intval($chunk / 100);
                    if ($hundreds > 0) {
                        $chunkWords .= $ones[$hundreds] . ' Hundred ';
                    }

                    $remainder = $chunk % 100;
                    if ($remainder >= 20) {
                        $chunkWords .= $tens[intval($remainder / 10)];
                        if ($remainder % 10 > 0) {
                            $chunkWords .= ' ' . $ones[$remainder % 10];
                        }
                        $chunkWords .= ' ';
                    } else if ($remainder > 0) {
                        $chunkWords .= $ones[$remainder] . ' ';
                    }

                    if ($thousands[$thousandIndex]) {
                        $chunkWords .= $thousands[$thousandIndex] . ' ';
                    }

                    $words = $chunkWords . $words;
                }

                $wholePart = intval($wholePart / 1000);
                $thousandIndex++;
            }
        }

        // Add Pesos
        $words .= 'Pesos';

        // Add cents as a number
        if ($centsPart > 0) {
            $words .= ' And ' . $centsPart . ' Cents';
        } else {
            $words .= ' Only';
        }

        return trim($words);
    }

    public function render()
    {
        return view('livewire.b-f-o.cheque');
    }
}

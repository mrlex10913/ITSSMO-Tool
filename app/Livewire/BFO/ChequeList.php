<?php

namespace App\Livewire\BFO;

use App\Models\BFO\Cheque;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.enduser')]
class ChequeList extends Component
{

    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $dateFrom = '';
    public $dateTo = '';
    public $selectedCheques = [];
    public $selectAll = false;
    public $showFilters = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dateFrom' => ['except' => ''],
        'dateTo' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedCheques = $this->getCheques()->pluck('id')->toArray();
        } else {
            $this->selectedCheques = [];
        }
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->resetPage();
    }

    public function viewCheque($id)
    {
        return redirect()->route('bfo.cheque.view', $id);
    }

    public function editCheque($id)
    {
        return redirect()->route('bfo.cheque.edit', $id);
    }

    public function duplicateCheque($id)
    {
        $cheque = Cheque::find($id);
        if ($cheque) {
            $newCheque = $cheque->replicate();
            $newCheque->cheque_number = Cheque::generateChequeNumber();
            $newCheque->status = 'draft';
            $newCheque->printed_at = null;
            $newCheque->created_by = Auth::id();
            $newCheque->save();

            session()->flash('success', 'Cheque duplicated successfully!');
            $this->resetPage();
        }
    }

    public function voidCheque($id)
    {
        $cheque = Cheque::find($id);
        if ($cheque && $cheque->status !== 'cancelled') {
            $cheque->update(['status' => 'cancelled']);
            session()->flash('success', 'Cheque voided successfully!');
        }
    }

    public function bulkAction($action)
    {
        if (empty($this->selectedCheques)) {
            session()->flash('error', 'Please select cheques first.');
            return;
        }

        switch ($action) {
            case 'void':
                Cheque::whereIn('id', $this->selectedCheques)
                    ->where('status', '!=', 'cancelled')
                    ->update(['status' => 'cancelled']);
                session()->flash('success', count($this->selectedCheques) . ' cheques voided successfully!');
                break;

            case 'mark_issued':
                Cheque::whereIn('id', $this->selectedCheques)
                    ->where('status', 'printed')
                    ->update(['status' => 'issued']);
                session()->flash('success', count($this->selectedCheques) . ' cheques marked as issued!');
                break;

            case 'export':
                return $this->exportSelected();
        }

        $this->selectedCheques = [];
        $this->selectAll = false;
    }

    public function exportSelected()
    {
        $cheques = Cheque::whereIn('id', $this->selectedCheques)->get();

        $filename = 'cheques_export_' . date('Y-m-d_H-i-s') . '.csv';

        return response()->streamDownload(function() use ($cheques) {
            $file = fopen('php://output', 'w');

            // Headers
            fputcsv($file, [
                'Cheque Number',
                'Payee Name',
                'Amount',
                'Amount in Words',
                'Date',
                'Status',
                'Printed At',
                'Created By',
                'Created At'
            ]);

            // Data
            foreach ($cheques as $cheque) {
                fputcsv($file, [
                    $cheque->cheque_number,
                    $cheque->payee_name,
                    $cheque->formatted_amount,
                    $cheque->amount_in_words,
                    $cheque->cheque_date->format('Y-m-d'),
                    ucfirst($cheque->status),
                    $cheque->printed_at ? $cheque->printed_at->format('Y-m-d H:i:s') : '',
                    $cheque->creator->name ?? '',
                    $cheque->created_at->format('Y-m-d H:i:s')
                ]);
            }

            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    public function getCheques()
    {
        return Cheque::query()
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('cheque_number', 'like', '%' . $this->search . '%')
                      ->orWhere('payee_name', 'like', '%' . $this->search . '%')
                      ->orWhere('amount_in_words', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->dateFrom, function($query) {
                $query->whereDate('cheque_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->whereDate('cheque_date', '<=', $this->dateTo);
            })
            ->with('creator')
            ->orderBy('created_at', 'desc');
    }

    public function render()
    {
        $cheques = $this->getCheques()->paginate(10);

        $stats = [
            'total' => Cheque::count(),
            'draft' => Cheque::where('status', 'draft')->count(),
            'printed' => Cheque::where('status', 'printed')->count(),
            'issued' => Cheque::where('status', 'issued')->count(),
            'cleared' => Cheque::where('status', 'cleared')->count(),
            'cancelled' => Cheque::where('status', 'cancelled')->count(),
        ];

        return view('livewire.b-f-o.cheque-list', compact('cheques', 'stats'));
    }
}

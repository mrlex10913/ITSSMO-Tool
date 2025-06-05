<?php

namespace App\Livewire\BFO;

use App\Models\BFO\ChequePayee;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class PayeeUpload extends Component
{
    use WithFileUploads;

    public $showModal = false;
    public $searchTerm = '';
    public $excelFile;
    public $payee_name = '';
    public $editingId = null;

    protected $listeners = ['open-payee-modal' => 'openModal'];

    public function openModal()
    {
        $this->showModal = true;
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->reset(['payee_name', 'editingId', 'excelFile']);
    }

    public function uploadExcel()
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $path = $this->excelFile->store('temp');
            $fullPath = Storage::path($path);

            // Simple CSV reading
            if (($handle = fopen($fullPath, "r")) !== FALSE) {
                $imported = 0;
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    if (!empty($data[0]) && $data[0] !== 'Payee Name') { // Skip header
                        ChequePayee::create(['payee_name' => trim($data[0])]);
                        $imported++;
                    }
                }
                fclose($handle);

                Storage::delete($path);
                session()->flash('success', "Successfully imported {$imported} payees!");
                $this->excelFile = null;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    public function savePayee()
    {
        $this->validate(['payee_name' => 'required|string|max:255']);

        try {
            if ($this->editingId) {
                ChequePayee::find($this->editingId)->update(['payee_name' => $this->payee_name]);
                session()->flash('success', 'Payee updated successfully!');
            } else {
                ChequePayee::create(['payee_name' => $this->payee_name]);
                session()->flash('success', 'Payee added successfully!');
            }

            $this->reset(['payee_name', 'editingId']);
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving payee: ' . $e->getMessage());
        }
    }

    public function editPayee($id)
    {
        $payee = ChequePayee::find($id);
        $this->editingId = $id;
        $this->payee_name = $payee->payee_name;
    }

    public function deletePayee($id)
    {
        try {
            ChequePayee::find($id)->delete();
            session()->flash('success', 'Payee deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting payee: ' . $e->getMessage());
        }
    }

    public function selectPayee($payeeName)
    {
        $this->dispatch('payee-selected', ['name' => $payeeName]);
        $this->closeModal();
    }

    public function downloadTemplate()
    {
        $headers = ['Payee Name'];
        $filename = 'payee_template.csv';

        return response()->streamDownload(function() use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['Sample Payee Name']);
            fclose($file);
        }, $filename, ['Content-Type' => 'text/csv']);
    }


    public function render()
    {
        $payees = ChequePayee::when($this->searchTerm, function($query) {
            $query->where('payee_name', 'like', '%' . $this->searchTerm . '%');
        })
        ->orderBy('payee_name')
        ->paginate(10);

        return view('livewire.b-f-o.payee-upload', compact('payees'));
    }
}

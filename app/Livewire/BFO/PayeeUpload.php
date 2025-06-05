<?php

namespace App\Livewire\BFO;

use App\Models\BFO\ChequePayee;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Livewire\WithPagination;

class PayeeUpload extends Component
{
    use WithFileUploads, WithPagination;

    public $previewData = [];
    public $showPreview = false;
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

            // Get existing payee names from database (case-insensitive)
            $existingPayees = ChequePayee::pluck('payee_name')
                ->map(function($name) {
                    return strtolower(trim($name));
                })
                ->toArray();

            // Track processed names in this upload session
            $processedInSession = [];
            $imported = 0;
            $duplicates = 0;
            $duplicateNames = [];

            // Simple CSV reading
            if (($handle = fopen($fullPath, "r")) !== FALSE) {
                $rowNumber = 0;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $rowNumber++;

                    // Skip empty rows and header row
                    if (empty($data[0]) || $data[0] === 'Payee Name') {
                        continue;
                    }

                    $payeeName = trim($data[0]);
                    $payeeNameLower = strtolower($payeeName);

                    // Check for duplicates in database
                    if (in_array($payeeNameLower, $existingPayees)) {
                        $duplicates++;
                        $duplicateNames[] = $payeeName . " (exists in database)";
                        continue;
                    }

                    // Check for duplicates in current upload session
                    if (in_array($payeeNameLower, $processedInSession)) {
                        $duplicates++;
                        $duplicateNames[] = $payeeName . " (duplicate in file)";
                        continue;
                    }

                    // If we reach here, it's a unique payee name
                    try {
                        ChequePayee::create(['payee_name' => $payeeName]);
                        $processedInSession[] = $payeeNameLower;
                        $imported++;
                    } catch (\Exception $e) {
                        // Handle any database errors for individual records
                        $duplicates++;
                        $duplicateNames[] = $payeeName . " (database error)";
                    }
                }
                fclose($handle);
            }

            Storage::delete($path);

            // Create detailed success message
            $message = "Import completed! Successfully imported {$imported} payee(s).";

            if ($duplicates > 0) {
                $message .= " Skipped {$duplicates} duplicate(s).";

                // Store duplicate details in session for display
                if (!empty($duplicateNames)) {
                    session()->flash('duplicates', array_slice($duplicateNames, 0, 10)); // Show max 10 duplicates
                    if (count($duplicateNames) > 10) {
                        session()->flash('more_duplicates', count($duplicateNames) - 10);
                    }
                }
            }

            session()->flash('success', $message);
            $this->excelFile = null;

        } catch (\Exception $e) {
            session()->flash('error', 'Error uploading file: ' . $e->getMessage());
        }
    }

    public function previewExcel()
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            $path = $this->excelFile->store('temp');
            $fullPath = Storage::path($path);

            $existingPayees = ChequePayee::pluck('payee_name')
                ->map(function($name) {
                    return strtolower(trim($name));
                })
                ->toArray();

            $preview = [];
            $processedInSession = [];

            if (($handle = fopen($fullPath, "r")) !== FALSE) {
                $rowNumber = 0;

                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && count($preview) < 20) {
                    $rowNumber++;

                    if (empty($data[0]) || $data[0] === 'Payee Name') {
                        continue;
                    }

                    $payeeName = trim($data[0]);
                    $payeeNameLower = strtolower($payeeName);

                    $status = 'new';
                    if (in_array($payeeNameLower, $existingPayees)) {
                        $status = 'exists';
                    } elseif (in_array($payeeNameLower, $processedInSession)) {
                        $status = 'duplicate_in_file';
                    } else {
                        $processedInSession[] = $payeeNameLower;
                    }

                    $preview[] = [
                        'name' => $payeeName,
                        'status' => $status,
                        'row' => $rowNumber
                    ];
                }
                fclose($handle);
            }

            Storage::delete($path);
            $this->previewData = $preview;
            $this->showPreview = true;

        } catch (\Exception $e) {
            session()->flash('error', 'Error previewing file: ' . $e->getMessage());
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

<?php

namespace App\Livewire\PAMO;

use App\Models\PAMO\Barcode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Rule;
use Livewire\WithPagination;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;

#[Layout('layouts.enduser')]
class BarcodeGenerator extends Component
{
    use WithPagination;

    #[Rule('required|numeric|min:1|max:1000')]
    public $qty;

    public $totalGeneratedCode;
    public $availableBarcodes;
    public $printedBarcodes;

    #[Rule('required|numeric|min:1')]
    public $printQuantity = 1;

    public $printQty = 1;
    public $printingBarcodeModal = false;
    public $printSize = 'medium';
    public $printLayout = 'standard';

    public $search = '';

    public function generateBarcodes(){

        $this->validate([
            'qty' => 'required|numeric|min:1|max:1000'
        ]);

        $successCount = 0;

        $quantity = $this->qty;

        for($i = 0; $i < $quantity; $i++){
            $barcodeNumber = $this->generateUniqueBarcodeNumber();
            Barcode::create([
                'number' => $barcodeNumber,
                'status' => 'available',
                'is_used' => false,
            ]);
            $successCount++;
        }

        $this->reset(['qty']);
        flash()->success("Successfully generated $successCount barcodes");
        // for($i = 0; $i < $quantity; $i++){
        //     $barcodeNumber = $this->generateUniqueBarcodeNumber();
        //     Barcode::create([
        //         'number' => $barcodeNumber,
        //         'status' => 'Not Printed',
        //     ]);
        //     $this->reset(['qty']);
        //     flash()->success('You have been generated barcode');
        // }
    }

    public function generateUniqueBarcodeNumber(){
        do{
            $barcodeNumber = $this->generateRandomNumericBarcode();
        } while(Barcode::where('number', $barcodeNumber)->exists());
        return $barcodeNumber;
    }

    public function generateRandomNumericBarcode(){
        $firstDigit = mt_rand(1, 9);
        $remainingDigits = str_pad(mt_rand(0, 999999999999), 12, '0', STR_PAD_LEFT);
        return $firstDigit . $remainingDigits;
    }

    public function printModalClick(){
        if ($this->availableBarcodes <= 0) {
            flash()->warning('No available barcodes to print. Please generate more barcodes first.');
            return;
        }

        // Validate the printQty
        $this->validate([
            'printQty' => "required|numeric|min:1|max:{$this->availableBarcodes}"
        ]);

        $this->printQuantity = $this->printQty;
        $this->printingBarcodeModal = true;
    }

    public function printBarcodes()
    {
        try {
            // Validate quantity against available barcodes
            $this->validate([
                'printQuantity' => "required|numeric|min:1|max:{$this->availableBarcodes}"
            ]);

            $barcodes = Barcode::where('is_used', false)
                ->take($this->printQuantity)
                ->get();

            if ($barcodes->isEmpty()) {
                flash()->warning('No available barcodes to print.');
                return;
            }

            // Create directory if it doesn't exist
            $barcodesDir = public_path('barcodes');
            if (!file_exists($barcodesDir)) {
                mkdir($barcodesDir, 0755, true);
            }

            // Generate and save physical PNG barcodes
            $generator = new BarcodeGeneratorPNG();
            foreach($barcodes as $barcode) {
                // Generate barcode with higher quality settings
                $barcodeImage = $generator->getBarcode(
                    $barcode->number,
                    $generator::TYPE_CODE_128,
                    3,  // Width factor (thicker bars)
                    60  // Height in pixels (taller for better scanning)
                );

                // Save the PNG file
                $filename = 'barcode_' . $barcode->number . '.png';
                $filePath = 'barcodes/' . $filename;
                file_put_contents(public_path($filePath), $barcodeImage);

                // Update barcode record with image path
                $barcode->update([
                    'is_used' => true,
                    'status' => 'printed',
                    'image_path' => $filePath
                ]);
            }

            // Generate print layout
            $html = View::make('print-layout', [
                'barcodes' => $barcodes,
                'size' => $this->printSize ?? 'medium',
                'layout' => $this->printLayout ?? 'standard'
            ])->render();

            // Log for debugging
            \Log::info('Print template generated', ['length' => strlen($html)]);

            // Dispatch to frontend
            $this->dispatch('printBarcodes', html: $html);

            $this->printingBarcodeModal = false;
            $this->reset(['printQuantity', 'printQty']);

            flash()->success("Successfully prepared {$barcodes->count()} barcodes for printing");
        } catch (\Exception $e) {
            \Log::error('Print error: ' . $e->getMessage());
            flash()->error('Error printing barcodes: ' . $e->getMessage());
        }
    }
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
         // Calculate metrics
        $this->totalGeneratedCode = Barcode::count();
        $this->availableBarcodes = Barcode::where('is_used', false)->count();
        $this->printedBarcodes = Barcode::where('is_used', true)->count();

        // Get barcodes with search and pagination
        $query = Barcode::query()
            ->when($this->search, function($query) {
                return $query->where('number', 'like', '%'.$this->search.'%');
            })
            ->orderByDesc('created_at');

        $barcodeList = $query->paginate(10);

        // Generate barcode HTML for display
        $barCodeimg = new BarcodeGeneratorHTML();
        foreach($barcodeList as $barcode){
            $barcode->barcode_html = $barCodeimg->getBarcode($barcode->number, $barCodeimg::TYPE_CODE_128);
        }

        return view('livewire.p-a-m-o.barcode-generator', [
            'barcodeList' => $barcodeList,
            'totalGeneratedCode' => $this->totalGeneratedCode,
            'availableBarcodes' => $this->availableBarcodes,
            'printedBarcodes' => $this->printedBarcodes
        ]);
    }
}

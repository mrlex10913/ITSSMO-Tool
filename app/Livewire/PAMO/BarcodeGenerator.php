<?php

namespace App\Livewire\PAMO;

use App\Models\PAMO\Barcode;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeGenerator extends Component
{
    public $qty;
    public $totalGeneratedCode;

    public $printQuantity;
    public $printingBarcodeModal = false;

    public function generateBarcodes($quantity){
        for($i = 0; $i < $quantity; $i++){
            $barcodeNumber = $this->generateUniqueBarcodeNumber();
            Barcode::create([
                'number' => $barcodeNumber,
                'status' => 'Not Printed',
            ]);
            $this->reset(['qty']);
            flash()->success('You have been generated barcode');
        }
    }

    public function generateUniqueBarcodeNumber(){
        do{
            $barcodeNumber = $this->generateRandomNumericBarcode();
        }while(Barcode::where('number', $barcodeNumber)->exists());
        return $barcodeNumber;
    }

    public function generateRandomNumericBarcode(){
        $firstDigit = mt_rand(1, 9);
        $remainingDigits = str_pad(mt_rand(0, 9999999999999), 12, '0', STR_PAD_LEFT);
        return $firstDigit . $remainingDigits;
    }

    public function printModalClick(){
        $this->printQuantity = 1;
        $this->printingBarcodeModal = true;
    }

    public function printBarcodes(){
        $barcodes = Barcode::where('is_used', false)->take($this->printQuantity)->get();

        foreach($barcodes as $barcode){
            $generator = new BarcodeGeneratorPNG();
            $barcodeImage = $generator->getBarcode($barcode->number, $generator::TYPE_CODE_128);
            $barcodePath = 'barcodes/'. $barcode->number . '.png';
            file_put_contents(public_path($barcodePath), $barcodeImage);

            $barcode->update(['is_used' => true, 'image_path' => $barcodePath]);
        }
        $html = View::make('print-layout', ['barcodes' => $barcodes])->render();
        $this->dispatch('printBarcodes', ['html' => $html]);
        $this->printingBarcodeModal = false;
    }

    public function render()
    {
        $barcodeList = Barcode::all();
        $barCodeimg = new BarcodeGeneratorHTML();

        foreach($barcodeList as $barcode){
            $barcode->barcode_html = $barCodeimg->getBarcode($barcode->number, $barCodeimg::TYPE_CODE_128);
        }
        $this->totalGeneratedCode = Barcode::count();
        return view('livewire.p-a-m-o.barcode-generator', [
            'barcodeList' => $barcodeList,
            'totalGeneratedCode' => $this->totalGeneratedCode

        ]);
    }
}

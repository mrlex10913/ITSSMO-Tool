<?php

namespace App\Http\Controllers;

use App\Models\PAMO\Barcode;
use Illuminate\Http\Request;
use Picqer\Barcode\BarcodeGeneratorPNG;

class PrintController extends Controller
{

    public function show(Request $request){

        $quantity = $request->query('quantity', 12); // Default to 12 if not provided
        $barcodes = Barcode::where('status', 'Not Printed')
            ->take($quantity)
            ->get();

        // Generate PNG barcodes for each record
        $barcodeGenerator = new BarcodeGeneratorPNG();
        foreach ($barcodes as $barcode) {
            $barcode->barcode_png = 'data:image/png;base64,' . base64_encode(
                $barcodeGenerator->getBarcode($barcode->number, $barcodeGenerator::TYPE_CODE_128)
            );
        }
        return view('livewire.p-a-m-o.print-barcode-view', compact('barcodes'));
    }
}

<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\print-layout.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcodes</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 0.5cm;
            }
            body {
                margin: 0;
                padding: 0;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            img {
                max-width: 100%;
            }
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 10px;
            padding: 10px;
            background-color: #f5f5f5;
            width: 210mm; /* A4 width */
            margin: 0 auto;
        }

        /* Main container for all barcodes */
        .print-container {
            width: 100%;
            display: flex;
            flex-direction: column;
        }

        /* Four-column row container */
        .barcode-row {
            display: flex;
            width: 100%;
            margin-bottom: 0.2cm;
        }

        /* Column containers - now 4 columns (25% each) */
        .barcode-column {
            flex: 1;
            display: flex;
            flex-wrap: wrap;
            padding: 0 0.1cm;
        }

        /* Small Size - adjusted for 4 columns */
        .size-small .barcode-item {
            width: 2.2cm;
            height: 1.1cm;
        }

        /* Medium Size - adjusted for 4 columns */
        .size-medium .barcode-item {
            width: 4.5cm;
            height: 2.0cm;
        }

        /* Large Size - adjusted for 4 columns */
        .size-large .barcode-item {
            width: 4.5cm;
            height: 2.2cm;
        }

        .barcode-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px dashed #ddd;
            padding: 0.1cm;
            margin: 0.1cm;
            page-break-inside: avoid;
            background-color: white;
            box-sizing: border-box;
        }

        /* Smaller company logo style */
        .company-logo {
            font-size: 6px;
            font-weight: normal;
            color: #555;
            margin-bottom: 0.05cm;
            text-align: center;
        }

        .barcode-image {
            width: 100%;
            text-align: center;
            padding: 0.1cm;
        }

        .barcode-image img {
            max-width: 100%;
            height: auto;
        }

        .barcode-number {
            font-size: 7px;
            margin-top: 0.1cm;
            text-align: center;
            color: black;
        }

        /* Add a subtle dotted line between columns */
        .column-divider {
            border-left: 1px dotted #ccc;
            height: 100%;
            margin: 0 0.1cm;
        }
    </style>
</head>
<body>
    <div class="print-container">
        @forelse($barcodes as $index => $barcode)
            @if($index % 2 == 0)
            <div class="barcode-row">
                <!-- First Column -->
                <div class="barcode-column size-{{ $size }} layout-{{ $layout }}">
                    <div class="barcode-item">
                        @if(isset($layout) && $layout === 'detailed')
                            <div class="company-logo">
                                STI West Negros University
                            </div>
                        @endif

                        <div class="barcode-image">
                            <img
                                src="{{ asset($barcode->image_path) }}?t={{ time() }}"
                                alt="Barcode {{ $barcode->number }}"
                                loading="eager"
                            >
                        </div>

                        @if(!isset($layout) || $layout !== 'compact')
                            <div class="barcode-number">
                                {{ $barcode->number }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="column-divider"></div>

                <!-- Second Column (Duplicate of First) -->
                <div class="barcode-column size-{{ $size }} layout-{{ $layout }}">
                    <div class="barcode-item">
                        @if(isset($layout) && $layout === 'detailed')
                            <div class="company-logo">
                                STI West Negros University
                            </div>
                        @endif

                        <div class="barcode-image">
                            <img
                                src="{{ asset($barcode->image_path) }}?t={{ time() }}"
                                alt="Barcode {{ $barcode->number }}"
                                loading="eager"
                            >
                        </div>

                        @if(!isset($layout) || $layout !== 'compact')
                            <div class="barcode-number">
                                {{ $barcode->number }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="column-divider"></div>

                <!-- Third Column (Different barcode) -->
                <div class="barcode-column size-{{ $size }} layout-{{ $layout }}">
                    <div class="barcode-item">
                        @if(isset($layout) && $layout === 'detailed')
                            <div class="company-logo">
                                STI West Negros University
                            </div>
                        @endif

                        <div class="barcode-image">
                            <img
                                src="{{ asset(isset($barcodes[$index+1]) ? $barcodes[$index+1]->image_path : $barcode->image_path) }}?t={{ time() }}"
                                alt="Barcode {{ isset($barcodes[$index+1]) ? $barcodes[$index+1]->number : $barcode->number }}"
                                loading="eager"
                            >
                        </div>

                        @if(!isset($layout) || $layout !== 'compact')
                            <div class="barcode-number">
                                {{ isset($barcodes[$index+1]) ? $barcodes[$index+1]->number : $barcode->number }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="column-divider"></div>

                <!-- Fourth Column (Duplicate of Third) -->
                <div class="barcode-column size-{{ $size }} layout-{{ $layout }}">
                    <div class="barcode-item">
                        @if(isset($layout) && $layout === 'detailed')
                            <div class="company-logo">
                                STI West Negros University
                            </div>
                        @endif

                        <div class="barcode-image">
                            <img
                                src="{{ asset(isset($barcodes[$index+1]) ? $barcodes[$index+1]->image_path : $barcode->image_path) }}?t={{ time() }}"
                                alt="Barcode {{ isset($barcodes[$index+1]) ? $barcodes[$index+1]->number : $barcode->number }}"
                                loading="eager"
                            >
                        </div>

                        @if(!isset($layout) || $layout !== 'compact')
                            <div class="barcode-number">
                                {{ isset($barcodes[$index+1]) ? $barcodes[$index+1]->number : $barcode->number }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        @empty
            <div style="text-align: center; width: 100%; padding: 20px;">
                No barcodes selected for printing.
            </div>
        @endforelse
    </div>
</body>
</html>

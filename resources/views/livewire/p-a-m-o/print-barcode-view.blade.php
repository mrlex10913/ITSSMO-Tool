<!DOCTYPE html>
<html>
<head>
    <title>Print Barcodes</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .barcode {
            margin-bottom: 20px;
        }
    </style>
</head>
<body onload="window.print()">
    <h1>Barcodes</h1>
    @foreach ($barcodes as $barcode)
        <div class="barcode">
            <p><strong>Number:</strong> {{ $barcode->number }}</p>
            <div>{!! $barcode->barcode_html !!}</div>
        </div>
    @endforeach
</body>
</html>

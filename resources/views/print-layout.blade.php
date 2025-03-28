
<!DOCTYPE html>
<html>
<head>
    <title>Print Barcode</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .barcode { text-align: center; margin: 20px; }
        .barcode img { width: 100%; max-width: 300px; }
    </style>
</head>
<body>
    @foreach($barcodes as $barcode)
        <div class="barcode">
            <p>{{ $barcode->number }}</p>
            <img src="{{ asset($barcode->image_path) }}" alt="Barcode">
        </div>
    @endforeach
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart IDP 51S Direct Print</title>
    <style>
        /* Smart IDP 51S optimized styles */
        @page {
            size: 3.375in 2.125in; /* CR80 standard card size */
            margin: 0;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: white;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .card-side {
            width: 3.375in;
            height: 2.125in;
            page-break-after: always;
            position: relative;
            overflow: hidden;
            display: block;
            box-sizing: border-box;
        }

        .card-side:last-child {
            page-break-after: avoid;
        }

        /* High quality image rendering for card printers */
        img {
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            max-width: 100%;
            height: auto;
        }

        /* Ensure colors print correctly on card printers */
        .bg-blue-700 { background-color: #1d4ed8 !important; }
        .bg-blue-600 { background-color: #2563eb !important; }
        .text-white { color: #ffffff !important; }
        .text-yellow-400 { color: #facc15 !important; }
        .text-gray-800 { color: #1f2937 !important; }
        .text-gray-600 { color: #4b5563 !important; }

        /* Hide screen-only elements */
        @media print {
            .no-print {
                display: none !important;
            }
        }

        /* Font optimization for card printers */
        .font-bold { font-weight: 700; }
        .font-semibold { font-weight: 600; }
        .text-xs { font-size: 0.75rem; }
        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }

        /* Spacing utilities */
        .p-1 { padding: 0.25rem; }
        .p-2 { padding: 0.5rem; }
        .p-3 { padding: 0.75rem; }
        .p-4 { padding: 1rem; }
        .m-1 { margin: 0.25rem; }
        .m-2 { margin: 0.5rem; }
        .m-3 { margin: 0.75rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-3 { margin-top: 0.75rem; }

        /* Layout utilities */
        .text-center { text-align: center; }
        .text-left { text-align: left; }
        .text-right { text-align: right; }
        .relative { position: relative; }
        .absolute { position: absolute; }
        .w-full { width: 100%; }
        .h-full { height: 100%; }
        .flex { display: flex; }
        .block { display: block; }
        .inline-block { display: inline-block; }
        .justify-center { justify-content: center; }
        .items-center { align-items: center; }
        .space-y-1 > * + * { margin-top: 0.25rem; }
        .space-y-2 > * + * { margin-top: 0.5rem; }

        /* Border utilities */
        .border-b { border-bottom: 1px solid; }
        .border-t { border-top: 1px solid; }
        .border-gray-400 { border-color: #9ca3af; }
        .border-gray-600 { border-color: #4b5563; }
        .rounded-xl { border-radius: 0.75rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .rounded { border-radius: 0.25rem; }

        /* Shadow utilities (may not print, but included for consistency) */
        .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }

        /* Overflow utilities */
        .overflow-hidden { overflow: hidden; }

        /* List utilities */
        .list-disc { list-style-type: disc; }
        .list-inside { list-style-position: inside; }

        /* Custom card-specific optimizations */
        .card-content {
            width: 100%;
            height: 100%;
            position: relative;
        }

        /* Quality optimizations for Smart IDP 51S */
        * {
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
    </style>
</head>
<body>
    <!-- Front Side -->
    <div class="card-side">
        {!! $frontHtml !!}
    </div>

    <!-- Back Side -->
    <div class="card-side">
        {!! $backHtml !!}
    </div>

    <!-- Print Instructions (only visible on screen, hidden when printing) -->
    <div class="no-print" style="position: fixed; top: 10px; left: 10px; background: #fff; border: 2px solid #000; padding: 10px; z-index: 9999; font-size: 12px; max-width: 300px;">
        <strong>Smart IDP 51S Printing Instructions:</strong><br>
        <ol style="margin: 5px 0; padding-left: 20px;">
            <li>Ensure Smart IDP 51S is connected and powered on</li>
            <li>Load CR80 blank cards into the printer feeder</li>
            <li>Select "Smart IDP 51S" as your printer in the print dialog</li>
            <li>Choose "Best Quality" or "Photo Quality" settings</li>
            <li>Enable "Print on Both Sides" (Duplex) option</li>
            <li>Set paper size to "CR80" or custom 3.375" x 2.125"</li>
        </ol>
        <button onclick="this.parentElement.style.display='none'" style="padding: 5px 10px; margin-top: 5px;">Hide Instructions</button>
        <button onclick="window.print()" style="padding: 5px 10px; margin-top: 5px; margin-left: 5px; background: #007cba; color: white; border: none; cursor: pointer;">Print Now</button>
    </div>

    <script>
        // Auto-print after a short delay to allow rendering
        setTimeout(function() {
            window.print();
        }, 1000);

        // Optional: Close window after printing
        window.onafterprint = function() {
            setTimeout(function() {
                window.close();
            }, 2000);
        };
    </script>
</body>
</html>

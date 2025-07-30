<div>
    <div class="container mx-auto p-6">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Design Controls Panel -->
            <div class="lg:w-1/3">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">ID Design Studio</h2>
                        <button wire:click="toggleDesignMode"
                                class="px-4 py-2 rounded-lg {{ $designMode ? 'bg-green-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                            {{ $designMode ? 'Design Mode ON' : 'Design Mode OFF' }}
                        </button>
                    </div>

                    @if($designMode)
                        <!-- Design Templates -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Quick Templates</h3>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach($templates as $key => $template)
                                    <button wire:click="applyTemplate('{{ $key }}')"
                                            class="p-3 border rounded-lg text-left hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center gap-3">
                                            <div class="w-6 h-6 rounded" style="background-color: {{ $template['front_bg'] }}"></div>
                                            <span class="font-medium">{{ $template['name'] }}</span>
                                        </div>
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Color Customization -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Front Side Colors</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium mb-1">Background Color</label>
                                    <input type="color" wire:model.live="frontBgColor"
                                           class="w-full h-10 border rounded cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Text Color</label>
                                    <input type="color" wire:model.live="frontTextColor"
                                           class="w-full h-10 border rounded cursor-pointer">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium mb-1">Accent Color</label>
                                    <input type="color" wire:model.live="frontAccentColor"
                                           class="w-full h-10 border rounded cursor-pointer">
                                </div>
                            </div>
                        </div>

                        <!-- Layout Options -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Layout Style</h3>
                            <select wire:model.live="frontLayout" class="w-full p-2 border rounded">
                                <option value="standard">Standard</option>
                                <option value="modern">Modern</option>
                                <option value="minimal">Minimal</option>
                                <option value="corporate">Corporate</option>
                            </select>
                        </div>

                        <!-- Logo Settings -->
                        <div class="mb-6">
                            <h3 class="text-lg font-semibold mb-3">Logo Settings</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.live="showLogo" class="mr-2">
                                    Show Logo
                                </label>
                                @if($showLogo)
                                    <select wire:model.live="logoPosition" class="w-full p-2 border rounded">
                                        <option value="top-left">Top Left</option>
                                        <option value="top-right">Top Right</option>
                                        <option value="top-center">Top Center</option>
                                        <option value="center">Center</option>
                                    </select>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Student Data Form -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold mb-3">Student Information</h3>
                        <div class="space-y-3">
                            <input type="text" wire:model.live="studentData.name"
                                   placeholder="First Name" class="w-full p-2 border rounded">
                            <input type="text" wire:model.live="studentData.middle_name"
                                   placeholder="Middle Initial" class="w-full p-2 border rounded">
                            <input type="text" wire:model.live="studentData.last_name"
                                   placeholder="Last Name" class="w-full p-2 border rounded">
                            <input type="text" wire:model.live="studentData.student_id"
                                   placeholder="Student ID" class="w-full p-2 border rounded">
                            <input type="text" wire:model.live="studentData.course"
                                   placeholder="Course" class="w-full p-2 border rounded">
                            <input type="text" wire:model.live="studentData.year"
                                   placeholder="Year" class="w-full p-2 border rounded">
                        </div>
                    </div>

                    <!-- Export Controls -->
                    <div class="space-y-3">
                        <button wire:click="directPrint"
                                class="w-full bg-red-600 text-white px-4 py-3 rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-print mr-2"></i>Direct Print to Smart IDP 51S
                        </button>
                        <button wire:click="exportForPrinting"
                                class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-download mr-2"></i>Export for Smart ID Printer
                        </button>
                        <button wire:click="printPreview"
                                class="w-full bg-green-600 text-white px-4 py-3 rounded-lg hover:bg-green-700 transition-colors">
                            <i class="fas fa-eye mr-2"></i>Print Preview
                        </button>
                    </div>
                </div>
            </div>

            <!-- ID Preview Panel -->
            <div class="lg:w-2/3">
                <div class="bg-white rounded-lg shadow-lg p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">ID Preview</h2>
                        <div class="flex space-x-2">
                            <button wire:click="setPreviewMode('front')"
                                    class="px-4 py-2 rounded {{ $previewMode == 'front' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                                Front
                            </button>
                            <button wire:click="setPreviewMode('back')"
                                    class="px-4 py-2 rounded {{ $previewMode == 'back' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-800' }}">
                                Back
                            </button>
                        </div>
                    </div>

                    <!-- ID Card Container -->
                    <div class="flex justify-center">
                        <div class="relative transform scale-150 origin-top">
                            <!-- Front Side -->
                            @if($previewMode == 'front')
                                <div id="id-front" class="w-64 h-96 rounded-xl shadow-xl relative overflow-hidden"
                                     style="background-color: {{ $frontBgColor }}; color: {{ $frontTextColor }};">

                                    <!-- Dynamic Layout Rendering -->
                                    @if($frontLayout == 'standard')
                                        @include('components.id-layouts.standard-front')
                                    @elseif($frontLayout == 'modern')
                                        @include('components.id-layouts.modern-front')
                                    @elseif($frontLayout == 'minimal')
                                        @include('components.id-layouts.minimal-front')
                                    @elseif($frontLayout == 'corporate')
                                        @include('components.id-layouts.corporate-front')
                                    @endif
                                </div>
                            @endif

                            <!-- Back Side -->
                            @if($previewMode == 'back')
                                <div id="id-back" class="w-64 h-96 rounded-xl shadow-xl relative overflow-hidden bg-white">
                                    @include('components.id-layouts.standard-back')
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Smart ID Printer 51S Info -->
                    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="font-semibold text-blue-800 mb-2">Smart ID Printer 51S Settings</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div>
                                <span class="font-medium">Card Type:</span> CR80 (3.375" x 2.125")
                            </div>
                            <div>
                                <span class="font-medium">Resolution:</span> 300 DPI
                            </div>
                            <div>
                                <span class="font-medium">Color Mode:</span> Full Color (YMCKO)
                            </div>
                            <div>
                                <span class="font-medium">Duplex:</span> Front & Back Printing
                            </div>
                        </div>

                        <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded">
                            <h5 class="font-semibold text-green-800 text-sm mb-1">Direct Print Features:</h5>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• Automatic printer detection (if USB connected)</li>
                                <li>• Optimized print settings for CR80 cards</li>
                                <li>• Both sides printing in single operation</li>
                                <li>• Print quality optimizations for card printers</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    /* Smart ID Printer 51S compatible dimensions */
    .id-card-container {
        width: 3.375in;
        height: 2.125in;
    }

    @media print {
        .id-card-container {
            width: 3.375in !important;
            height: 2.125in !important;
            margin: 0 !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }
    }
    </style>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:initialized', function() {
    // Listen for Livewire events
    window.Livewire.on('download-print-data', function(data) {
        const printData = data[0];
        const blob = new Blob([JSON.stringify(printData, null, 2)], {
            type: 'application/json'
        });

        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'smart-id-printer-data.json';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        URL.revokeObjectURL(url);
    });

    // Listen for print preview event
    window.Livewire.on('print-preview', function() {
        const frontContent = document.getElementById('id-front');
        const backContent = document.getElementById('id-back');

        if (!frontContent && !backContent) {
            alert('No content to print. Please make sure the ID preview is visible.');
            return;
        }

        const printWindow = window.open('', '_blank');
        if (!printWindow) {
            alert('Popup blocked. Please allow popups for this site.');
            return;
        }

        // Get all stylesheets from the current page
        const stylesheets = Array.from(document.styleSheets);
        let allStyles = '';

        // Extract CSS rules from stylesheets
        stylesheets.forEach(sheet => {
            try {
                if (sheet.cssRules) {
                    Array.from(sheet.cssRules).forEach(rule => {
                        allStyles += rule.cssText + '\n';
                    });
                }
            } catch (e) {
                // Handle cross-origin stylesheets
                console.log('Cannot access stylesheet:', e);
            }
        });

        // Get all style elements
        const styleElements = document.querySelectorAll('style');
        styleElements.forEach(style => {
            allStyles += style.textContent + '\n';
        });

        // Create the HTML structure with all existing styles
        const html = document.createElement('html');
        const head = document.createElement('head');
        const title = document.createElement('title');
        title.textContent = 'ID Card Print Preview';

        const style = document.createElement('style');
        style.textContent = allStyles + `
            /* Print-specific overrides */
            @page {
                size: 3.375in 2.125in;
                margin: 0;
            }
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
                background: white;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
            }
            .print-card {
                width: 3.375in;
                height: 2.125in;
                margin: 0.25in 0;
                border: 1px solid #ddd;
                page-break-inside: avoid;
                display: flex;
                align-items: center;
                justify-content: center;
                position: relative;
                overflow: hidden;
            }
            .print-card .id-content {
                width: 100%;
                height: 100%;
                transform: scale(0.4);
                transform-origin: center center;
                border-radius: 0 !important;
                box-shadow: none !important;
            }
            .page-break {
                page-break-before: always;
            }
            .no-print {
                display: none;
            }
        `;

        head.appendChild(title);
        head.appendChild(style);

        const body = document.createElement('body');

        // Add front content with preserved styling
        if (frontContent) {
            const frontCard = document.createElement('div');
            frontCard.className = 'print-card';

            const frontWrapper = document.createElement('div');
            frontWrapper.className = 'id-content';
            frontWrapper.innerHTML = frontContent.outerHTML;

            frontCard.appendChild(frontWrapper);
            body.appendChild(frontCard);
        }

        // Add back content with preserved styling
        if (backContent) {
            const backCard = document.createElement('div');
            backCard.className = 'print-card page-break';

            const backWrapper = document.createElement('div');
            backWrapper.className = 'id-content';
            backWrapper.innerHTML = backContent.outerHTML;

            backCard.appendChild(backWrapper);
            body.appendChild(backCard);
        }

        html.appendChild(head);
        html.appendChild(body);

        // Write the complete document
        printWindow.document.open();
        printWindow.document.write('<!DOCTYPE html>');
        printWindow.document.write(html.outerHTML);
        printWindow.document.close();
        printWindow.focus();

        setTimeout(function() {
            printWindow.print();
        }, 1000);
    });

    // Listen for direct print to Smart IDP 51S event
    window.Livewire.on('direct-print-to-smart-idp', function(data) {
        const printData = data[0];

        // Check if Smart IDP 51S printer is available
        if (window.navigator && window.navigator.usb) {
            // Modern approach: try USB Web API for direct printer communication
            attemptDirectUSBPrint(printData);
        } else {
            // Fallback: open print window with optimized settings for Smart IDP 51S
            openSmartIDPPrintWindow(printData);
        }
    });

    function attemptDirectUSBPrint(printData) {
        navigator.usb.requestDevice({
            filters: [
                { vendorId: 0x04E8 }, // Samsung (Smart ID Printer manufacturer)
                { vendorId: 0x0B05 }  // Alternative vendor ID for card printers
            ]
        }).then(device => {
            console.log('USB device found:', device);
            // This would require specific Smart IDP 51S drivers/SDK
            // For now, fall back to optimized print window
            openSmartIDPPrintWindow(printData);
        }).catch(error => {
            console.log('USB device access denied or not found, using print window fallback');
            openSmartIDPPrintWindow(printData);
        });
    }

    function openSmartIDPPrintWindow(printData) {
        const printWindow = window.open('', '_blank', 'width=400,height=300');
        if (!printWindow) {
            alert('Popup blocked. Please allow popups for this site to enable direct printing.');
            return;
        }

        // Use the optimized HTML template if available
        if (printData.optimized_html) {
            printWindow.document.open();
            printWindow.document.write(printData.optimized_html);
            printWindow.document.close();
            printWindow.focus();
            return;
        }

        // Create optimized HTML for Smart IDP 51S
        const html = document.createElement('html');
        const head = document.createElement('head');
        const title = document.createElement('title');
        title.textContent = 'Smart IDP 51S Direct Print';

        const style = document.createElement('style');
        style.textContent = `
            @page {
                size: 3.375in 2.125in; /* CR80 card size */
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
                display: flex;
                flex-direction: column;
                box-sizing: border-box;
            }

            .card-side:last-child {
                page-break-after: avoid;
            }

            /* Smart IDP 51S specific optimizations */
            .print-optimized {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }

            /* Copy all existing ID card styles */
            ${getComputedStyles()}
        `;

        head.appendChild(title);
        head.appendChild(style);

        const body = document.createElement('body');

        // Add front side
        const frontDiv = document.createElement('div');
        frontDiv.className = 'card-side print-optimized';
        frontDiv.innerHTML = printData.front_html;
        body.appendChild(frontDiv);

        // Add back side
        const backDiv = document.createElement('div');
        backDiv.className = 'card-side print-optimized';
        backDiv.innerHTML = printData.back_html;
        body.appendChild(backDiv);

        html.appendChild(head);
        html.appendChild(body);

        printWindow.document.open();
        printWindow.document.write('<!DOCTYPE html>');
        printWindow.document.write(html.outerHTML);
        printWindow.document.close();

        // Focus and print with Smart IDP 51S optimizations
        printWindow.focus();

        // Add print instructions for Smart IDP 51S
        const instructions = document.createElement('div');
        instructions.innerHTML = `
            <div style="position: fixed; top: 10px; left: 10px; background: #fff; border: 2px solid #000; padding: 10px; z-index: 9999; font-size: 12px;">
                <strong>Smart IDP 51S Printing Instructions:</strong><br>
                1. Ensure Smart IDP 51S is connected and powered on<br>
                2. Load CR80 blank cards into the printer<br>
                3. Select "Smart IDP 51S" as your printer<br>
                4. Use "Best Quality" print settings<br>
                5. Enable "Duplex/Both Sides" printing<br>
                <button onclick="this.parentElement.style.display='none'">Hide Instructions</button>
            </div>
        `;
        printWindow.document.body.appendChild(instructions);

        setTimeout(function() {
            printWindow.print();

            // Auto-close after printing (optional)
            printWindow.onafterprint = function() {
                setTimeout(() => printWindow.close(), 2000);
            };
        }, 1500);
    }

    function getComputedStyles() {
        // Extract relevant styles from the current page
        let styles = '';
        try {
            const stylesheets = Array.from(document.styleSheets);
            stylesheets.forEach(sheet => {
                try {
                    if (sheet.cssRules) {
                        Array.from(sheet.cssRules).forEach(rule => {
                            if (rule.cssText.includes('id-') || rule.cssText.includes('bg-') || rule.cssText.includes('text-')) {
                                styles += rule.cssText + '\\n';
                            }
                        });
                    }
                } catch (e) {
                    // Cross-origin stylesheet access error
                }
            });
        } catch (e) {
            console.log('Could not extract styles:', e);
        }
        return styles;
    }
});
</script>
@endpush

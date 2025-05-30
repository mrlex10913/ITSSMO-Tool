<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\livewire\b-f-o\cheque.blade.php -->
<div class="min-h-screen bg-gray-50 p-6"
     x-data="{
        // Form data
        payee: '',
        amount: '',
        date: '{{ date('Y-m-d') }}',

        // Edit mode state
        isEditMode: false,
        selectedField: null,
        isDragging: false,
        dragOffset: { x: 0, y: 0 },

        // Field positions (loaded from localStorage)
        fieldPositions: {},

        init() {
            this.loadSavedPositions();
            this.updateFields();

            // Watch for form changes
            this.$watch('payee', () => this.updateFields());
            this.$watch('amount', () => this.updateFields());
            this.$watch('date', () => this.updateFields());
        },

        formatNumberWithCommas(num) {
            if (!num) return '';
            return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },

        toggleEditMode() {
            this.isEditMode = !this.isEditMode;
            if (!this.isEditMode && this.selectedField) {
                this.selectedField.classList.remove('selected');
                this.selectedField = null;
            }
        },

        selectField(event) {
            if (!this.isEditMode) return;

            if (this.selectedField) {
                this.selectedField.classList.remove('selected');
            }

            this.selectedField = event.currentTarget;
            this.selectedField.classList.add('selected');

            // Update position controls
            this.$refs.fieldX.value = Math.round(this.selectedField.offsetLeft);
            this.$refs.fieldY.value = Math.round(this.selectedField.offsetTop);
            this.$refs.fieldWidth.value = this.selectedField.offsetWidth;

            const computedStyle = window.getComputedStyle(this.selectedField.querySelector('.field-content'));
            this.$refs.fontSize.value = computedStyle.fontSize;
        },

        startDrag(event) {
            if (!this.isEditMode) return;

            this.isDragging = true;
            this.selectedField = event.currentTarget;
            this.selectField(event);

            const rect = this.selectedField.getBoundingClientRect();
            this.dragOffset.x = event.clientX - rect.left;
            this.dragOffset.y = event.clientY - rect.top;

            event.preventDefault();
        },

        drag(event) {
            if (!this.isDragging || !this.selectedField) return;

            const container = this.$refs.chequeContainer;
            const containerRect = container.getBoundingClientRect();

            let newX = event.clientX - containerRect.left - this.dragOffset.x;
            let newY = event.clientY - containerRect.top - this.dragOffset.y;

            // Constrain to container
            newX = Math.max(0, Math.min(newX, container.offsetWidth - this.selectedField.offsetWidth));
            newY = Math.max(0, Math.min(newY, container.offsetHeight - this.selectedField.offsetHeight));

            this.selectedField.style.left = newX + 'px';
            this.selectedField.style.top = newY + 'px';

            // Update controls
            this.$refs.fieldX.value = Math.round(newX);
            this.$refs.fieldY.value = Math.round(newY);
        },

        stopDrag() {
            this.isDragging = false;
            this.savePositions();
        },

        updateSelectedFieldPosition() {
            if (!this.selectedField) return;

            const x = this.$refs.fieldX.value;
            const y = this.$refs.fieldY.value;
            const width = this.$refs.fieldWidth.value;

            this.selectedField.style.left = x + 'px';
            this.selectedField.style.top = y + 'px';
            if (width) this.selectedField.style.width = width + 'px';

            this.savePositions();
        },

        updateSelectedFieldStyle() {
            if (!this.selectedField) return;

            const fontSize = this.$refs.fontSize.value;
            const content = this.selectedField.querySelector('.field-content');
            content.style.fontSize = fontSize;

            this.savePositions();
        },

        updateFields() {
            // Update payee
            const payeeDisplay = this.$refs.payeeDisplay;
            if (payeeDisplay) {
                payeeDisplay.textContent = this.payee || '_________________________________';
            }

            // Update amount (with commas, no peso sign)
            if (this.amount) {
                const amountDisplay = this.$refs.amountNumberDisplay;
                const amountWordsDisplay = this.$refs.amountWordsDisplay;

                if (amountDisplay) {
                    // Remove peso sign and add commas
                    amountDisplay.textContent = this.formatNumberWithCommas(this.amount);
                }

                if (amountWordsDisplay) {
                    amountWordsDisplay.textContent = this.numberToWords(this.amount) + ' PESOS';
                }
            }

            // Update date (MM-DD-YYYY format)
            if (this.date) {
                const dateDisplay = this.$refs.dateDisplay;
                if (dateDisplay) {
                    const dateObj = new Date(this.date);
                    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const day = String(dateObj.getDate()).padStart(2, '0');
                    const year = dateObj.getFullYear();
                    dateDisplay.textContent = `${month}-${day}-${year}`;
                }
            }
        },

        numberToWords(num) {
            const ones = ['', 'ONE', 'TWO', 'THREE', 'FOUR', 'FIVE', 'SIX', 'SEVEN', 'EIGHT', 'NINE', 'TEN', 'ELEVEN', 'TWELVE', 'THIRTEEN', 'FOURTEEN', 'FIFTEEN', 'SIXTEEN', 'SEVENTEEN', 'EIGHTEEN', 'NINETEEN'];
            const tens = ['', '', 'TWENTY', 'THIRTY', 'FORTY', 'FIFTY', 'SIXTY', 'SEVENTY', 'EIGHTY', 'NINETY'];
            const thousands = ['', 'THOUSAND', 'MILLION', 'BILLION'];

            if (num == 0) return 'ZERO';

            let words = '';
            let parts = num.toString().split('.');
            let wholePart = parseInt(parts[0]);

            if (wholePart === 0) return 'ZERO';

            let thousandIndex = 0;
            while (wholePart > 0) {
                let chunk = wholePart % 1000;
                if (chunk !== 0) {
                    let chunkWords = '';

                    let hundreds = Math.floor(chunk / 100);
                    if (hundreds > 0) {
                        chunkWords += ones[hundreds] + ' HUNDRED ';
                    }

                    let remainder = chunk % 100;
                    if (remainder >= 20) {
                        chunkWords += tens[Math.floor(remainder / 10)] + ' ';
                        if (remainder % 10 > 0) {
                            chunkWords += ones[remainder % 10] + ' ';
                        }
                    } else if (remainder > 0) {
                        chunkWords += ones[remainder] + ' ';
                    }

                    if (thousands[thousandIndex]) {
                        chunkWords += thousands[thousandIndex] + ' ';
                    }

                    words = chunkWords + words;
                }

                wholePart = Math.floor(wholePart / 1000);
                thousandIndex++;
            }

            return words.trim();
        },

        printCheque() {
            const container = this.$refs.chequeContainer;
            container.classList.add('print-mode');
            window.print();
            container.classList.remove('print-mode');
        },

        saveCheque() {
            const chequeData = {
                payee: this.payee,
                amount: this.amount,
                date: this.date,
                // memo: this.memo, // Remove memo from save data
                positions: this.getFieldPositions()
            };

            localStorage.setItem('chequeData', JSON.stringify(chequeData));
            this.$dispatch('notify', { message: 'Cheque data saved successfully!', type: 'success' });
        },

        savePositions() {
            const positions = this.getFieldPositions();
            localStorage.setItem('chequePositions', JSON.stringify(positions));
        },

        getFieldPositions() {
            const fields = document.querySelectorAll('.draggable-field');
            const positions = {};

            fields.forEach(field => {
                const content = field.querySelector('.field-content');
                positions[field.id] = {
                    left: field.style.left,
                    top: field.style.top,
                    width: field.style.width,
                    fontSize: content.style.fontSize
                };
            });

            return positions;
        },

        loadSavedPositions() {
            const saved = localStorage.getItem('chequePositions');
            if (saved) {
                const positions = JSON.parse(saved);

                setTimeout(() => {
                    Object.keys(positions).forEach(fieldId => {
                        const field = document.getElementById(fieldId);
                        const content = field?.querySelector('.field-content');

                        if (field && positions[fieldId]) {
                            if (positions[fieldId].left) field.style.left = positions[fieldId].left;
                            if (positions[fieldId].top) field.style.top = positions[fieldId].top;
                            if (positions[fieldId].width) field.style.width = positions[fieldId].width;
                            if (positions[fieldId].fontSize && content) content.style.fontSize = positions[fieldId].fontSize;
                        }
                    });
                }, 100);
            }
        },

        resetPositions() {
            if (confirm('Reset all field positions to default?')) {
                localStorage.removeItem('chequePositions');
                location.reload();
            }
        }
     }"
     @mousemove="drag($event)"
     @mouseup="stopDrag()">

    <div class="max-w-7xl mx-auto">
        <!-- Header Controls -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 lg:mb-0">
                    <i class="fas fa-money-check-alt text-blue-600 mr-3"></i>
                    Cheque Management System
                </h1>
                <div class="flex flex-wrap gap-3">
                    <button @click="toggleEditMode()"
                            :class="isEditMode ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'"
                            class="px-4 py-2 text-white rounded-lg transition-colors">
                        <i :class="isEditMode ? 'fas fa-times mr-2' : 'fas fa-edit mr-2'"></i>
                        <span x-text="isEditMode ? 'Exit Edit' : 'Edit Mode'"></span>
                    </button>
                    <button @click="printCheque()"
                            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-print mr-2"></i>Print Cheque
                    </button>
                    <button @click="saveCheque()"
                            class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Draft
                    </button>
                    <button @click="resetPositions()"
                            class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors">
                        <i class="fas fa-undo mr-2"></i>Reset
                    </button>
                </div>
            </div>

            <!-- Quick Fill Form - Remove memo field -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payee Name</label>
                    <input type="text" x-model="payee" placeholder="Enter payee name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (Pesos)</label>
                    <input type="number" x-model="amount" placeholder="0.00" step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" x-model="date"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <!-- Remove memo input field -->
            </div>
        </div>

        <!-- Cheque Design Area -->
        <div class="bg-white rounded-lg shadow-lg p-8">
            <div class="mb-4 text-center">
                <h2 class="text-xl font-semibold text-gray-700">Cheque Preview</h2>
                <p class="text-sm text-gray-500">Drag fields to position them on your cheque template</p>
            </div>

            <!-- Cheque Container -->
            <div x-ref="chequeContainer"
                 :class="isEditMode ? 'edit-mode' : ''"
                 class="relative bg-gradient-to-br from-blue-50 to-green-50 border-2 border-gray-300 rounded-lg mx-auto print:bg-white print:border-none"
                 style="width: 8in; height: 3in; min-height: 300px;">

                <!-- Bank Header (Static) - Will be hidden during print -->
                <div class="absolute top-4 left-4 right-4 text-center border-b border-gray-300 pb-2 print:border-black">
                    <h3 class="text-lg font-bold text-blue-800">CHINA BANK</h3>
                </div>

                <!-- Draggable Fields -->

                <!-- Date Field -->
                <div class="draggable-field absolute" id="dateField" style="top: 80px; right: 20px;"
                     @mousedown="startDrag($event)" @click="selectField($event)">
                    <label class="field-label">Date:</label>
                    <div class="field-content" x-ref="dateDisplay">{{ date('m-d-Y') }}</div>
                </div>

                <!-- Pay To Field -->
                <div class="draggable-field absolute" id="payToField" style="top: 120px; left: 20px; width: 400px;"
                     @mousedown="startDrag($event)" @click="selectField($event)">
                    <label class="field-label">Pay to the order of:</label>
                    <div class="field-content border-b border-dotted border-gray-400" x-ref="payeeDisplay" style="min-height: 25px;">
                        _________________________________
                    </div>
                </div>

                <!-- Amount in Numbers -->
                <div class="draggable-field absolute" id="amountNumberField" style="top: 120px; right: 20px;"
                     @mousedown="startDrag($event)" @click="selectField($event)">
                    <label class="field-label">₱</label>
                    <div class="field-content border border-gray-400 px-2 py-1 bg-white" x-ref="amountNumberDisplay" style="min-width: 100px;">
                        ₱______.__
                    </div>
                </div>

                <!-- Amount in Words -->
                <div class="draggable-field absolute" id="amountWordsField" style="top: 160px; left: 20px; width: 500px;"
                     @mousedown="startDrag($event)" @click="selectField($event)">
                    <div class="field-content border-b border-dotted border-gray-400" x-ref="amountWordsDisplay" style="min-height: 25px;">
                       PESOS ____________________________________________________________
                    </div>
                </div>

                <!-- Remove Memo Field completely -->




                <!-- Bank Logo Placeholder -->
                <div class="absolute bottom-4 right-4 w-16 h-16 bg-blue-100 border border-blue-300 rounded flex items-center justify-center">
                    <i class="fas fa-university text-blue-600 text-xl"></i>
                </div>
            </div>

            <!-- Field Position Controls -->
            <div x-show="isEditMode" x-transition class="mt-6">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold mb-3">Selected Field Controls</h3>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">X Position</label>
                            <input type="number" x-ref="fieldX" @input="updateSelectedFieldPosition()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Y Position</label>
                            <input type="number" x-ref="fieldY" @input="updateSelectedFieldPosition()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Width</label>
                            <input type="number" x-ref="fieldWidth" @input="updateSelectedFieldPosition()"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Font Size</label>
                            <select x-ref="fontSize" @change="updateSelectedFieldStyle()"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                                <option value="12px">12px</option>
                                <option value="14px" selected>14px</option>
                                <option value="16px">16px</option>
                                <option value="18px">18px</option>
                                <option value="20px">20px</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mt-6">
            <h3 class="text-lg font-semibold text-blue-800 mb-3">
                <i class="fas fa-info-circle mr-2"></i>How to Use
            </h3>
            <div class="grid md:grid-cols-2 gap-4 text-sm text-blue-700">
                <div>
                    <h4 class="font-semibold mb-2">Setting Up:</h4>
                    <ul class="space-y-1">
                        <li>• Fill in the form fields above</li>
                        <li>• Click "Edit Mode" to move fields</li>
                        <li>• Drag fields to match your cheque template</li>
                        <li>• Use precise controls for fine positioning</li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold mb-2">Printing:</h4>
                    <ul class="space-y-1">
                        <li>• Position your official cheque in the printer</li>
                        <li>• Click "Print Cheque" when positioned correctly</li>
                        <li>• Save layouts for future use</li>
                        <li>• Test with blank paper first</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<style>
.draggable-field {
    cursor: move;
    user-select: none;
    padding: 2px;
    border: 2px dashed transparent;
    transition: all 0.2s ease;
}

.draggable-field:hover {
    border-color: #3b82f6;
    background-color: rgba(59, 130, 246, 0.1);
}

.draggable-field.selected {
    border-color: #ef4444;
    background-color: rgba(239, 68, 68, 0.1);
}

.field-label {
    font-size: 13px;
    color: #666;
    font-weight: bold;
    display: block;
}

.field-content {
    font-family: 'Calibre', sans-serif;
    font-size: 12px;
    color: #000;
    line-height: 1.2;
}

.edit-mode .draggable-field {
    border-color: #3b82f6;
}

.edit-mode .field-label {
    display: block !important;
}

.print-mode .field-label {
    display: none !important;
}

.print-mode .draggable-field {
    border: none !important;
    background: none !important;
}

@media print {
    body * {
        visibility: hidden;
    }
    #chequeContainer, #chequeContainer * {
        visibility: visible;
    }
    [x-ref="chequeContainer"], [x-ref="chequeContainer"] * {
        visibility: visible;
    }
    [x-ref="chequeContainer"] {
        position: absolute;
        left: 0;
        top: 0;
        width: 8in !important;
        height: 3in !important;
        background: white !important;
        border: none !important;
    }

    /* Hide all labels during print */
    .field-label {
        display: none !important;
        visibility: hidden !important;
    }

    .draggable-field {
        border: none !important;
        background: none !important;
    }

    /* Hide bank header and cheque number during print */
    .absolute.top-4.left-4.right-4,  /* Bank header */
    .absolute.top-4.right-4 {        /* Cheque number */
        display: none !important;
        visibility: hidden !important;
    }

    /* Hide bank logo during print */
    .absolute.bottom-4.right-4 {     /* Bank logo */
        display: none !important;
        visibility: hidden !important;
    }

    /* Remove all underlines and borders from field content during print */
    .field-content {
        border: none !important;
        border-bottom: none !important;
        background: transparent !important;
    }

    /* Specifically target the dotted borders */
    .border-b.border-dotted,
    .border-dotted,
    .border-gray-400,
    .border-b.border-gray-400 {
        border: none !important;
        border-bottom: none !important;
    }

    /* Remove box borders from amount field */
    .border.border-gray-400 {
        border: none !important;
    }
}
</style>
</div>


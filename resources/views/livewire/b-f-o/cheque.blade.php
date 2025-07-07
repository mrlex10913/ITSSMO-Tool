<div class="min-h-screen bg-gray-50 p-6"
     x-data="{
        // Form data
        payee: @entangle('payee'),
        amount: @entangle('amount'),
        date: @entangle('date'),

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

            // Improved watchers that preserve positions
            this.$watch('payee', () => {
                const savedPositions = this.getFieldPositions();
                this.updateFields();
                setTimeout(() => this.applyFieldPositions(savedPositions), 50);
            });

            this.$watch('amount', () => {
                const savedPositions = this.getFieldPositions();
                this.updateFields();
                setTimeout(() => this.applyFieldPositions(savedPositions), 50);
            });

            this.$watch('date', () => {
                const savedPositions = this.getFieldPositions();
                this.updateFields();
                setTimeout(() => this.applyFieldPositions(savedPositions), 50);
            });

            // Add window events for better position persistence
            window.addEventListener('beforeprint', () => this.savePositions());
            window.addEventListener('afterprint', () => setTimeout(() => this.loadSavedPositions(), 100));
        },

        formatNumberWithCommas(num) {
            if (!num) return '';
            return parseFloat(num).toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,');
        },

        toggleEditMode() {
            this.savePositions();

            this.isEditMode = !this.isEditMode;

            if (!this.isEditMode && this.selectedField) {
                this.selectedField.classList.remove('selected');
                this.selectedField = null;
            }

            // Prevent dragging if not in edit mode
            const fields = document.querySelectorAll('.draggable-field');
            fields.forEach(field => {
                field.style.cursor = this.isEditMode ? 'move' : 'default';
            });
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

            if (this.selectedField.id === 'dateField') {
                this.loadDateSpacingControls();
            }
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
            if (!this.selectedField || !this.isEditMode) return;

            const x = this.$refs.fieldX.value;
            const y = this.$refs.fieldY.value;
            const width = this.$refs.fieldWidth.value;

            this.selectedField.style.left = x + 'px';
            this.selectedField.style.top = y + 'px';
            if (width) this.selectedField.style.width = width + 'px';

            this.savePositions();
        },

        updateSelectedFieldStyle() {
            if (!this.selectedField || !this.isEditMode) return;

            const fontSize = this.$refs.fontSize.value;
            const content = this.selectedField.querySelector('.field-content');
            content.style.fontSize = fontSize;

            this.savePositions();
        },

        updateFields() {
            // Update payee
            const savedPositions = this.getFieldPositions();

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
                    // Now includes cents automatically
                    amountWordsDisplay.textContent = this.numberToWords(this.amount);
                }
            }

            // Update date (MM-DD-YYYY format)
            if (this.date) {
                const dateDisplay = this.$refs.dateDisplay;
                if (dateDisplay) {
                    const dateObj = new Date(this.date);

                    // Get date components
                    const month = String(dateObj.getMonth() + 1).padStart(2, '0');
                    const day = String(dateObj.getDate()).padStart(2, '0');
                    const year = String(dateObj.getFullYear());

                    // Clear previous content
                    dateDisplay.innerHTML = '';

                    // Create month digits with custom spacing
                    const monthContainer = document.createElement('span');
                    monthContainer.className = 'date-group month-group';
                    month.split('').forEach(digit => {
                        const span = document.createElement('span');
                        span.className = 'date-digit';
                        span.textContent = digit;
                        monthContainer.appendChild(span);
                    });
                    dateDisplay.appendChild(monthContainer);

                    // Add space between groups (controlled by CSS)
                    const monthDaySpacer = document.createElement('span');
                    monthDaySpacer.className = 'date-spacer month-day-spacer';
                    dateDisplay.appendChild(monthDaySpacer);

                    // Create day digits with custom spacing
                    const dayContainer = document.createElement('span');
                    dayContainer.className = 'date-group day-group';
                    day.split('').forEach(digit => {
                        const span = document.createElement('span');
                        span.className = 'date-digit';
                        span.textContent = digit;
                        dayContainer.appendChild(span);
                    });
                    dateDisplay.appendChild(dayContainer);

                    // Add space between groups (controlled by CSS)
                    const dayYearSpacer = document.createElement('span');
                    dayYearSpacer.className = 'date-spacer day-year-spacer';
                    dateDisplay.appendChild(dayYearSpacer);

                    // Create year digits with custom spacing
                    const yearContainer = document.createElement('span');
                    yearContainer.className = 'date-group year-group';
                    year.split('').forEach(digit => {
                        const span = document.createElement('span');
                        span.className = 'date-digit';
                        span.textContent = digit;
                        yearContainer.appendChild(span);
                    });
                    dateDisplay.appendChild(yearContainer);
                }
            }

            // Restore positions after updating fields
            setTimeout(() => this.applyFieldPositions(savedPositions), 50);
        },

        updateDateSpacing(type) {
            if (!this.selectedField || this.selectedField.id !== 'dateField') return;

            const dateField = this.selectedField;

            switch(type) {
                case 'digit':
                    dateField.style.setProperty('--digit-spacing', `${this.$refs.digitSpacing.value}px`);
                    break;
                case 'month-day':
                    dateField.style.setProperty('--month-day-spacing', `${this.$refs.monthDaySpacing.value}px`);
                    break;
                case 'day-year':
                    dateField.style.setProperty('--day-year-spacing', `${this.$refs.dayYearSpacing.value}px`);
                    break;
                case 'month':
                    dateField.style.setProperty('--month-spacing', `${this.$refs.monthSpacing.value}px`);
                    break;
                case 'day':
                    dateField.style.setProperty('--day-spacing', `${this.$refs.daySpacing.value}px`);
                    break;
                case 'year':
                    dateField.style.setProperty('--year-spacing', `${this.$refs.yearSpacing.value}px`);
                    break;
            }

            this.savePositions(); // Save the updated spacing
        },

        // Update the loadDateSpacingControls to set control values from saved state
        loadDateSpacingControls() {
            if (!this.selectedField || this.selectedField.id !== 'dateField') return;

            const computedStyle = getComputedStyle(this.selectedField);

            // Set spacing control values
            this.$refs.digitSpacing.value = parseInt(computedStyle.getPropertyValue('--digit-spacing') || 0);
            this.$refs.monthDaySpacing.value = parseInt(computedStyle.getPropertyValue('--month-day-spacing') || 10);
            this.$refs.dayYearSpacing.value = parseInt(computedStyle.getPropertyValue('--day-year-spacing') || 10);
            this.$refs.monthSpacing.value = parseInt(computedStyle.getPropertyValue('--month-spacing') || 0);
            this.$refs.daySpacing.value = parseInt(computedStyle.getPropertyValue('--day-spacing') || 0);
            this.$refs.yearSpacing.value = parseInt(computedStyle.getPropertyValue('--year-spacing') || 0);
        },

        numberToWords(num) {
            const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
            const thousands = ['', 'Thousand', 'Million', 'Billion'];

            if (num == 0) return 'Zero Pesos Only';

            let words = '';
            let parts = num.toString().split('.');
            let wholePart = parseInt(parts[0]);
            let centsPart = parts[1] ? parseInt(parts[1].padEnd(2, '0').substring(0, 2)) : 0;

            // Convert whole number part
            if (wholePart === 0) {
                words = 'Zero';
            } else {
                let thousandIndex = 0;
                while (wholePart > 0) {
                    let chunk = wholePart % 1000;
                    if (chunk !== 0) {
                        let chunkWords = '';

                        let hundreds = Math.floor(chunk / 100);
                        if (hundreds > 0) {
                            chunkWords += ones[hundreds] + ' Hundred ';
                        }

                        let remainder = chunk % 100;
                        if (remainder >= 20) {
                            chunkWords += tens[Math.floor(remainder / 10)];
                            if (remainder % 10 > 0) {
                                chunkWords += ' ' + ones[remainder % 10];
                            }
                            chunkWords += ' ';
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
            }

            // Add Pesos
            words += 'Pesos';

            // Add cents if present (as a number)
            if (centsPart > 0) {
                words += ' And ' + centsPart + ' Cents';
            } else {
                words += ' Only';
            }

            return words.trim();
        },

        printCheque() {
            if (!this.payee || !this.amount || !this.date) {
                alert('Please fill in all required fields (Payee, Amount, Date)');
                return;
            }

            // Save positions before proceeding
            this.savePositions();

            const positions = this.getFieldPositions();

            // Use $wire directly since this is the component
            $wire.saveAndPrint(positions);
        },

        saveCheque() {
            if (!this.payee || !this.amount || !this.date) {
                alert('Please fill in all required fields (Payee, Amount, Date)');
                return;
            }

            // Save positions before proceeding
            this.savePositions();

            const positions = this.getFieldPositions();

            // Use $wire directly since this is the component
            $wire.saveDraft(positions);
        },

        proceedWithPrint() {
            // Save positions before printing
            this.savePositions();

            const container = this.$refs.chequeContainer;
            container.classList.add('print-mode');

            // Print
            window.print();

            // Restore after printing
            container.classList.remove('print-mode');

            // Make sure to reload positions after printing
            setTimeout(() => this.loadSavedPositions(), 100);
        },

        savePositions() {
            const positions = this.getFieldPositions();
            localStorage.setItem('chequePositions', JSON.stringify(positions));
        },

        getFieldPositions() {
            const fields = document.querySelectorAll('.draggable-field');
            const positions = {}; // Define positions object first

            fields.forEach(field => {
                const content = field.querySelector('.field-content');
                // Create a new position object for EACH field
                const position = {
                    left: field.style.left,
                    top: field.style.top,
                    width: field.style.width,
                    fontSize: content ? content.style.fontSize : null
                };

                // Add date spacing properties if this is the date field
                if (field.id === 'dateField') {
                    const style = getComputedStyle(field);
                    position.dateSpacing = {
                        digit: style.getPropertyValue('--digit-spacing'),
                        monthDay: style.getPropertyValue('--month-day-spacing'),
                        dayYear: style.getPropertyValue('--day-year-spacing'),
                        month: style.getPropertyValue('--month-spacing'),
                        day: style.getPropertyValue('--day-spacing'),
                        year: style.getPropertyValue('--year-spacing')
                    };
                }

                // Store the position in the positions object
                positions[field.id] = position;
            });

            return positions;
        },

        loadSavedPositions() {
            const saved = localStorage.getItem('chequePositions');
            if (saved) {
                const positions = JSON.parse(saved);

                // Apply positions immediately and with a delay to ensure they're applied
                this.applyFieldPositions(positions);

                // Also apply again after a delay to handle dynamic loading
                setTimeout(() => this.applyFieldPositions(positions), 100);
            }
        },
        applyFieldPositions(positions) {
            Object.keys(positions).forEach(fieldId => {
                const field = document.getElementById(fieldId);
                const content = field?.querySelector('.field-content');

                if (field && positions[fieldId]) {
                    if (positions[fieldId].left) field.style.left = positions[fieldId].left;
                    if (positions[fieldId].top) field.style.top = positions[fieldId].top;
                    if (positions[fieldId].width) field.style.width = positions[fieldId].width;
                    if (positions[fieldId].fontSize && content) content.style.fontSize = positions[fieldId].fontSize;

                    if (fieldId === 'dateField' && positions[fieldId].dateSpacing) {
                        const spacing = positions[fieldId].dateSpacing;
                        if (spacing.digit) field.style.setProperty('--digit-spacing', spacing.digit);
                        if (spacing.monthDay) field.style.setProperty('--month-day-spacing', spacing.monthDay);
                        if (spacing.dayYear) field.style.setProperty('--day-year-spacing', spacing.dayYear);
                        if (spacing.month) field.style.setProperty('--month-spacing', spacing.month);
                        if (spacing.day) field.style.setProperty('--day-spacing', spacing.day);
                        if (spacing.year) field.style.setProperty('--year-spacing', spacing.year);
                    }
                }
            });
        },

        resetPositions() {
            if (confirm('Reset all field positions to default?')) {
                localStorage.removeItem('chequePositions');
                location.reload();
            }
        }
     }"
     @payee-selected-autocomplete.window="payee = $event.detail"
     @payee-selected.window="payee = $event.detail.name"
     @cheque-saved-proceed-print.window="proceedWithPrint()"
     @mousemove="drag($event)"
     @mouseup="stopDrag()">
    {{-- @livewire('b-f-o.cheque-manager', [], key('cheque-manager')) --}}
    <div class="max-w-7xl mx-auto">
        @if (session()->has('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>
                {{ session('success') }}
            </div>
        @endif

        @if (session()->has('error'))
            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>
                {{ session('error') }}
            </div>
        @endif
        <!-- Header Controls -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-4">
                <h1 class="text-3xl font-bold text-gray-800 mb-4 lg:mb-0">
                    <i class="fas fa-money-check-alt text-blue-600 mr-3"></i>
                    Cheque Issuance
                </h1>
                <div class="flex flex-wrap gap-3">
                    <button @click="toggleEditMode()"
                            :class="isEditMode ? 'bg-red-600 hover:bg-red-700' : 'bg-blue-600 hover:bg-blue-700'"
                            class="px-4 py-2 text-white rounded-lg transition-colors">
                        <i :class="isEditMode ? 'fas fa-times mr-2' : 'fas fa-edit mr-2'"></i>
                        <span x-text="isEditMode ? 'Exit Edit' : 'Edit Mode'"></span>
                    </button>

                    <!-- Add this new Upload Payee button -->
                    <button @click="$dispatch('open-payee-modal')"
                            class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors">
                        <i class="fas fa-upload mr-2"></i>Upload Payee
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
                @livewire('b-f-o.payee-autocomplete')
                {{-- <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payee Name</label>
                    <input type="text" x-model="payee" placeholder="Enter payee name"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div> --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (Pesos)</label>
                    <input type="number"
                           x-model="amount"
                           wire:model="amount"
                           placeholder="0.00"
                           step="0.01"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date"
                           x-model="date"
                           wire:model="date"
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
                        ____________________________________________________________
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

            <div x-show="isEditMode && selectedField && selectedField.id === 'dateField'" class="mt-4 bg-gray-100 p-4 rounded-md">
                <h4 class="font-medium mb-2">Date Spacing Controls</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Digit Spacing (px)</label>
                        <input type="number" x-ref="digitSpacing" value="0" min="0" max="20"
                            @input="updateDateSpacing('digit')"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Month-Day Gap (px)</label>
                        <input type="number" x-ref="monthDaySpacing" value="10" min="0" max="50"
                            @input="updateDateSpacing('month-day')"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Day-Year Gap (px)</label>
                        <input type="number" x-ref="dayYearSpacing" value="10" min="0" max="50"
                            @input="updateDateSpacing('day-year')"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Month Letter Spacing (px)</label>
                        <input type="number" x-ref="monthSpacing" value="0" min="0" max="20"
                            @input="updateDateSpacing('month')"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Day Letter Spacing (px)</label>
                        <input type="number" x-ref="daySpacing" value="0" min="0" max="20"
                            @input="updateDateSpacing('day')"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Year Letter Spacing (px)</label>
                        <input type="number" x-ref="yearSpacing" value="0" min="0" max="20"
                            @input="updateDateSpacing('year')"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm">
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
        @include('livewire.b-f-o.partials.upload-payee-modal')
    </div>
<style>

.date-group {
    display: inline-flex;
}

.date-digit {
    display: inline-block;
    margin-right: var(--digit-spacing, 0px);
}

.date-digit:last-child {
    margin-right: 0;
}

.date-spacer {
    display: inline-block;
    width: var(--group-spacing, 0px);
}

.month-group {
    letter-spacing: var(--month-spacing, 0px);
}

.day-group {
    letter-spacing: var(--day-spacing, 0px);
}

.year-group {
    letter-spacing: var(--year-spacing, 0px);
}

.month-day-spacer {
    width: var(--month-day-spacing, 10px);
}

.day-year-spacer {
    width: var(--day-year-spacing, 10px);
}
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
    :not(.edit-mode) .draggable-field {
    cursor: default !important;
    border-color: transparent !important;
    }

    :not(.edit-mode) .draggable-field:hover {
        border-color: transparent !important;
        background-color: transparent !important;
    }
}
</style>
</div>


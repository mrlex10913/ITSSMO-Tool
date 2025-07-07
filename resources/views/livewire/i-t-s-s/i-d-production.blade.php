<!-- filepath: c:\inetpub\wwwroot\ITSSMO-Tool\resources\views\livewire\i-t-s-s\i-d-production.blade.php -->
<div class="relative w-full max-w-md">
    <!-- Front Side of ID Card - College -->
    <div id="college-front" class="rounded-xl shadow-lg aspect-[54/86] relative overflow-hidden">
        <!-- Yellow Top Bar - adjusted for portrait orientation -->
        <div class="bg-yellow-400 h-1/5 w-full p-2">
            <div class="flex justify-between items-center">
                <div class="flex-grow"></div>
                <!-- STI Logo in yellow header -->
                <div class="w-16 h-16">
                    <img src="{{ asset('images/STI Only Clolored.png') }}" alt="STI Logo" class="w-full h-full object-contain">
                </div>
            </div>
        </div>

        <!-- Blue Main Area - adjusted for portrait layout -->
        <div class="bg-blue-600 h-4/5 w-full relative p-3">
            <!-- Watermark design elements -->
            <div class="absolute inset-0 opacity-20 pointer-events-none">
                <div class="absolute bottom-0 right-[-105px] h-3/5">
                    <img src="{{ asset('images/SEAL WHITE LIKE ALEX.png') }}" alt="STI Emblem" class="w-full h-full object-contain">
                </div>
            </div>

            <div class="text-center text-yellow-400 mt-2">
                <h1 class="font-bold text-xl">STI West Negros University</h1>
            </div>

            <!-- Photo Area - positioned for portrait layout -->
            <div class="w-32 h-40 bg-white border-4 border-white rounded-lg mx-auto mt-4">
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-sharp text-gray-400 text-3xl">person</span>
                </div>
            </div>

            <!-- ID Information - repositioned for portrait layout -->
            <div class="text-center text-white mt-6">
                <h3 class="font-bold text-2xl tracking-wider mb-0">Panaguiton</h3>
                <p class="text-white text-sm mb-1">Nikki T.</p>
                <p class="text-white text-sm">BS in Civil Engineering</p>
            </div>

            <!-- Year -->
            <div class="absolute bottom-1 right-2 text-white opacity-60 text-xs">
                1983
            </div>
        </div>
    </div>

    <!-- Back Side of ID Card - adjusted for portrait orientation -->
    <div id="id-back" class="hidden bg-white rounded-xl shadow-lg aspect-[54/86] relative overflow-hidden p-3">
        <div class="text-center text-sm font-semibold">
            <p>Student No.</p>
            <p class="font-bold text-lg mb-1">23-6067-990</p>
        </div>

        <!-- Signature Line -->
        <div class="w-full border-b border-gray-500 mt-2 mb-1"></div>
        <p class="text-[10px] text-gray-600 text-center mb-2">Student's Signature</p>

        <!-- Address - adjusted spacing for portrait layout -->
        <div class="text-xs">
            <p class="font-semibold">Home Address:</p>
            <p class="mb-2">131 Prk Sawmill 3 Brgy. Beta, Bacolod City, Negros Occidental</p>

            <p class="font-semibold">In case of emergency, please contact:</p>
            <p>Erna Besmanos</p>
            <p class="mb-3">9469065822</p>
        </div>

        <!-- Admin Signature -->
        <div class="text-center mt-3">
            <div class="w-28 mx-auto border-b border-gray-600 mb-1"></div>
            <p class="text-xs font-semibold">Ryan Mark S. Molina</p>
            <p class="text-xs">Executive Vice President</p>
        </div>

        <!-- University Info -->
        <div class="text-xs text-center font-semibold mt-3">
            <p>STI West Negros University</p>
            <p>Burgos St., Brgy. Villamonte</p>
            <p>Bacolod City</p>
            <p>Tel.: (034) 434-4561</p>
        </div>

        <!-- Divider -->
        <div class="border-t border-gray-400 my-2"></div>

        <!-- Reminders Section -->
        <div class="text-[9px]">
            <p class="font-semibold">Reminders:</p>
            <ul class="list-disc list-inside">
                <li>This ID is exclusively for College students</li>
                <li>Students are required to wear this ID inside STI premises</li>
                <li>This ID is not valid without the validation sticker for the current term and school year</li>
                <li>Tampering in any way invalidates the ID</li>
                <li>This ID is non-transferrable</li>
                <li>If found, please return to the owner or campus</li>
            </ul>
        </div>
    </div>
    <!-- Front/Back Toggle Controls -->
<div class="mt-6 flex justify-center space-x-4">
    <button id="show-front" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50">
        Show Front
    </button>
    <button id="show-back" class="px-4 py-2 bg-gray-200 text-gray-800 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-opacity-50">
        Show Back
    </button>
</div>

<!-- JavaScript for Front/Back toggling -->
<script>
    document.getElementById('show-front').addEventListener('click', function() {
        document.getElementById('college-front').classList.remove('hidden');
        document.getElementById('id-back').classList.add('hidden');
        this.classList.add('bg-blue-600', 'text-white');
        this.classList.remove('bg-gray-200', 'text-gray-800');
        document.getElementById('show-back').classList.add('bg-gray-200', 'text-gray-800');
        document.getElementById('show-back').classList.remove('bg-blue-600', 'text-white');
    });

    document.getElementById('show-back').addEventListener('click', function() {
        document.getElementById('college-front').classList.add('hidden');
        document.getElementById('id-back').classList.remove('hidden');
        this.classList.add('bg-blue-600', 'text-white');
        this.classList.remove('bg-gray-200', 'text-gray-800');
        document.getElementById('show-front').classList.add('bg-gray-200', 'text-gray-800');
        document.getElementById('show-front').classList.remove('bg-blue-600', 'text-white');
    });
</script>
</div>



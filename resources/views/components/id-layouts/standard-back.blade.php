<!-- Standard Back Layout -->
<div class="w-full h-full bg-white text-gray-800 p-3 text-xs">
    <!-- Student ID Header -->
    <div class="text-center mb-3">
        <p class="font-semibold">Student No.</p>
        <p class="font-bold text-sm">{{ $studentData['student_id'] }}</p>
    </div>

    <!-- Signature Line -->
    <div class="mb-3">
        <div class="w-full border-b border-gray-400 mb-1"></div>
        <p class="text-center text-xs text-gray-600">Student's Signature</p>
    </div>

    <!-- Address Section -->
    <div class="mb-3">
        <p class="font-semibold text-xs mb-1">Home Address:</p>
        <p class="text-xs mb-2">{{ $studentData['address'] ?? '123 Main St., City, Province' }}</p>

        <p class="font-semibold text-xs mb-1">In case of emergency, please contact:</p>
        <p class="text-xs">{{ $studentData['emergency_contact'] ?? 'Emergency Contact' }}</p>
        <p class="text-xs mb-2">{{ $studentData['emergency_phone'] ?? '09123456789' }}</p>
    </div>

    <!-- Admin Signature -->
    <div class="text-center mb-3">
        <div class="w-20 mx-auto border-b border-gray-600 mb-1"></div>
        <p class="text-xs font-semibold">Ryan Mark S. Molina</p>
        <p class="text-xs">Executive Vice President</p>
    </div>

    <!-- University Info -->
    <div class="text-xs text-center font-semibold mb-2">
        <p>STI West Negros University</p>
        <p>Burgos St., Brgy. Villamonte</p>
        <p>Bacolod City</p>
        <p>Tel.: (034) 434-4561</p>
    </div>

    <!-- Divider -->
    <div class="border-t border-gray-400 my-2"></div>

    <!-- Reminders Section -->
    <div class="text-xs">
        <p class="font-semibold mb-1">Reminders:</p>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
            <li>This ID is exclusively for College students</li>
            <li>Students are required to wear this ID inside STI premises</li>
            <li>This ID is not valid without validation sticker</li>
            <li>Tampering in any way invalidates the ID</li>
            <li>This ID is non-transferrable</li>
            <li>If found, please return to owner or campus</li>
        </ul>
    </div>
</div>

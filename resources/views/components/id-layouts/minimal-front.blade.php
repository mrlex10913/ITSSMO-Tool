<!-- Minimal Front Layout -->
<div class="w-full h-full relative bg-white text-gray-800 p-6">
    <!-- Clean Header -->
    <div class="text-center mb-6">
        @if($showLogo && $logoPosition == 'top-center')
            <div class="w-8 h-8 mx-auto mb-2">
                <img src="{{ asset('images/STI Only Clolored.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
        @endif
        <h1 class="text-xs font-light tracking-widest" style="color: {{ $frontBgColor }};">STI WEST NEGROS UNIVERSITY</h1>
    </div>

    <!-- Centered Photo -->
    <div class="flex justify-center mb-4">
        <div class="w-16 h-20 bg-gray-100 border overflow-hidden">
            @if($studentData['photo_url'])
                <img src="{{ $studentData['photo_url'] }}" alt="Student Photo" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-sharp text-gray-400 text-sm">person</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Clean Typography -->
    <div class="text-center space-y-2">
        <h2 class="text-sm font-medium tracking-wide">{{ $studentData['name'] }} {{ $studentData['middle_name'] }} {{ $studentData['last_name'] }}</h2>
        <p class="text-xs text-gray-600">{{ $studentData['course'] }}</p>
        <div class="w-8 h-px bg-gray-300 mx-auto"></div>
        <p class="text-xs text-gray-500">{{ $studentData['student_id'] }}</p>
    </div>

    <!-- Simple Footer -->
    <div class="absolute bottom-3 right-3">
        <span class="text-xs text-gray-400">{{ $studentData['year'] }}</span>
    </div>
</div>

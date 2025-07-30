<!-- Corporate Front Layout -->
<div class="w-full h-full relative">
    <!-- Professional Header -->
    <div class="h-16 w-full flex items-center px-4" style="background: linear-gradient(135deg, {{ $frontBgColor }} 0%, {{ $frontAccentColor }} 100%);">
        @if($showLogo)
            <div class="w-8 h-8 bg-white rounded p-1 mr-2">
                <img src="{{ asset('images/STI Only Clolored.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
        @endif
        <div class="text-white">
            <h1 class="text-xs font-bold">STI WEST NEGROS</h1>
            <p class="text-xs opacity-90">UNIVERSITY</p>
        </div>
    </div>

    <!-- Professional Content Layout -->
    <div class="px-4 py-3 h-80 bg-gray-50">
        <!-- Photo and Basic Info Side by Side -->
        <div class="flex items-start gap-3 mb-4">
            <!-- Professional Photo -->
            <div class="w-16 h-20 bg-white border shadow-sm overflow-hidden">
                @if($studentData['photo_url'])
                    <img src="{{ $studentData['photo_url'] }}" alt="Student Photo" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="material-symbols-sharp text-gray-400 text-sm">person</span>
                    </div>
                @endif
            </div>

            <!-- Professional Info Layout -->
            <div class="flex-1 text-gray-800">
                <h3 class="font-bold text-sm mb-1">{{ strtoupper($studentData['last_name']) }}</h3>
                <p class="text-xs mb-1">{{ $studentData['name'] }} {{ $studentData['middle_name'] }}</p>
                <p class="text-xs text-gray-600">{{ $studentData['course'] }}</p>
            </div>
        </div>

        <!-- Professional Details -->
        <div class="bg-white p-2 rounded shadow-sm text-xs">
            <div class="flex justify-between mb-1">
                <span class="text-gray-600">Student ID:</span>
                <span class="font-medium">{{ $studentData['student_id'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Academic Year:</span>
                <span class="font-medium">{{ $studentData['year'] }}</span>
            </div>
        </div>
    </div>

    <!-- Professional Footer -->
    <div class="absolute bottom-0 left-0 right-0 h-8 flex items-center justify-center text-xs text-white"
         style="background-color: {{ $frontBgColor }};">
        <span>AUTHORIZED PERSONNEL ONLY</span>
    </div>
</div>

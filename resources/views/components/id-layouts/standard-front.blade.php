<!-- Standard Front Layout -->
<div class="w-full h-full relative">
    <!-- Header Section with Accent Color -->
    <div class="h-20 w-full p-3 flex items-center justify-between"
         style="background-color: {{ $frontAccentColor }};">
        @if($showLogo && in_array($logoPosition, ['top-left', 'top-center', 'top-right']))
            <div class="flex {{ $logoPosition == 'top-center' ? 'justify-center w-full' : ($logoPosition == 'top-right' ? 'justify-end w-full' : '') }}">
                <div class="w-12 h-12">
                    <img src="{{ asset('images/STI Only Clolored.png') }}" alt="Logo" class="w-full h-full object-contain">
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Area -->
    <div class="px-4 py-3 h-72">
        <!-- Institution Name -->
        <div class="text-center mb-4" style="color: {{ $frontAccentColor }};">
            <h1 class="font-bold text-sm leading-tight">STI West Negros University</h1>
        </div>

        <!-- Photo Area -->
        <div class="w-20 h-24 bg-white border-2 border-white rounded mx-auto mb-3">
            @if($studentData['photo_url'])
                <img src="{{ $studentData['photo_url'] }}" alt="Student Photo" class="w-full h-full object-cover rounded">
            @else
                <div class="w-full h-full flex items-center justify-center">
                    <span class="material-symbols-sharp text-gray-400 text-lg">person</span>
                </div>
            @endif
        </div>

        <!-- Student Information -->
        <div class="text-center space-y-1">
            <h3 class="font-bold text-lg tracking-wide">{{ $studentData['last_name'] }}</h3>
            <p class="text-sm">{{ $studentData['name'] }} {{ $studentData['middle_name'] }}</p>
            <p class="text-xs">{{ $studentData['course'] }}</p>
        </div>

        <!-- Student ID (if visible) -->
        <div class="text-center mt-2">
            <p class="text-xs opacity-80">{{ $studentData['student_id'] }}</p>
        </div>
    </div>

    <!-- Footer Year -->
    <div class="absolute bottom-2 right-3">
        <span class="text-xs opacity-60">{{ $studentData['year'] }}</span>
    </div>

    <!-- Watermark/Background Design -->
    <div class="absolute inset-0 opacity-10 pointer-events-none">
        <div class="absolute bottom-0 right-[-40px] h-32 w-32">
            <img src="{{ asset('images/SEAL WHITE LIKE ALEX.png') }}" alt="Watermark" class="w-full h-full object-contain">
        </div>
    </div>
</div>

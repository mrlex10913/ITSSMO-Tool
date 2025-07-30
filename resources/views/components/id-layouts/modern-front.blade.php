<!-- Modern Front Layout -->
<div class="w-full h-full relative overflow-hidden">
    <!-- Geometric Background Pattern -->
    <div class="absolute inset-0 opacity-20">
        <div class="absolute top-0 right-0 w-32 h-32 transform rotate-45 translate-x-16 -translate-y-16"
             style="background: linear-gradient(45deg, {{ $frontAccentColor }} 0%, transparent 70%);"></div>
        <div class="absolute bottom-0 left-0 w-24 h-24 transform -rotate-45 -translate-x-12 translate-y-12"
             style="background: linear-gradient(-45deg, {{ $frontAccentColor }} 0%, transparent 70%);"></div>
    </div>

    <!-- Header with Logo -->
    @if($showLogo)
        <div class="absolute top-3 {{ $logoPosition == 'top-right' ? 'right-3' : 'left-3' }}">
            <div class="w-10 h-10 bg-white rounded-full p-1.5">
                <img src="{{ asset('images/STI Only Clolored.png') }}" alt="Logo" class="w-full h-full object-contain">
            </div>
        </div>
    @endif

    <!-- Main Content -->
    <div class="flex flex-col h-full justify-center items-center p-4">
        <!-- Institution Name -->
        <div class="text-center mb-4">
            <h1 class="font-bold text-sm" style="color: {{ $frontAccentColor }};">STI West Negros</h1>
            <h2 class="font-semibold text-xs" style="color: {{ $frontAccentColor }};">University</h2>
        </div>

        <!-- Photo with Modern Border -->
        <div class="relative mb-4">
            <div class="w-20 h-24 bg-white rounded-lg shadow-lg overflow-hidden border-2"
                 style="border-color: {{ $frontAccentColor }};">
                @if($studentData['photo_url'])
                    <img src="{{ $studentData['photo_url'] }}" alt="Student Photo" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center" style="color: {{ $frontAccentColor }};">
                        <span class="material-symbols-sharp text-lg">person</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Student Info with Modern Typography -->
        <div class="text-center space-y-1">
            <h3 class="font-bold text-base tracking-wider uppercase">{{ $studentData['last_name'] }}</h3>
            <p class="text-sm font-medium">{{ $studentData['name'] }} {{ $studentData['middle_name'] }}</p>
            <div class="w-12 h-0.5 mx-auto" style="background-color: {{ $frontAccentColor }};"></div>
            <p class="text-xs font-medium">{{ $studentData['course'] }}</p>
        </div>
    </div>

    <!-- Bottom Info -->
    <div class="absolute bottom-3 left-3 right-3 flex justify-between items-center text-xs">
        <span class="opacity-80">{{ $studentData['student_id'] }}</span>
        <span class="opacity-60">{{ $studentData['year'] }}</span>
    </div>
</div>

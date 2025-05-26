<a wire:navigate
   href="{{ $href }}"
   {{ $attributes->merge([
       'class' => 'flex items-center p-2 rounded-lg backdrop-blur-sm transition-colors duration-200 ' .
                  ($isActive() ? 'bg-sky-500 text-white' : 'text-white hover:bg-blue-500')
   ]) }}>
    @if($icon)
        <span class="material-symbols-sharp">{{ $icon }}</span>
    @endif
    <span class="font-medium ml-3">{{ $slot }}</span>
</a>

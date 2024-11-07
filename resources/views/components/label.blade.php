@props(['value'])

{{-- <label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700 dark:text-gray-300']) }}>
    {{ $value ?? $slot }}
</label> --}}
<label {{ $attributes->merge(['class' => 'block text-sm font-medium mb-1']) }}>
    {{ $value ?? $slot }}
</label>

@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-maroon-800']) }}>
    {{ $value ?? $slot }}
</label>


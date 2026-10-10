@props(['disabled' => false])

<select @disabled($disabled) {{ $attributes->merge(['class' => 'border-cream-border focus:border-maroon-700 focus:ring-maroon-700 rounded-xl shadow-sm text-sm text-charcoal-dark']) }}>
    {{ $slot }}
</select>

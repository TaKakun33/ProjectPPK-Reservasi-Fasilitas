<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-maroon-800 border border-transparent rounded-md font-semibold text-xs text-cream-100 uppercase tracking-widest hover:bg-maroon-900 focus:bg-maroon-900 active:bg-maroon-900 focus:outline-none focus:ring-2 focus:ring-maroon-700 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>


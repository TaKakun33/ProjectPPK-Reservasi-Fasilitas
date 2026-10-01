<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gold-400 border border-transparent rounded-md font-semibold text-xs text-navy-900 uppercase tracking-widest hover:bg-gold-500 focus:bg-gold-500 active:bg-gold-600 focus:outline-none focus:ring-2 focus:ring-gold-400 focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>

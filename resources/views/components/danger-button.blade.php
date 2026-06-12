<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-7 py-2.5 bg-gradient-to-r from-red-500 to-rose-600 border border-transparent rounded-full font-bold text-[11px] text-white uppercase tracking-widest shadow-lg shadow-red-500/20 hover:shadow-red-500/40 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-red-400 focus:ring-offset-2 disabled:opacity-50 transition-all duration-300']) }}>
    {{ $slot }}
</button>

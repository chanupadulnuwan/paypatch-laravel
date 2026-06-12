<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center px-7 py-2.5 bg-white border-2 border-[#6C3AF4]/30 rounded-full font-bold text-[11px] text-[#6C3AF4] uppercase tracking-widest hover:bg-[#6C3AF4]/5 hover:border-[#6C3AF4]/60 focus:outline-none focus:ring-2 focus:ring-[#6C3AF4]/20 focus:ring-offset-2 disabled:opacity-50 transition-all duration-300']) }}>
    {{ $slot }}
</button>

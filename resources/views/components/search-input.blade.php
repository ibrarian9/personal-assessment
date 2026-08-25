@props(['name' => 'search', 'placeholder' => 'Cari data...', 'value' => ''])
<div class="relative flex-1 min-w-[220px]">
    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
        </svg>
    </div>
    <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}"
           class="w-full pl-9 pr-3.5 py-2 text-sm rounded-xl border border-slate-300 bg-white placeholder-slate-400 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all">
</div>

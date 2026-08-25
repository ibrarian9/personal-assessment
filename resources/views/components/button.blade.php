@props(['type' => 'button', 'variant' => 'primary', 'href' => null, 'class' => ''])
@php
    $base = "inline-flex items-center justify-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-xl transition-all shadow-xs focus:ring-2 focus:outline-none ";
    $variants = [
        'primary' => 'bg-teal-600 text-white hover:bg-teal-700 focus:ring-teal-500/30',
        'secondary' => 'bg-slate-800 text-white hover:bg-slate-900 focus:ring-slate-500/30',
        'outline' => 'border border-slate-300 text-slate-700 bg-white hover:bg-slate-50 focus:ring-slate-300',
        'danger' => 'bg-rose-50 text-rose-600 hover:bg-rose-100 focus:ring-rose-500/30',
        'subtle' => 'bg-teal-50 text-teal-700 hover:bg-teal-100 focus:ring-teal-500/30',
    ];
    $classes = $base . ($variants[$variant] ?? $variants['primary']) . ' ' . $class;
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif

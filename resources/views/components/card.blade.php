@props(['class' => ''])
<div {{ $attributes->merge(['class' => 'rounded-2xl bg-white p-6 shadow-xs border border-slate-200/80 space-y-6 ' . $class]) }}>
    {{ $slot }}
</div>

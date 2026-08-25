@props(['class' => ''])
<div class="overflow-x-auto rounded-xl border border-slate-200">
    <table {{ $attributes->merge(['class' => 'w-full text-left text-sm text-slate-600 ' . $class]) }}>
        {{ $slot }}
    </table>
</div>

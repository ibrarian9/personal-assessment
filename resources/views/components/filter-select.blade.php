@props(['name', 'onchange' => null, 'class' => ''])
<select name="{{ $name }}" @if($onchange) onchange="{{ $onchange }}" @endif
        class="py-2 px-3 text-sm font-medium rounded-xl border border-slate-300 bg-white text-slate-700 focus:border-teal-500 focus:outline-none focus:ring-2 focus:ring-teal-500/20 transition-all {{ $class }}">
    {{ $slot }}
</select>

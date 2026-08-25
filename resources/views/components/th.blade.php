@props(['align' => 'left', 'class' => ''])
@php
    $alignClass = match($align) {
        'center' => 'text-center',
        'right' => 'text-right',
        default => 'text-left',
    };
@endphp
<th {{ $attributes->merge(['class' => "py-3.5 px-4 {$alignClass} {$class}"]) }}>
    {{ $slot }}
</th>

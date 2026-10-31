@props([
    'id',
    'title' => '',
    'size' => 'md', // sm, md, lg, xl
    'icon' => null,
])

@php
    $sizeClass = match($size) {
        'sm' => 'max-w-md',
        'lg' => 'modal__container--lg max-w-2xl',
        'xl' => 'modal__container--xl max-w-4xl',
        default => 'max-w-xl',
    };
@endphp

<div class="modal micromodal-slide" id="{{ $id }}" aria-hidden="true">
    <div class="modal__overlay" tabindex="-1" data-micromodal-close>
        <div class="modal__container {{ $sizeClass }} rounded-3xl bg-white p-6 sm:p-8 shadow-2xl border border-slate-200" role="dialog" aria-modal="true" aria-labelledby="{{ $id }}-title">
            <header class="modal__header flex items-center justify-between pb-4 border-b border-slate-100">
                <div class="flex items-center gap-3">
                    @if($icon)
                        <div class="p-2 rounded-xl bg-teal-50 text-teal-600 border border-teal-100/80 shrink-0">
                            {{ $icon }}
                        </div>
                    @endif
                    <div>
                        <h3 class="modal__title text-lg font-bold text-slate-900" id="{{ $id }}-title">
                            {{ $title }}
                        </h3>
                        @if(isset($subtitle))
                            <p class="text-xs text-slate-500 mt-0.5">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                <button type="button" class="modal__close p-1.5 text-slate-400 hover:text-slate-600 rounded-xl hover:bg-slate-100 transition-colors" aria-label="Close modal" data-micromodal-close>
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </header>

            <main class="modal__content text-sm text-slate-600 my-4" id="{{ $id }}-content">
                {{ $slot }}
            </main>

            @if(isset($footer))
                <footer class="modal__footer flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    {{ $footer }}
                </footer>
            @endif
        </div>
    </div>
</div>

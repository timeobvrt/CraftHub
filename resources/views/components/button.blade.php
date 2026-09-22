@props([
    'href' => null,
    'type' => 'button',
])

@php
    $classes = '
        inline-flex items-center justify-center gap-2
        border border-crafthub/70
        bg-crafthub
        px-5 py-2.5
        text-sm font-semibold text-background
        transition-colors duration-150
        hover:bg-crafthub-light
        focus:outline-none
        focus-visible:ring-2
        focus-visible:ring-crafthub/30
        disabled:pointer-events-none
        disabled:opacity-50
        font-pixel
        cursor-pointer
    ';
@endphp

@if ($href)
    <a
        href="{{ $href }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $classes]) }}
    >
        {{ $slot }}
    </button>
@endif

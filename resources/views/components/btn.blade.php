{{--
    Button Component - AdminLTE 3 / Bootstrap 4
    
    Props: type, class, icon, size, href, loading, disabled
--}}

@props([
    'type' => 'button',
    'class' => 'btn-primary',
    'icon' => null,
    'size' => null,
    'href' => null,
    'loading' => false,
    'disabled' => false,
])

@php
    $btnClass = 'btn ' . $class;
    
    if ($size) {
        $btnClass .= ' btn-' . $size;
    }
    
    if ($loading || $disabled) {
        $btnClass .= ' disabled';
    }
@endphp

@if($href && !$disabled && !$loading)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $btnClass]) }}>
        @if($icon)
            <i class="{{ $icon }} mr-1"></i>
        @endif
        {{ $slot }}
    </a>
@else
    <button 
        type="{{ $type }}" 
        {{ $attributes->merge(['class' => $btnClass]) }}
        @if($disabled || $loading) disabled @endif
    >
        @if($loading)
            <span class="spinner-border spinner-border-sm mr-1" role="status" aria-hidden="true"></span>
        @elseif($icon)
            <i class="{{ $icon }} mr-1"></i>
        @endif
        {{ $slot }}
    </button>
@endif

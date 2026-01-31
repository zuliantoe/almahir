{{--
Alert Component
Usage: <x-alert type="success" message="Your message" dismissible />
Types: success, danger, warning, info
--}}
@props([
    'type' => 'info',
    'message' => '',
    'dismissible' => false,
])

@php
$alertClasses = [
    'success' => 'alert-success',
    'danger' => 'alert-danger',
    'warning' => 'alert-warning',
    'info' => 'alert-info',
];
$alertClass = $alertClasses[$type] ?? 'alert-info';
$icon = match($type) {
    'success' => 'fas fa-check-circle',
    'danger' => 'fas fa-exclamation-triangle',
    'warning' => 'fas fa-exclamation-circle',
    'info' => 'fas fa-info-circle',
    default => 'fas fa-info-circle',
};
@endphp

<div {{ $attributes->merge(['class' => "alert {$alertClass}" . ($dismissible ? ' alert-dismissible fade show' : '')]) }}
     role="alert">
    @if($dismissible)
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    @endif
    <i class="{{ $icon }} mr-2"></i>
    {{ $message ?? $slot }}
</div>

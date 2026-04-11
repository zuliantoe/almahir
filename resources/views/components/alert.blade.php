{{--
Alert Component (SweetAlert2 Standard)
Usage: <x-alert type="success" message="Your message" />
Types: success, danger, warning, info
--}}
@props([
    'type' => 'info',
    'message' => '',
])

@php
$swalType = match($type) {
    'success' => 'success',
    'danger', 'error' => 'error',
    'warning' => 'warning',
    'info' => 'info',
    default => 'info',
};
$title = $message ?: $slot;
@endphp

<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            Toast.fire({
                icon: '{{ $swalType }}',
                title: '{!! addslashes($title) !!}'
            });
        }
    });
</script>

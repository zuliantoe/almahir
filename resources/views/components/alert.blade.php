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
            const AlertModal = Swal.mixin({
                position: 'center',
                showConfirmButton: true,
                confirmButtonText: 'Tutup',
                confirmButtonColor: '#3085d6',
                timer: 4000,
                timerProgressBar: true,
                didOpen: (popup) => {
                    popup.addEventListener('mouseenter', Swal.stopTimer)
                    popup.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });

            AlertModal.fire({
                icon: '{{ $swalType }}',
                title: '{!! addslashes($title) !!}'
            });
        }
    });
</script>

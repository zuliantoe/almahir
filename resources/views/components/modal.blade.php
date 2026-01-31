{{--
    Modal Component - AdminLTE 3 / Bootstrap 4
    
    Props: id, title, size, centered, static, scrollable
    Slots: default (body), footer
--}}

@props([
    'id',
    'title' => 'Modal',
    'size' => null,
    'centered' => false,
    'static' => false,
    'scrollable' => false,
])

@php
    $dialogClass = 'modal-dialog';
    
    if ($size) {
        $dialogClass .= ' modal-' . $size;
    }
    
    if ($centered) {
        $dialogClass .= ' modal-dialog-centered';
    }
    
    if ($scrollable) {
        $dialogClass .= ' modal-dialog-scrollable';
    }
@endphp

<div 
    class="modal fade" 
    id="{{ $id }}" 
    tabindex="-1" 
    role="dialog" 
    aria-labelledby="{{ $id }}Label" 
    aria-hidden="true"
    @if($static) 
        data-backdrop="static" 
        data-keyboard="false" 
    @endif
    {{ $attributes }}
>
    <div class="{{ $dialogClass }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                {{ $slot }}
            </div>

            @if(isset($footer))
                <div class="modal-footer">
                    {{ $footer }}
                </div>
            @endif
        </div>
    </div>
</div>

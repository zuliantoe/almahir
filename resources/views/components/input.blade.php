{{--
    Input Component - AdminLTE 3
    
    Props: label, name, type, value, placeholder, required, disabled, readonly, hint, prepend, append
--}}

@props([
    'label' => null,
    'name',
    'type' => 'text',
    'value' => null,
    'placeholder' => null,
    'required' => false,
    'disabled' => false,
    'readonly' => false,
    'hint' => null,
    'prepend' => null,
    'append' => null,
])

@php
    $hasError = $errors->has($name);
    $inputClass = 'form-control';
    if ($hasError) {
        $inputClass .= ' is-invalid';
    }
    $inputValue = old($name, $value);
    $inputId = 'input-' . str_replace(['[', ']', '.'], ['-', '', '-'], $name);
@endphp

<div class="form-group">
    @if($label)
        <label for="{{ $inputId }}">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif

    @if($prepend || $append)
        <div class="input-group">
            @if($prepend)
                <div class="input-group-prepend">
                    <span class="input-group-text">{!! $prepend !!}</span>
                </div>
            @endif
            
            <input 
                type="{{ $type }}"
                id="{{ $inputId }}"
                name="{{ $name }}"
                value="{{ $inputValue }}"
                placeholder="{{ $placeholder }}"
                {{ $attributes->merge(['class' => $inputClass]) }}
                @if($required) required @endif
                @if($disabled) disabled @endif
                @if($readonly) readonly @endif
            />
            
            @if($append)
                <div class="input-group-append">
                    <span class="input-group-text">{!! $append !!}</span>
                </div>
            @endif
            
            @error($name)
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    @else
        <input 
            type="{{ $type }}"
            id="{{ $inputId }}"
            name="{{ $name }}"
            value="{{ $inputValue }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge(['class' => $inputClass]) }}
            @if($required) required @endif
            @if($disabled) disabled @endif
            @if($readonly) readonly @endif
        />
        
        @error($name)
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    @endif

    @if($hint)
        <small class="form-text text-muted">{{ $hint }}</small>
    @endif
</div>

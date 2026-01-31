{{--
    Card Component - AdminLTE 3 styling
    
    Props: title, type (primary/success/danger/warning/info), outline, collapsed, removable, maximizable, collapsible
    Slots: tools, footer, default
--}}

@props([
    'title' => null,
    'type' => 'primary',
    'outline' => false,
    'collapsed' => false,
    'removable' => false,
    'maximizable' => false,
    'collapsible' => false,
])

@php
    $cardClass = $outline 
        ? "card card-outline card-{$type}" 
        : "card card-{$type}";
    
    if ($collapsed) {
        $cardClass .= ' collapsed-card';
    }
@endphp

<div {{ $attributes->merge(['class' => $cardClass]) }}>
    @if($title || isset($tools) || $collapsible || $maximizable || $removable)
        <div class="card-header">
            @if($title)
                <h3 class="card-title">{{ $title }}</h3>
            @endif
            
            <div class="card-tools">
                @if(isset($tools))
                    {{ $tools }}
                @endif
                
                @if($collapsible)
                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                        <i class="fas {{ $collapsed ? 'fa-plus' : 'fa-minus' }}"></i>
                    </button>
                @endif
                
                @if($maximizable)
                    <button type="button" class="btn btn-tool" data-card-widget="maximize">
                        <i class="fas fa-expand"></i>
                    </button>
                @endif
                
                @if($removable)
                    <button type="button" class="btn btn-tool" data-card-widget="remove">
                        <i class="fas fa-times"></i>
                    </button>
                @endif
            </div>
        </div>
    @endif

    <div class="card-body" @if($collapsed) style="display: none;" @endif>
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="card-footer">
            {{ $footer }}
        </div>
    @endif
</div>

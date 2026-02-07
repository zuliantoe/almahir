@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="container-fluid">
    <x-card title="Detail ManajemenAsetDanAsrama" icon="fas fa-edit">
        {{-- TODO: Add form content --}}
        <p class="text-muted">Form content here...</p>
        
        <div class="mt-4">
            <a href="{{ route('manajemenasetdanasrama.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
            </a>
        </div>
    </x-card>
</div>
@endsection

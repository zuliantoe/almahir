@extends('layouts.app')

@section('title', 'Tambah Master Jam Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('akademik::master-jam-pelajaran._form')
        </div>
    </div>
</div>
@endsection


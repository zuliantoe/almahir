@extends('layouts.app')

@section('title', 'Edit Master Jam Pelajaran')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('akademik::master-jam-pelajaran._form', ['masterJamPelajaran' => $masterJamPelajaran])
        </div>
    </div>
</div>
@endsection


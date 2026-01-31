@extends('layouts.app')

@section('title', 'UI Style Guide')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><i class="fas fa-palette mr-2"></i>UI Style Guide</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                <li class="breadcrumb-item active">UI Guide</li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    {{-- Introduction --}}
    <div class="alert alert-primary">
        <h4><i class="fas fa-info-circle mr-2"></i>Panduan Penggunaan Komponen UI</h4>
        <p class="mb-0">
            Tim <strong>WAJIB menggunakan komponen</strong> standar dan <strong>DILARANG menulis HTML mentah</strong> untuk elemen-elemen dasar.
        </p>
    </div>

    {{-- BUTTON COMPONENT --}}
    <div class="card card-dark">
        <div class="card-header">
            <h3 class="card-title">1. Button Component</h3>
            <div class="card-tools"><span class="badge badge-secondary">btn.blade.php</span></div>
        </div>
        <div class="card-body">
            <p><strong>File:</strong> <code>resources/views/components/btn.blade.php</code></p>
            
            <table class="table table-sm table-bordered mb-4">
                <thead class="thead-light">
                    <tr><th>Prop</th><th>Default</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>type</code></td><td>button</td><td>submit, button, reset</td></tr>
                    <tr><td><code>class</code></td><td>btn-primary</td><td>Button color class</td></tr>
                    <tr><td><code>icon</code></td><td>null</td><td>FontAwesome icon class</td></tr>
                    <tr><td><code>href</code></td><td>null</td><td>Renders as &lt;a&gt; tag</td></tr>
                    <tr><td><code>loading</code></td><td>false</td><td>Show loading spinner</td></tr>
                </tbody>
            </table>

            <h5>Live Demo:</h5>
            <div class="row">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded mb-2">
                        <x-btn class="btn-primary">Primary</x-btn>
                        <x-btn class="btn-success">Success</x-btn>
                        <x-btn class="btn-danger">Danger</x-btn>
                        <x-btn class="btn-warning">Warning</x-btn>
                    </div>
                    <div class="p-3 bg-light rounded mb-2">
                        <x-btn class="btn-primary" icon="fas fa-save">Simpan</x-btn>
                        <x-btn class="btn-success" icon="fas fa-plus">Tambah</x-btn>
                        <x-btn class="btn-danger" icon="fas fa-trash">Hapus</x-btn>
                    </div>
                </div>
                <div class="col-md-6">
<pre><code>&lt;x-btn class="btn-primary"&gt;Primary&lt;/x-btn&gt;
&lt;x-btn class="btn-success"&gt;Success&lt;/x-btn&gt;

&lt;!-- With icon --&gt;
&lt;x-btn class="btn-primary" icon="fas fa-save"&gt;Simpan&lt;/x-btn&gt;

&lt;!-- Submit type --&gt;
&lt;x-btn type="submit" class="btn-success"&gt;Submit&lt;/x-btn&gt;

&lt;!-- As link --&gt;
&lt;x-btn class="btn-info" href="/dashboard"&gt;Go&lt;/x-btn&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD COMPONENT --}}
    <div class="card card-dark">
        <div class="card-header">
            <h3 class="card-title">2. Card Component</h3>
            <div class="card-tools"><span class="badge badge-secondary">card.blade.php</span></div>
        </div>
        <div class="card-body">
            <p><strong>File:</strong> <code>resources/views/components/card.blade.php</code></p>
            
            <table class="table table-sm table-bordered mb-4">
                <thead class="thead-light">
                    <tr><th>Prop</th><th>Default</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>title</code></td><td>null</td><td>Card header title</td></tr>
                    <tr><td><code>type</code></td><td>primary</td><td>Color theme</td></tr>
                    <tr><td><code>outline</code></td><td>false</td><td>Use outline style</td></tr>
                </tbody>
            </table>

            <h5>Slots:</h5>
            <ul>
                <li><code>tools</code> - Action buttons in header</li>
                <li><code>footer</code> - Card footer content</li>
            </ul>

            <h5>Live Demo:</h5>
            <div class="row">
                <div class="col-md-4">
                    <x-card title="Primary" type="primary">Content</x-card>
                </div>
                <div class="col-md-4">
                    <x-card title="Success" type="success">Content</x-card>
                </div>
                <div class="col-md-4">
                    <x-card title="Outline" type="info" :outline="true">Content</x-card>
                </div>
            </div>

            <h5 class="mt-3">Code:</h5>
<pre><code>&lt;x-card title="Title" type="primary"&gt;
    Content
&lt;/x-card&gt;

&lt;x-card title="With Footer" type="success"&gt;
    Content
    &lt;x-slot name="footer"&gt;Footer&lt;/x-slot&gt;
&lt;/x-card&gt;</code></pre>
        </div>
    </div>

    {{-- INPUT COMPONENT --}}
    <div class="card card-dark">
        <div class="card-header">
            <h3 class="card-title">3. Input Component</h3>
            <div class="card-tools"><span class="badge badge-secondary">input.blade.php</span></div>
        </div>
        <div class="card-body">
            <p><strong>File:</strong> <code>resources/views/components/input.blade.php</code></p>
            
            <table class="table table-sm table-bordered mb-4">
                <thead class="thead-light">
                    <tr><th>Prop</th><th>Default</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>label</code></td><td>null</td><td>Input label</td></tr>
                    <tr><td><code>name</code></td><td>required</td><td>Input name</td></tr>
                    <tr><td><code>type</code></td><td>text</td><td>Input type</td></tr>
                    <tr><td><code>prepend/append</code></td><td>null</td><td>Input group addons</td></tr>
                </tbody>
            </table>

            <div class="alert alert-info">
                <i class="fas fa-magic mr-2"></i>Auto: validation errors + old() repopulation
            </div>

            <h5>Live Demo:</h5>
            <div class="row">
                <div class="col-md-6">
                    <x-input label="Username" name="demo_user" placeholder="Enter username" />
                    <x-input label="Price" name="demo_price" prepend="Rp" append=".00" />
                </div>
                <div class="col-md-6">
<pre><code>&lt;x-input label="Username" name="username" required /&gt;

&lt;x-input label="Price" name="price" 
    prepend="Rp" append=".00" /&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL COMPONENT --}}
    <div class="card card-dark">
        <div class="card-header">
            <h3 class="card-title">4. Modal Component</h3>
            <div class="card-tools"><span class="badge badge-secondary">modal.blade.php</span></div>
        </div>
        <div class="card-body">
            <p><strong>File:</strong> <code>resources/views/components/modal.blade.php</code></p>
            
            <table class="table table-sm table-bordered mb-4">
                <thead class="thead-light">
                    <tr><th>Prop</th><th>Default</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>id</code></td><td>required</td><td>Modal DOM id</td></tr>
                    <tr><td><code>title</code></td><td>Modal</td><td>Header title</td></tr>
                    <tr><td><code>size</code></td><td>null</td><td>sm, lg, xl</td></tr>
                    <tr><td><code>centered</code></td><td>false</td><td>Vertically center</td></tr>
                </tbody>
            </table>

            <h5>Live Demo:</h5>
            <div class="p-3 bg-light rounded mb-3">
                <x-btn class="btn-primary" data-toggle="modal" data-target="#demoModal">Open Modal</x-btn>
                <x-btn class="btn-danger" data-toggle="modal" data-target="#confirmModal">Confirm Modal</x-btn>
            </div>

<pre><code>&lt;x-modal id="myModal" title="Title"&gt;
    Body content
    &lt;x-slot name="footer"&gt;
        &lt;x-btn data-dismiss="modal"&gt;Close&lt;/x-btn&gt;
        &lt;x-btn class="btn-primary"&gt;Save&lt;/x-btn&gt;
    &lt;/x-slot&gt;
&lt;/x-modal&gt;

&lt;x-btn data-toggle="modal" data-target="#myModal"&gt;Open&lt;/x-btn&gt;</code></pre>
        </div>
    </div>

    {{-- QUICK REFERENCE --}}
    <div class="card card-success card-outline">
        <div class="card-header">
            <h3 class="card-title">Quick Reference</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6>Common Buttons:</h6>
<pre><code>&lt;x-btn type="submit" class="btn-primary" icon="fas fa-save"&gt;Simpan&lt;/x-btn&gt;
&lt;x-btn class="btn-success" icon="fas fa-plus" href="..."&gt;Tambah&lt;/x-btn&gt;
&lt;x-btn class="btn-warning btn-sm" icon="fas fa-edit"&gt;Edit&lt;/x-btn&gt;
&lt;x-btn class="btn-danger btn-sm" icon="fas fa-trash"&gt;Hapus&lt;/x-btn&gt;</code></pre>
                </div>
                <div class="col-md-6">
                    <h6>Form Template:</h6>
<pre><code>&lt;form action="..." method="POST"&gt;
    @@csrf
    &lt;x-card title="Form" type="primary"&gt;
        &lt;x-input label="Nama" name="nama" required /&gt;
        &lt;x-slot name="footer"&gt;
            &lt;x-btn type="submit"&gt;Simpan&lt;/x-btn&gt;
        &lt;/x-slot&gt;
    &lt;/x-card&gt;
&lt;/form&gt;</code></pre>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL DEFINITIONS --}}
    <x-modal id="demoModal" title="Default Modal">
        <p>This is a default modal example.</p>
        <x-slot name="footer">
            <x-btn class="btn-secondary" data-dismiss="modal">Close</x-btn>
            <x-btn class="btn-primary">Save</x-btn>
        </x-slot>
    </x-modal>

    <x-modal id="confirmModal" title="Konfirmasi" :centered="true">
        <div class="text-center">
            <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
            <h5>Apakah Anda yakin?</h5>
        </div>
        <x-slot name="footer">
            <x-btn class="btn-secondary" data-dismiss="modal">Batal</x-btn>
            <x-btn class="btn-danger" icon="fas fa-trash">Hapus</x-btn>
        </x-slot>
    </x-modal>
@endsection

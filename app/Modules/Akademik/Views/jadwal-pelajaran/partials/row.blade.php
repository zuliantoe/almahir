@php
    $isEdit = isset($data);
    $prefix = $isEdit ? "" : "schedules[$index]";
    $nameRombel = $isEdit ? "rombel_id" : "schedules[$index][rombel_id]";
    $nameMapel = $isEdit ? "mapel_id" : "schedules[$index][mapel_id]";
    $nameGuru = $isEdit ? "guru_id" : "schedules[$index][guru_id]";
    $nameHari = $isEdit ? "hari" : "schedules[$index][hari]";
    $nameJamKe = $isEdit ? "jamke" : "schedules[$index][jamke]";
    $nameJamAwal = $isEdit ? "jamawal" : "schedules[$index][jamawal]";
    $nameJamAkhir = $isEdit ? "jamakhir" : "schedules[$index][jamakhir]";

    // Prevent Select2 from initializing on template row by checking index
    $isTemplate = ($index === 'REPLACE_INDEX');
    $select2Class = $isTemplate ? 'select2-dynamic' : 'select2';
@endphp

<tr>
    <td class="px-3">
        <select name="{{ $nameRombel }}" class="form-control form-control-sm form-control-premium {{ $select2Class }}" required>
            <option value="">Rombel</option>
            @foreach($rombels as $rombel)
                <option value="{{ $rombel->id }}" {{ (isset($data) && $data->rombel_id == $rombel->id) ? 'selected' : '' }}>
                    {{ $rombel->nama_rombel }}
                </option>
            @endforeach
        </select>
    </td>
    <td class="px-3">
        <select name="{{ $nameMapel }}" class="form-control form-control-sm form-control-premium {{ $select2Class }}" required>
            <option value="">Mapel</option>
            @foreach($mapels as $mapel)
                <option value="{{ $mapel->id }}" {{ (isset($data) && $data->mapel_id == $mapel->id) ? 'selected' : '' }}>
                    {{ $mapel->nama }}
                </option>
            @endforeach
        </select>
    </td>
    <td class="px-3">
        <select name="{{ $nameGuru }}" class="form-control form-control-sm form-control-premium {{ $select2Class }}" required>
            <option value="">Guru</option>
            @foreach($gurus as $guru)
                <option value="{{ $guru->id }}" {{ (isset($data) && $data->guru_id == $guru->id) ? 'selected' : '' }}>
                    {{ $guru->nama }}
                </option>
            @endforeach
        </select>
    </td>
    <td class="px-3">
        <select name="{{ $nameHari }}" class="form-control form-control-sm form-control-premium" required>
            <option value="">Hari</option>
            @foreach($hariList as $hari)
                {{-- $hari sudah berupa string nama hari (Senin, Selasa, ...) --}}
                <option value="{{ $hari }}" {{ (isset($data) && $data->hari == $hari) ? 'selected' : '' }}>{{ $hari }}</option>
            @endforeach
        </select>
    </td>
    <td class="px-3">
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text bg-primary-soft border-0 rounded-left-pill text-primary font-weight-bold">#</span>
            </div>
            <input type="number" name="{{ $nameJamKe }}" class="form-control form-control-sm form-control-premium border-left-0 rounded-right-pill text-center" 
                   placeholder="1" value="{{ $data->jamke ?? '' }}" required style="max-width: 80px;">
        </div>
    </td>
    <td class="px-3">
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text bg-light border-right-0"><i class="far fa-clock text-primary"></i></span>
            </div>
            <input type="time" name="{{ $nameJamAwal }}" class="form-control form-control-sm form-control-premium border-left-0 rounded-right-pill" 
                   value="{{ isset($data) ? substr($data->jamawal, 0, 5) : '' }}" required>
        </div>
    </td>
    <td class="px-3">
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <span class="input-group-text bg-light border-right-0"><i class="fas fa-history text-danger"></i></span>
            </div>
            <input type="time" name="{{ $nameJamAkhir }}" class="form-control form-control-sm form-control-premium border-left-0 rounded-right-pill" 
                   value="{{ isset($data) ? substr($data->jamakhir, 0, 5) : '' }}" required>
        </div>
    </td>
    @if(!$isEdit)
    <td class="text-center px-3">
        <button type="button" class="btn btn-light btn-sm text-danger rounded-circle shadow-sm transition-all hover-scale border" onclick="removeRow(this)" title="Hapus Baris">
            <i class="fas fa-times"></i>
        </button>
    </td>
    @endif
</tr>

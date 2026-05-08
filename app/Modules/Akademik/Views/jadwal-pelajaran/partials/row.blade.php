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
@endphp

<tr>
    <td>
        <select name="{{ $nameRombel }}" class="form-control form-control-sm select2" required>
            <option value="">Rombel</option>
            @foreach($rombels as $rombel)
                <option value="{{ $rombel->id }}" {{ (isset($data) && $data->rombel_id == $rombel->id) ? 'selected' : '' }}>
                    {{ $rombel->nama_rombel }}
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="{{ $nameMapel }}" class="form-control form-control-sm select2" required>
            <option value="">Mapel</option>
            @foreach($mapels as $mapel)
                <option value="{{ $mapel->id }}" {{ (isset($data) && $data->mapel_id == $mapel->id) ? 'selected' : '' }}>
                    {{ $mapel->nama }}
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="{{ $nameGuru }}" class="form-control form-control-sm select2" required>
            <option value="">Guru</option>
            @foreach($gurus as $guru)
                <option value="{{ $guru->id }}" {{ (isset($data) && $data->guru_id == $guru->id) ? 'selected' : '' }}>
                    {{ $guru->nama }}
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <select name="{{ $nameHari }}" class="form-control form-control-sm" required>
            <option value="">Hari</option>
            @foreach($hariList as $hari)
                <option value="{{ $hari }}" {{ (isset($data) && $data->hari == $hari) ? 'selected' : '' }}>{{ $hari }}</option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" name="{{ $nameJamKe }}" class="form-control form-control-sm" placeholder="1" value="{{ $data->jamke ?? '' }}" required>
    </td>
    <td>
        <input type="time" name="{{ $nameJamAwal }}" class="form-control form-control-sm" value="{{ isset($data) ? substr($data->jamawal, 0, 5) : '' }}" required>
    </td>
    <td>
        <input type="time" name="{{ $nameJamAkhir }}" class="form-control form-control-sm" value="{{ isset($data) ? substr($data->jamakhir, 0, 5) : '' }}" required>
    </td>
    @if(!$isEdit)
    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
            <i class="fas fa-trash"></i>
        </button>
    </td>
    @endif
</tr>

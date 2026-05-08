@php
    $isEdit = isset($data);
    $nameMapel = $isEdit ? "mapel_id" : "details[$index][mapel_id]";
    $nameJam = $isEdit ? "totaljam" : "details[$index][total_jam_minggu]";
    $nameKKM = $isEdit ? "kkm" : "details[$index][kkm]";
@endphp

<tr>
    <td>
        <select name="{{ $nameMapel }}" class="form-control form-control-sm select2" required>
            <option value="">Pilih Mata Pelajaran</option>
            @foreach($mapels as $mapel)
                <option value="{{ $mapel->id }}" {{ (isset($data) && $data->mapel_id == $mapel->id) ? 'selected' : '' }}>
                    [{{ $mapel->kode }}] {{ $mapel->nama }}
                </option>
            @endforeach
        </select>
    </td>
    <td>
        <input type="number" name="{{ $nameJam }}" class="form-control form-control-sm" placeholder="Jam/Minggu" value="{{ $data->totaljam ?? '' }}" required>
    </td>
    <td>
        <input type="number" name="{{ $nameKKM }}" class="form-control form-control-sm" placeholder="KKM" value="{{ $data->kkm ?? '' }}" required>
    </td>
    @if(!$isEdit)
    <td class="text-center">
        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
            <i class="fas fa-trash"></i>
        </button>
    </td>
    @endif
</tr>

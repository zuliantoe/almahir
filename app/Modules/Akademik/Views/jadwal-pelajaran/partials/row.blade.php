@php
    $isEdit = isset($data);

    // schedule field name mapping
    $nameRombel = $isEdit ? "rombel_id" : "schedules[$index][rombel_id]";
    $nameMapel = $isEdit ? "mapel_id" : "schedules[$index][mapel_id]";
    $nameGuru  = $isEdit ? "guru_id" : "schedules[$index][guru_id]";
    $nameHari  = $isEdit ? "hari" : "schedules[$index][hari]";

    // new master jam selection (source of jamawal/jamakhir)
    $nameMasterJam = $isEdit ? "master_jam_pelajaran_id" : "schedules[$index][master_jam_pelajaran_id]";

    // still submit jamke/jamawal/jamakhir but readonly/hidden
    $nameJamKe = $isEdit ? "jamke" : "schedules[$index][jamke]";
    $nameJamAwal = $isEdit ? "jamawal" : "schedules[$index][jamawal]";
    $nameJamAkhir = $isEdit ? "jamakhir" : "schedules[$index][jamakhir]";

    // Prevent Select2 from initializing on template row by checking index
    $isTemplate = ($index === 'REPLACE_INDEX');
    $select2Class = $isTemplate ? 'select2-dynamic' : 'select2';

    // current selected master jam id if data already has jamke
    $selectedMasterJamId = null;
    if (isset($data) && isset($data->jamke) && isset($masterJams)) {
        $selectedMasterJamId = $masterJams->firstWhere('jamke', $data->jamke)?->id;
    }
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
        <select name="{{ $nameHari }}" class="form-control form-control-sm form-control-premium hari-select" required>
            <option value="">Hari</option>
            @foreach($hariList as $hari)
                <option value="{{ $hari }}" {{ (isset($data) && $data->hari == $hari) ? 'selected' : '' }}>{{ $hari }}</option>
            @endforeach
        </select>
    </td>

    <td class="px-3">
        <select
            name="{{ $nameMasterJam }}"
            class="form-control form-control-sm form-control-premium master-jam-select"
            required
            data-jadwal-master-jam-target="{{ $index }}"
            data-jamawal-target="{{ $nameJamAwal }}"
            data-jamakhir-target="{{ $nameJamAkhir }}"
            data-jamke-target="{{ $nameJamKe }}"
        >
            <option value="">Pilih Master Jam</option>
            @foreach($masterJams as $mj)
                <option value="{{ $mj->id }}"
                    {{ ($selectedMasterJamId && $mj->id == $selectedMasterJamId) ? 'selected' : '' }}
                    data-jamawal="{{ $mj->jamawal }}"
                    data-jamakhir="{{ $mj->jamakhir }}"
                    data-jamke="{{ $mj->jamke }}"
                    data-hari="{{ $mj->hari }}"
                    data-istirahat="{{ $mj->is_istirahat ? 1 : 0 }}"
                >
                    {{ $mj->jamke }} ({{ \Carbon\Carbon::parse($mj->jamawal)->format('H:i') }}-{{ \Carbon\Carbon::parse($mj->jamakhir)->format('H:i') }}) {{ $mj->is_istirahat ? '[ISTIRAHAT]' : '' }}
                </option>
            @endforeach
        </select>
        <input type="hidden" name="{{ $nameJamKe }}" class="jamke-input" value="{{ $data->jamke ?? '' }}">
        <input type="hidden" name="{{ $nameJamAwal }}" class="jamawal-input" value="{{ isset($data) ? substr($data->jamawal, 0, 5) : '' }}">
        <input type="hidden" name="{{ $nameJamAkhir }}" class="jamakhir-input" value="{{ isset($data) ? substr($data->jamakhir, 0, 5) : '' }}">
    </td>

    @if(!$isEdit)
        <td class="text-center px-3">
            <button type="button" class="btn btn-light btn-sm text-danger rounded-circle shadow-sm transition-all hover-scale border" onclick="removeRow(this)" title="Hapus Baris">
                <i class="fas fa-times"></i>
            </button>
        </td>
    @endif
</tr>

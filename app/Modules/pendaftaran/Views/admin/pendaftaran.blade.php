<style>
    body {
        font-family: Arial;
        background: #f4f6f9;
    }

    .container {
        max-width: 1000px;
        margin: 40px auto;
        background: white;
        padding: 30px;
        border-radius: 12px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    th,
    td {
        padding: 12px;
        border-bottom: 1px solid #eee;
        text-align: left;
    }

    th {
        background: #f9fafb;
    }

    .btn {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
    }

    .btn-view {
        background: #3b82f6;
        color: white;
    }

    .btn-jadwal {
        background: #16a34a;
        color: white;
    }
</style>


<div class="container">

    <h2>Dashboard Admin</h2>

    <table>

        <thead>
            <tr>
                <th>Nama</th>
                <th>Tempat Lahir</th>
                <th>No HP</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($data as $item)
                <tr>

                    <td>{{ $item->nama_lengkap }}</td>

                    <td>{{ $item->tempat_lahir }}</td>

                    <td>{{ $item->no_hp }}</td>

                    <td>

                        <a href="/pendaftaran/admin/pendaftaran/{{ $item->id }}" class="btn btn-view">
                            lihat
                        </a>

                        <a href="#" class="btn btn-jadwal">
                            Set Jadwal
                        </a>

                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

</div>

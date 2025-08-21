<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Data Siswa</title>
    <style>
        body { 
            font-family: 'Helvetica', sans-serif; 
            font-size: 12px;
        }
        .header { 
            text-align: center; 
            margin-bottom: 20px; 
        }
        .header h1 { 
            margin: 0; 
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            font-size: 14px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            border: 1px solid #000; 
            padding: 8px; 
            text-align: left; 
        }
        th { 
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Siswa</h1>
        <p>
            Filter Kelas: {{ $filterInfo['kelas'] }} | 
            Pencarian: {{ $filterInfo['search'] == 'Semua Siswa' ? 'Tidak ada' : "'".$filterInfo['search']."'" }}
        </p>
        <p>Tanggal Cetak: {{ date('d-m-Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th class="text-center">No</th>
                <th>NIS</th>
                <th>Nama Lengkap</th>
                <th>Kelas</th>
                <th>No. HP Wali</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($siswas as $siswa)
                <tr>
                    <td class="text-center">{{ $loop->iteration }}</td>
                    <td>{{ $siswa->nis }}</td>
                    <td>{{ $siswa->nama_lengkap }}</td>
                    <td>{{ $siswa->kelas }}</td>
                    <td>{{ $siswa->no_hp_wali }}</td>
                    <td>{{ $siswa->alamat }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data siswa yang cocok dengan filter yang diterapkan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
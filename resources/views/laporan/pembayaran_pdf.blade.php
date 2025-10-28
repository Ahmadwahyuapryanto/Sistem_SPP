<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pembayaran SPP</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h1 { margin: 0; }
        .header p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; font-size: 12px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        tfoot { font-weight: bold; }
        tfoot td { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Pembayaran SPP</h1>
        <p>Periode: {{ \Carbon\Carbon::parse($tanggalMulai)->isoFormat('D MMMM Y') }} - {{ \Carbon\Carbon::parse($tanggalSelesai)->isoFormat('D MMMM Y') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal Bayar</th>
                <th>Nama Siswa</th>
                <th>Periode Tagihan</th>
                <th>Jumlah</th>
                <th>Metode Pembayaran</th> {{-- KOLOM BARU --}}
                <th>Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($pembayarans as $pembayaran)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->isoFormat('D MMM Y') }}</td>
                    <td>{{ $pembayaran->tagihan->siswa->nama_lengkap }}</td>
                    <td>{{ \Carbon\Carbon::createFromDate($pembayaran->tagihan->tahun, $pembayaran->tagihan->bulan, 1)->translatedFormat('F Y') }}</td>
                    <td>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                    <td>{{ $pembayaran->metode_pembayaran }}</td> {{-- DATA BARU --}}
                    <td>{{ $pembayaran->user->name ?? 'Online' }}</td> {{-- PENYESUAIAN DISINI --}}
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data untuk periode yang dipilih.</td> {{-- COLSPAN DISESUAIKAN --}}
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5" style="text-align: right;">Total Pendapatan</td> {{-- COLSPAN DISESUAIKAN --}}
                <td colspan="2">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
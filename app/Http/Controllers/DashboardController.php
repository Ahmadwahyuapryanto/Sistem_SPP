<?php

namespace App\Http\Controllers;

use App\Models\BiayaSpp;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
   public function index(Request $request)
{
    // ---- DATA UNTUK FORM GENERATE TAGIHAN ----
    $biayaSpps = BiayaSpp::all();

    // ---- DATA UNTUK STATISTIK ----
    $selectedBulan = $request->input('bulan', date('m'));
    $selectedTahun = $request->input('tahun', date('Y'));
    $totalSiswa = Siswa::count();

    // Query dasar untuk tagihan
    $queryTagihan = Tagihan::where('tahun', $selectedTahun);

    // Jika bulan spesifik dipilih (bukan "semua bulan")
    if ($selectedBulan != 'all') {
        $queryTagihan->where('bulan', $selectedBulan);
    }

    // Clone query agar bisa digunakan untuk beberapa perhitungan
    $queryLunas = clone $queryTagihan;
    $queryBelumLunas = clone $queryTagihan;

    $jumlahSudahLunas = $queryLunas->where('status', 'LUNAS')->count();
    $jumlahBelumLunas = $queryBelumLunas->where('status', 'BELUM LUNAS')->count();

    // ---- DATA UNTUK GRAFIK (Ini tidak berubah, tetap menampilkan data 12 bulan) ----
    $dataPembayaran = Tagihan::select(
            DB::raw('bulan'),
            DB::raw("SUM(CASE WHEN status = 'LUNAS' THEN 1 ELSE 0 END) as lunas"),
            DB::raw("SUM(CASE WHEN status = 'BELUM LUNAS' THEN 1 ELSE 0 END) as belum_lunas")
        )
        ->where('tahun', $selectedTahun)
        ->groupBy('bulan')
        ->orderBy('bulan', 'asc')
        ->get();

    $labels = [];
    $dataLunas = [];
    $dataBelumLunas = [];
    $namaBulan = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    for ($i = 0; $i < 12; $i++) {
        $bulan = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
        $labels[] = $namaBulan[$i];
        $dataLunas[$bulan] = 0;
        $dataBelumLunas[$bulan] = 0;
    }

    foreach ($dataPembayaran as $data) {
        $dataLunas[$data->bulan] = $data->lunas;
        $dataBelumLunas[$data->bulan] = $data->belum_lunas;
    }

    return view('dashboard', [
        'biayaSpps' => $biayaSpps,
        'totalSiswa' => $totalSiswa,
        'jumlahSudahLunas' => $jumlahSudahLunas,
        'jumlahBelumLunas' => $jumlahBelumLunas,
        'selectedBulan' => $selectedBulan,
        'selectedTahun' => $selectedTahun,
        'chartLabels' => $labels,
        'chartDataLunas' => array_values($dataLunas),
        'chartDataBelumLunas' => array_values($dataBelumLunas),
    ]);
    }
}
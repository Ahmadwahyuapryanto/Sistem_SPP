<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf; // Import PDF Facade

class LaporanController extends Controller
{
    // Fungsi untuk mengambil data pembayaran berdasarkan rentang tanggal
    private function getDataPembayaran($tanggalMulai, $tanggalSelesai)
    {
        return Pembayaran::with(['tagihan.siswa', 'user'])
            ->whereBetween('tanggal_bayar', [$tanggalMulai, $tanggalSelesai])
            ->orderBy('tanggal_bayar', 'asc')
            ->get();
    }

    // Menampilkan halaman laporan pembayaran di web
    public function pembayaran(Request $request)
    {
        // Set tanggal default ke bulan ini jika tidak ada input
        $tanggalMulai = $request->input('tanggal_mulai', date('Y-m-01'));
        $tanggalSelesai = $request->input('tanggal_selesai', date('Y-m-t'));

        $pembayarans = $this->getDataPembayaran($tanggalMulai, $tanggalSelesai);
        $totalPendapatan = $pembayarans->sum('jumlah_bayar');
        
        return view('laporan.pembayaran', compact('pembayarans', 'totalPendapatan', 'tanggalMulai', 'tanggalSelesai'));
    }

    // Membuat dan mengunduh laporan dalam format PDF
    public function cetakPdf(Request $request)
    {
        $tanggalMulai = $request->input('tanggal_mulai', date('Y-m-01'));
        $tanggalSelesai = $request->input('tanggal_selesai', date('Y-m-t'));

        $pembayarans = $this->getDataPembayaran($tanggalMulai, $tanggalSelesai);
        $totalPendapatan = $pembayarans->sum('jumlah_bayar');

        // Membuat view PDF dengan data yang sudah difilter
        $pdf = Pdf::loadView('laporan.pembayaran_pdf', compact('pembayarans', 'totalPendapatan', 'tanggalMulai', 'tanggalSelesai'));
        
        // Mengatur ukuran kertas dan orientasi
        $pdf->setPaper('a4', 'landscape');
        
        // Mengunduh file PDF
        return $pdf->download('laporan-pembayaran-'.$tanggalMulai.'-'.$tanggalSelesai.'.pdf');
    }
}
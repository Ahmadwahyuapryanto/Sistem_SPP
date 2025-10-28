<?php

namespace App\Http\Controllers\Wali;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class PembayaranController extends Controller
{
    public function create(Request $request, Tagihan $tagihan)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = config('services.midtrans.is_sanitized');
        Config::$is3ds = config('services.midtrans.is_3ds');

        // Detail Transaksi
        $transaction_details = [
            'order_id' => 'SPP-' . $tagihan->id . '-' . time(), // Order ID unik
            'gross_amount' => $tagihan->jumlah,
        ];

        // Detail Wali Murid (Customer)
        $customer_details = [
            'first_name' => $tagihan->siswa->wali->nama,
            'email' => $tagihan->siswa->wali->email,
            // 'phone' => '08123456789', // Opsional
        ];

        // Detail Item
        $bulan = \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->translatedFormat('F');
        $item_details[] = [
            'id' => $tagihan->id,
            'price' => $tagihan->jumlah,
            'quantity' => 1,
            'name' => 'Pembayaran SPP ' . $bulan . ' ' . $tagihan->tahun,
        ];
        
        // --- LOGIC BARU: Menentukan URL Pengembalian (Return URL) ---
        // 1. Ambil URL halaman sebelumnya. Ini akan menjadi tujuan pengembalian user.
        //    Gunakan 'url()->previous()' untuk mendapatkan URL dari mana user berasal.
        $returnUrl = url()->previous();

        // 2. Jika url()->previous() tidak ada (misalnya, jika user langsung mengakses URL ini), 
        //    kita bisa fallback ke rute default (misalnya, dashboard wali). 
        //    ANDA MUNGKIN PERLU MENGGANTI 'wali.dashboard' dengan rute yang sesuai di aplikasi Anda.
        if (empty($returnUrl)) {
            $returnUrl = route('wali.dashboard'); 
        }

        $params = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
            
            // 3. Tambahkan callbacks untuk Midtrans Redirect
            'callbacks' => [
                // finish: Dipakai jika pembayaran berhasil. User akan kembali ke halaman sebelumnya dengan status success.
                'finish' => $returnUrl . '?transaction_status=success', 
                // unfinish: Dipakai jika pembayaran dibatalkan atau tidak selesai.
                'unfinish' => $returnUrl . '?transaction_status=pending',
                // error: Dipakai jika terjadi error saat pembayaran.
                'error' => $returnUrl . '?transaction_status=failed',
            ],
        ];
        // --- AKHIR LOGIC BARU ---

        try {
            $snapToken = Snap::getSnapToken($params);
            
            // Redirect kembali ke halaman sebelumnya (untuk memunculkan Midtrans Snap modal/pop-up)
            return redirect()->back()->with('snap_token', $snapToken);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

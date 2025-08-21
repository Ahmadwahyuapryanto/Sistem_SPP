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

        $params = [
            'transaction_details' => $transaction_details,
            'customer_details' => $customer_details,
            'item_details' => $item_details,
        ];

        try {
            $snapToken = Snap::getSnapToken($params);
            // Kirim token ke view melalui session flash
            return redirect()->back()->with('snap_token', $snapToken);
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
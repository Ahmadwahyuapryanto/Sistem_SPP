<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Notification;

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            $notification = new Notification();
            
            $transaction = $notification->transaction_status;
            $type = $notification->payment_type;
            $orderId = $notification->order_id;
            $fraud = $notification->fraud_status;

            Log::info("Webhook Midtrans diterima: " . $orderId);

            // Pisahkan order_id untuk mendapatkan id_tagihan
            $parts = explode('-', $orderId);
            $tagihanId = $parts[1];

            $tagihan = Tagihan::findOrFail($tagihanId);

            if ($transaction == 'capture' || $transaction == 'settlement') {
                // Jika transaksi berhasil dan aman
                if ($fraud == 'accept') {
                    // Update status tagihan menjadi LUNAS
                    $tagihan->update(['status' => 'LUNAS']);
                    // Buat record pembayaran
                    Pembayaran::create([
                        'tagihan_id' => $tagihan->id,
                        'user_id' => null, // Pembayaran dilakukan oleh wali, bukan petugas
                        'tanggal_bayar' => now(),
                        'jumlah_bayar' => $tagihan->jumlah,
                    ]);
                }
            }
            
            return response()->json(['message' => 'Notification processed'], 200);

        } catch (\Exception $e) {
            Log::error("Error Webhook Midtrans: " . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
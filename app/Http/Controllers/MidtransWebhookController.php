<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // <-- Tambahkan
use Midtrans\Config;
use Midtrans\Notification;
use App\Notifications\PembayaranLunasNotification; // <-- Tambahkan

class MidtransWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');

        try {
            // Gunakan $request->all() untuk mendapatkan payload notifikasi
            $payload = $request->all();
            Log::info("Webhook Midtrans diterima: " . json_encode($payload));

            // Validasi payload
            if (empty($payload['order_id']) || empty($payload['transaction_status'])) {
                 Log::warning("Webhook Midtrans: Payload tidak lengkap.");
                 return response()->json(['message' => 'Invalid payload'], 400);
            }

            // Ambil data dari payload
            $transactionStatus = $payload['transaction_status'];
            $paymentType = $payload['payment_type'] ?? 'online';
            $orderId = $payload['order_id']; // Ini adalah ID Tagihan
            $fraudStatus = $payload['fraud_status'] ?? 'accept';

            $tagihan = Tagihan::with(['siswa.waliMurid'])->find($orderId); // Eager load relasi

            if (!$tagihan) {
                Log::error("Tagihan tidak ditemukan untuk Order ID: " . $orderId);
                return response()->json(['message' => 'Tagihan not found'], 404);
            }

            // Cek jika tagihan sudah lunas (untuk mencegah proses ganda)
            if ($tagihan->status === 'LUNAS') {
                Log::info("Webhook diterima untuk tagihan yang sudah lunas: " . $orderId);
                return response()->json(['message' => 'Tagihan already paid'], 200);
            }

            if ($transactionStatus == 'capture' || $transactionStatus == 'settlement') {
                // Jika transaksi berhasil dan aman
                if ($fraudStatus == 'accept') {
                    
                    // Update status tagihan menjadi LUNAS
                    $tagihan->update(['status' => 'LUNAS']);

                    // Buat record pembayaran
                    // Saya sesuaikan dengan kolom di migrasi kamu ('metode_pembayaran' dan 'total_tagihan')
                    $pembayaran = Pembayaran::create([
                        'tagihan_id' => $tagihan->id,
                        'user_id' => null, // Pembayaran dilakukan oleh wali, bukan petugas
                        'tanggal_bayar' => now(),
                        'jumlah_bayar' => $tagihan->total_tagihan, // Sesuai migrasi
                        'metode_pembayaran' => $paymentType,      // Sesuai migrasi
                    ]);

                    // ---------------------------------------------------
                    // KODE BARU DIMULAI DARI SINI
                    // ---------------------------------------------------

                    // Kirim Notifikasi WA ke Wali Murid
                    try {
                        // Ambil data wali murid dari relasi yang sudah di-load
                        $waliMurid = $tagihan->siswa->waliMurid;

                        // Pastikan wali murid ada dan punya nomor HP
                        if ($waliMurid && $waliMurid->no_hp) {
                            
                            // Panggil notifikasi
                            // Saya sesuaikan dengan nama method di file PembayaranLunasNotification.php
                            // yaitu method 'send'
                            $notifInstance = new PembayaranLunasNotification($pembayaran);
                            $notifInstance->send($waliMurid); // Panggil method 'send'

                            Log::info("Notifikasi WA (online) berhasil dikirim ke: " . $waliMurid->no_hp . " untuk tagihan ID: " . $tagihan->id);
                        } else {
                            Log::warning("Gagal kirim notifikasi WA (online): Wali Murid atau No HP tidak ditemukan untuk tagihan ID: " . $tagihan->id);
                        }
                    } catch (\Exception $e) {
                        // Catat error jika gagal kirim notifikasi
                        Log::error('Gagal kirim notifikasi WA (online) untuk tagihan ID: ' . $tagihan->id . '. Error: ' . $e->getMessage());
                    }

                    // ---------------------------------------------------
                    // KODE BARU BERAKHIR DI SINI
                    // ---------------------------------------------------

                }
            } else if ($transactionStatus == 'pending') {
                Log::info("Status transaksi pending untuk order ID: " . $orderId);
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'cancel' || $transactionStatus == 'expire') {
                Log::warning("Status transaksi gagal/dibatalkan/kedaluwarsa untuk order ID: " . $orderId . ". Status: " . $transactionStatus);
            } else {
                 Log::info("Status transaksi lain diterima untuk order ID: " . $orderId . ". Status: " . $transactionStatus);
            }

            return response()->json(['message' => 'Notification processed'], 200);

        } catch (\Exception $e) {
            Log::error("Error Webhook Midtrans: " . $e->getMessage());
            return response()->json(['message' => 'Error processing notification'], 500);
        }
    }
}
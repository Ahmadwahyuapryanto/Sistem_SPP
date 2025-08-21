<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PembayaranLunasNotification extends Notification
{
    use Queueable;

    protected $pembayaran;

    public function __construct($pembayaran)
    {
        $this->pembayaran = $pembayaran;
    }

    public function via($notifiable)
    {
        return []; // Kita kirim manual
    }

    public function send()
    {
        $tagihan = $this->pembayaran->tagihan;
        $siswa = $tagihan->siswa;

        $token = env('FONNTE_TOKEN');
        if (!$token) {
            Log::error('Fonnte token not set in .env file.');
            return;
        }

        $target = $siswa->no_hp_wali;
        if (substr($target, 0, 1) === '0') {
            $target = '62' . substr($target, 1);
        }

        $bulan = \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->translatedFormat('F');
        $nominal = 'Rp ' . number_format($this->pembayaran->jumlah_bayar, 0, ',', '.');
        $tanggalBayar = \Carbon\Carbon::parse($this->pembayaran->tanggal_bayar)->isoFormat('dddd, D MMMM Y');

        $message = "Yth. Wali Murid dari *{$siswa->nama_lengkap}*,\n\n";
        $message .= "Terima kasih. Pembayaran SPP untuk bulan *{$bulan} {$tagihan->tahun}* sebesar *{$nominal}* telah kami terima pada *{$tanggalBayar}*.\n\n";
        $message .= "Status Tagihan: *LUNAS*\n\n";
        $message .= "Hormat kami,\n*Admin Keuangan Sekolah*";

        try {
            Http::withHeaders(['Authorization' => $token])
                ->post('https://api.fonnte.com/send', [
                    'target' => $target,
                    'message' => $message,
                ]);
        } catch (\Exception $e) {
            Log::error('Fonnte WhatsApp Gagal: ' . $e->getMessage());
        }
    }
}
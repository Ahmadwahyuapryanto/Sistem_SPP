<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;
use App\Models\WaliMurid;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PengingatTagihanNotification extends Notification
{
    use Queueable;

    protected $waliMurid;
    protected $tagihanBelumLunas;

    /**
     * Create a new notification instance.
     */
    public function __construct(WaliMurid $waliMurid, Collection $tagihanBelumLunas)
    {
        $this->waliMurid = $waliMurid;
        $this->tagihanBelumLunas = $tagihanBelumLunas;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return []; // Kita kirim manual via Fonnte
    }

    /**
     * Method kustom untuk kirim ke Fonnte (dipanggil manual dari Command)
     */
    public function sendToFonnte(WaliMurid $notifiable)
    {
        $namaSiswa = $this->waliMurid->siswa->nama_lengkap ?? 'Siswa Ybs';
        $nomorHp = $notifiable->no_hp; 

        if (!$nomorHp) {
             Log::warning("Pengingat Tagihan: Nomor HP Wali Murid ID " . $this->waliMurid->id . " kosong.");
             return null;
        }

        // Format nomor HP ke 62...
        if (substr($nomorHp, 0, 1) === '0') {
            $nomorHp = '62' . substr($nomorHp, 1);
        }

        // Buat daftar tagihan
        $daftarTagihanText = "";
        $totalTunggakan = 0;
        foreach ($this->tagihanBelumLunas as $index => $tagihan) {
            $nomor = $index + 1;
            // Set lokasi Carbon ke Indonesia
            setlocale(LC_TIME, 'id_ID');
            \Carbon\Carbon::setLocale('id');
            
            // Format Periode (Bulan Tahun)
            $periode = \Carbon\Carbon::createFromDate($tagihan->tahun, $tagihan->bulan, 1)->translatedFormat('F Y');
            // Format Jumlah (dari kolom 'total_tagihan')
            $jumlah = 'Rp ' . number_format($tagihan->total_tagihan, 0, ',', '.');
            // Format Tanggal Jatuh Tempo
            $jatuhTempo = \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->translatedFormat('d F Y');

            $daftarTagihanText .= "\n" . $nomor . ". Periode: *" . $periode . "* - Jumlah: *" . $jumlah . "* (Jatuh Tempo: " . $jatuhTempo . ")";
            $totalTunggakan += $tagihan->total_tagihan; // Ambil dari 'total_tagihan'
        }

        $totalTunggakanFormatted = 'Rp ' . number_format($totalTunggakan, 0, ',', '.');

        $pesan = "Yth. Bapak/Ibu Wali Murid dari *" . $namaSiswa . "*,\n\n"
               . "Kami ingin mengingatkan bahwa terdapat tagihan SPP yang masih BELUM LUNAS atas nama siswa tersebut. Berikut rinciannya:"
               . $daftarTagihanText . "\n\n"
               . "*Total Tunggakan:* " . $totalTunggakanFormatted . "\n\n"
               . "Mohon untuk segera melakukan pembayaran melalui aplikasi atau di sekolah.\n\n"
               . "Terima kasih atas perhatiannya.\n"
               . "*Admin Keuangan Sekolah*";

        $token = config('services.fonnte.token');
        if (!$token) {
            Log::error('Pengingat Tagihan: Fonnte token tidak ditemukan di config/services.php.');
            return null;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $nomorHp,
                'message' => $pesan,
                'countryCode' => '62',
            ]);

            if (!$response->successful()) {
                Log::error("Pengingat Tagihan: Gagal kirim ke Fonnte untuk {$nomorHp}. Status: " . $response->status() . " Body: " . $response->body());
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Pengingat Tagihan: Exception saat kirim ke Fonnte untuk ' . $nomorHp . '. Error: ' . $e->getMessage());
            return null;
        }
    }
}
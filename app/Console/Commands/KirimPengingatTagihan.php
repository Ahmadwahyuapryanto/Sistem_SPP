<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\WaliMurid;
use App\Models\Tagihan;
use App\Notifications\PengingatTagihanNotification;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class KirimPengingatTagihan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:kirim-pengingat-tagihan {--paksa : Kirim pengingat meskipun belum jatuh tempo atau baru saja dikirim}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Kirim notifikasi WA pengingat tagihan SPP yang belum lunas dan sudah jatuh tempo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai proses pengiriman pengingat tagihan...');
        Log::info('Scheduler: Memulai proses KirimPengingatTagihan.');

        $paksa = $this->option('paksa'); // Ambil nilai opsi --paksa

        // 1. Dapatkan semua Wali Murid yang punya siswa dengan tagihan BELUM LUNAS
        // Saya sesuaikan nama relasi menjadi 'tagihan' (singular) sesuai Model Siswa-mu
        $waliMuridsQuery = WaliMurid::whereHas('siswa.tagihan', function ($query) use ($paksa) {
            $query->where('status', 'BELUM LUNAS');
            if (!$paksa) {
                // Hanya tagihan yang sudah jatuh tempo JIKA tidak dipaksa
                // Kodemu sudah punya kolom 'tanggal_jatuh_tempo' di migrasi, jadi kita pakai
                $query->whereDate('tanggal_jatuh_tempo', '<=', Carbon::today()); 
            }
        });

        // Eager load relasi siswa dan tagihan yang belum lunas saja
        $waliMurids = $waliMuridsQuery->with(['siswa.tagihan' => function ($query) use ($paksa) { // relasi 'tagihan'
            $query->where('status', 'BELUM LUNAS');
            if (!$paksa) {
                $query->whereDate('tanggal_jatuh_tempo', '<=', Carbon::today());
            }
            // Urutkan tagihan berdasarkan tahun lalu bulan
            $query->orderBy('tahun', 'asc')->orderBy('bulan', 'asc'); 
        }])
        ->whereNotNull('no_hp') // Hanya ambil wali murid yang punya nomor HP
        ->whereHas('siswa')    // Pastikan relasi siswa ada
        ->get();


        if ($waliMurids->isEmpty()) {
            $this->info('Tidak ada wali murid dengan tagihan tertunggak yang memenuhi kriteria.');
            Log::info('Scheduler: Tidak ada tagihan tertunggak ditemukan.');
            return 0;
        }

        $this->info("Ditemukan " . $waliMurids->count() . " wali murid dengan tagihan tertunggak.");
        $berhasilKirim = 0;
        $gagalKirim = 0;

        // 2. Loop setiap wali murid dan kirim notifikasi
        foreach ($waliMurids as $wali) {
             // Pastikan siswa dan tagihan (yang sudah difilter) benar-benar ada
             // Saya sesuaikan nama relasi 'tagihan' (singular)
            if (!$wali->siswa || $wali->siswa->tagihan->isEmpty()) {
                Log::warning("Scheduler: Skip wali murid ID: " . $wali->id . " (Relasi siswa/tagihan yang relevan tidak ditemukan setelah eager loading).");
                continue;
            }

            // Ambil tagihan yang belum lunas dari relasi yang sudah di-load
            $tagihanBelumLunas = $wali->siswa->tagihan;

            try {
                // Buat instance notifikasi
                $notification = new PengingatTagihanNotification($wali, $tagihanBelumLunas);

                // Kirim notifikasi via Fonnte
                $response = $notification->sendToFonnte($wali); // Panggil method 'sendToFonnte'

                if ($response && $response->successful()) {
                    $this->info(" > Berhasil kirim pengingat ke: " . $wali->nama . " (Siswa: " . $wali->siswa->nama_lengkap . ")");
                    Log::info("Scheduler: Berhasil kirim pengingat ke " . $wali->no_hp);
                    $berhasilKirim++;
                } else {
                    $this->error(" > Gagal kirim pengingat ke: " . $wali->nama . " (No HP: " . $wali->no_hp . ")");
                    $gagalKirim++;
                }

            } catch (\Exception $e) {
                $this->error(" > Error saat kirim ke " . $wali->nama . ": " . $e->getMessage());
                Log::error("Scheduler: Exception saat kirim ke " . $wali->no_hp . ". Error: " . $e->getMessage());
                $gagalKirim++;
            }
        }

        $this->info("Proses pengiriman pengingat selesai. Berhasil: {$berhasilKirim}, Gagal: {$gagalKirim}.");
        Log::info("Scheduler: Selesai proses KirimPengingatTagihan. Berhasil: {$berhasilKirim}, Gagal: {$gagalKirim}.");
        return 0;
    }
}
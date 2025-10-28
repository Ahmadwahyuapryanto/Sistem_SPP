<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule; // <-- Pastikan ini ada

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// ---------------------------------------------------
// KODE YANG DIPERBARUI
// ---------------------------------------------------

// Menjadwalkan command pengingat tagihan
Schedule::command('app:kirim-pengingat-tagihan')
         ->monthlyOn(1, '08:00')           // <-- Perubahan di sini: Jalan tiap tanggal 1 jam 8 pagi
         ->timezone('Asia/Jakarta')        // Set timezone WIB
         ->withoutOverlapping()            // Mencegah command berjalan ganda
         ->onFailure(function () {
             // Opsional: Kirim notifikasi jika job gagal
             \Illuminate\Support\Facades\Log::error("Scheduler: Job KirimPengingatTagihan gagal dijalankan.");
         });

// ---------------------------------------------------
// AKHIR DARI KODE YANG DIPERBARUI
// ---------------------------------------------------
<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\PembayaranController;
use App\Http\Controllers\BiayaSppController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\WaliMuridController;
use App\Http\Controllers\MidtransWebhookController;
use App\Http\Controllers\Wali\PembayaranController as WaliPembayaranController;
use App\Http\Controllers\Auth\Wali\LoginController as WaliLoginController;
use App\Http\Controllers\StafController; // <-- TAMBAHKAN IMPORT INI

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Di sini Anda mendaftarkan semua route untuk aplikasi Anda.
|
*/

// Route Halaman Awal
Route::get('/', function () {
    return view('welcome');
});

// Route untuk Admin/Petugas
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ROUTE UNTUK SISWA (CRUD)
    Route::get('siswa/cetak', [SiswaController::class, 'cetakSiswa'])->name('siswa.cetak');
    Route::resource('siswa', SiswaController::class);

    // ROUTE UNTUK TAGIHAN
    Route::post('/tagihan/generate', [TagihanController::class, 'generate'])->name('tagihan.generate');
    Route::get('/siswa/{siswa}/tagihan', [TagihanController::class, 'show'])->name('tagihan.show');
    
    // ROUTE UNTUK PEMBAYARAN
    Route::get('/pembayaran/{tagihan}/create', [PembayaranController::class, 'create'])->name('pembayaran.create');
    Route::post('/pembayaran', [PembayaranController::class, 'store'])->name('pembayaran.store');
    
    // ROUTE UNTUK BIAYA SPP
    Route::resource('biaya-spp', BiayaSppController::class);
    
    // ROUTE UNTUK LAPORAN
    Route::get('/laporan/pembayaran', [LaporanController::class, 'pembayaran'])->name('laporan.pembayaran');
    Route::get('/laporan/pembayaran/cetak', [LaporanController::class, 'cetakPdf'])->name('laporan.cetak_pdf');

    // ROUTE UNTUK MENGELOLA AKUN WALI
    Route::get('/wali-murid/{siswa}/create', [WaliMuridController::class, 'create'])->name('wali.create');
    Route::post('/wali-murid', [WaliMuridController::class, 'store'])->name('wali.store');

    // ===================================================================
    // == ROUTE UNTUK MANAJEMEN STAF (HANYA UNTUK ADMIN) ==
    // ===================================================================
    // Route untuk Manajemen Staf (Hanya untuk Admin)
    Route::resource('staf', StafController::class)
        ->middleware('role:admin')
        ->except(['show']); // Kita tidak butuh halaman 'show'
});

// Ini memuat route default untuk login/register admin (auth.php)
require __DIR__.'/auth.php';


// ===================================================================
// == ROUTE UNTUK PORTAL WALI MURID ==
// ===================================================================

// Route untuk menampilkan & memproses form login wali murid
Route::get('wali/login', [WaliLoginController::class, 'showLoginForm'])->name('wali.login');
Route::post('wali/login', [WaliLoginController::class, 'login']);
Route::post('wali/logout', [WaliLoginController::class, 'logout'])->name('wali.logout');

// Route yang hanya bisa diakses oleh wali murid yang sudah login
Route::prefix('wali')->middleware('auth:wali')->name('wali.')->group(function() {
    
    Route::get('/dashboard', function() {
        // Mengambil data wali yang sedang login
        $wali = \Illuminate\Support\Facades\Auth::user();
        // Mengambil data siswa yang terhubung dengan wali
        $siswa = $wali->siswa;
        // Mengambil data tagihan milik siswa tersebut
        $tagihans = $siswa->tagihans()->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();
        
        return view('wali.dashboard', compact('wali', 'siswa', 'tagihans'));
    })->name('dashboard');
    // ROUTE UNTUK PROSES PEMBAYARAN
    Route::post('/pembayaran/{tagihan}', [WaliPembayaranController::class, 'create'])->name('pembayaran.create');
    
    // Anda bisa menambahkan route lain untuk wali di sini jika diperlukan
});

// ROUTE UNTUK WEBHOOK MIDTRANS (diletakkan di luar middleware auth)
Route::post('/midtrans/webhook', [MidtransWebhookController::class, 'handle'])->name('midtrans.webhook');
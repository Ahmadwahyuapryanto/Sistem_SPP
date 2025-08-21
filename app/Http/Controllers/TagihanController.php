<?php

namespace App\Http\Controllers;

use App\Models\BiayaSpp;
use App\Models\Siswa;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagihanController extends Controller
{
    /**
     * Menampilkan daftar tagihan untuk siswa tertentu.
     */
    public function show(Siswa $siswa)
    {
        // Mengambil semua tagihan milik siswa yang dipilih, diurutkan dari yang terbaru
        $tagihans = $siswa->tagihans()->orderBy('tahun', 'desc')->orderBy('bulan', 'desc')->get();

        return view('tagihan.show', compact('siswa', 'tagihans'));
    }

    /**
     * Membuat tagihan untuk semua siswa pada bulan dan tahun tertentu.
     */
    public function generate(Request $request)
    {
        // 1. Validasi input dari form di dashboard
        $request->validate([
            'bulan' => 'required|numeric|between:1,12',
            'tahun' => 'required|numeric',
            'biaya_spp_id' => 'required|exists:biaya_spps,id' // Validasi bahwa biaya_spp_id ada di tabel biaya_spps
        ]);

        // 2. Ambil data Biaya SPP berdasarkan ID yang dipilih
        $biayaSpp = BiayaSpp::find($request->biaya_spp_id);

        // Memulai transaksi database untuk memastikan semua data aman
        DB::beginTransaction();
        try {
            $siswas = Siswa::all(); // Ambil semua data siswa
            $generatedCount = 0;

            // 3. Looping untuk setiap siswa
            foreach ($siswas as $siswa) {
                // Cek apakah tagihan untuk siswa ini di periode ini sudah ada
                $existingTagihan = Tagihan::where('siswa_id', $siswa->id)
                                          ->where('bulan', $request->bulan)
                                          ->where('tahun', $request->tahun)
                                          ->exists();

                // 4. Jika tagihan belum ada, buat tagihan baru
                if (!$existingTagihan) {
                    Tagihan::create([
                        'siswa_id' => $siswa->id,
                        'bulan' => $request->bulan,
                        'tahun' => $request->tahun,
                        'jumlah' => $biayaSpp->nominal, // Ambil nominal dari data master Biaya SPP
                        'status' => 'BELUM LUNAS',
                    ]);
                    $generatedCount++;
                }
            }

            // Jika semua proses berhasil, commit transaksi
            DB::commit();

            // 5. Beri pesan feedback ke user
            if ($generatedCount > 0) {
                return back()->with('success', "$generatedCount tagihan baru berhasil digenerate.");
            }

            return back()->with('error', 'Tidak ada tagihan baru yang digenerate. Semua siswa sudah memiliki tagihan untuk periode ini.');

        } catch (\Exception $e) {
            // Jika terjadi error, batalkan semua proses yang sudah berjalan
            DB::rollBack();
            Log::error('Gagal Generate Tagihan: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat proses generate tagihan.');
        }
    }
}
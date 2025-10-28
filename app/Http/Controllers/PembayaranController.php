<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Pembayaran;
use App\Notifications\PembayaranLunasNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PembayaranController extends Controller
{
    /**
     * Menampilkan halaman form konfirmasi pembayaran.
     */
    public function create(Tagihan $tagihan)
    {
        return view('pembayaran.create', compact('tagihan'));
    }

    /**
     * Menyimpan data pembayaran dan mengirim notifikasi.
     */
    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihans,id',
            'jumlah_bayar' => 'required|numeric',
        ]);

        DB::beginTransaction();
        try {
            $tagihan = Tagihan::with('siswa')->where('id', $request->tagihan_id)->lockForUpdate()->first();

            if ($tagihan->status === 'LUNAS') {
                return back()->with('error', 'Tagihan ini sudah lunas.');
            }

            $pembayaran = Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'user_id' => Auth::id(),
                'tanggal_bayar' => now(),
                'jumlah_bayar' => $request->jumlah_bayar,
                'metode_pembayaran' => 'cash', 
            ]);

            $tagihan->update(['status' => 'LUNAS']);

            // Kirim Notifikasi WhatsApp
            (new PembayaranLunasNotification($pembayaran))->send();

            DB::commit();

            return redirect()->route('tagihan.show', $tagihan->siswa_id)
                             ->with('success', 'Pembayaran berhasil dikonfirmasi dan notifikasi telah dikirim.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error saat proses pembayaran: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat memproses pembayaran.');
        }
    }
}
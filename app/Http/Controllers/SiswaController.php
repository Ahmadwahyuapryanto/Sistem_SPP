<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
// Ganti baris ini
// use PDF; 
// Menjadi baris ini untuk menghilangkan error
use Barryvdh\DomPDF\Facade\Pdf;

class SiswaController extends Controller
{
    /**
     * Display a listing of the resource.
     * Fungsi ini sekarang akan menangani filter dan pencarian.
     */
    public function index(Request $request)
    {
        // Ambil daftar kelas unik untuk dropdown filter
        $kelas_list = Siswa::select('kelas')->distinct()->orderBy('kelas')->get();

        // Mulai query dasar
        $query = Siswa::query();

        // 1. Logika untuk Pencarian (berdasarkan nama atau NIS)
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        // 2. Logika untuk Filter per Kelas
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        // Eksekusi query dengan paginasi
        $siswas = $query->latest()->paginate(10);

        // Kirim data ke view
        return view('siswa.index', compact('siswas', 'kelas_list'));
    }

    // FUNGSI BARU UNTUK CETAK PDF
    public function cetakSiswa(Request $request)
    {
        // Mulai query dasar
        $query = Siswa::query();

        // Terapkan filter yang sama seperti di halaman index
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->input('kelas'));
        }

        // Ambil semua data siswa yang cocok tanpa paginasi
        $siswas = $query->orderBy('kelas')->orderBy('nama_lengkap')->get();

        // Data tambahan untuk judul PDF
        $filterInfo = [
            'kelas' => $request->input('kelas', 'Semua Kelas'),
            'search' => $request->input('search', 'Semua Siswa'),
        ];

        // Buat PDF
        $pdf = Pdf::loadView('siswa.cetak_pdf', compact('siswas', 'filterInfo'));
        
        // Atur nama file dan tampilkan di browser
        return $pdf->stream('laporan-data-siswa.pdf');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('siswa.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis',
            'nama_lengkap' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'no_hp_wali' => 'required|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        Siswa::create($request->all());

        return redirect()->route('siswa.index')
                         ->with('success', 'Siswa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Siswa $siswa)
    {
        return view('siswa.edit', compact('siswa'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nis' => 'required|unique:siswas,nis,' . $siswa->id,
            'nama_lengkap' => 'required|string|max:255',
            'kelas' => 'required|string|max:50',
            'no_hp_wali' => 'required|string|max:15',
            'alamat' => 'nullable|string',
        ]);

        $siswa->update($request->all());

        return redirect()->route('siswa.index')
                         ->with('success', 'Data siswa berhasil diupdate.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Siswa $siswa)
    {
        $siswa->delete();

        return redirect()->route('siswa.index')
                         ->with('success', 'Data siswa berhasil dihapus.');
    }
}
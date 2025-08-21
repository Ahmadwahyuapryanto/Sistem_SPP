<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\WaliMurid;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class WaliMuridController extends Controller
{
    public function create(Siswa $siswa)
    {
        // Pastikan siswa belum punya akun wali
        if ($siswa->wali) {
            return redirect()->route('siswa.index')->with('error', 'Siswa ini sudah memiliki akun wali.');
        }
        return view('wali_murid.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id|unique:wali_murids,siswa_id',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:wali_murids,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        WaliMurid::create([
            'siswa_id' => $request->siswa_id,
            'nama' => $request->nama,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('siswa.index')->with('success', 'Akun Wali Murid berhasil dibuat.');
    }
}
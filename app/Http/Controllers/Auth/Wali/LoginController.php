<?php

namespace App\Http\Controllers\Auth\Wali;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Siswa; // Import model Siswa

class LoginController extends Controller
{
    /**
     * Menampilkan form login untuk wali.
     */
    public function showLoginForm()
    {
        return view('auth_wali.login');
    }

    /**
     * Menangani permintaan login.
     */
    public function login(Request $request)
    {
        // Validasi input form
        $request->validate([
            'email' => 'required|string', // 'email' di sini adalah nama_lengkap siswa
            'password' => 'required|string', // 'password' di sini adalah nis siswa
        ]);

        // Cari siswa berdasarkan nama lengkap dan NIS
        $siswa = Siswa::where('nama_lengkap', $request->email)
                      ->where('nis', $request->password)
                      ->first();

        // Cek jika siswa ditemukan DAN siswa tersebut memiliki relasi ke wali
        if ($siswa && $siswa->wali) {
            // Jika ditemukan, login-kan akun wali yang bersangkutan
            Auth::guard('wali')->login($siswa->wali);

            // Arahkan ke dashboard wali
            return redirect()->intended(route('wali.dashboard'));
        }

        // Jika tidak ditemukan atau tidak punya akun wali, kembalikan ke form login dengan pesan error
        return back()->withErrors([
            'email' => 'Username Atau Password tidak cocok.',
        ])->onlyInput('email');
    }

    /**
     * Menangani proses logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('wali')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // UBAH BARIS INI
        // return redirect('/'); // <-- Kode lama
        return redirect()->route('wali.login'); // <-- Kode baru, mengarahkan ke halaman login wali
    }
}
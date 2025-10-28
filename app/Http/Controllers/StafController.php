<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class StafController extends Controller
{
    /**
     * Menampilkan daftar staf.
     */
    public function index()
    {
        $stafs = User::where('role', 'staf')->latest()->paginate(10);
        return view('staf.index', compact('stafs'));
    }

    /**
     * Menampilkan form untuk membuat staf baru.
     */
    public function create()
    {
        return view('staf.create');
    }

    /**
     * Menyimpan staf baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'staf',
        ]);

        return redirect()->route('staf.index')->with('success', 'Akun staf berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit staf.
     */
    public function edit(User $staf)
    {
        // Pastikan kita tidak mencoba mengedit admin
        if ($staf->role === 'admin') {
            abort(404);
        }
        return view('staf.edit', compact('staf'));
    }

    /**
     * Memperbarui data staf di database.
     */
    public function update(Request $request, User $staf)
    {
        // Pastikan kita tidak mencoba mengedit admin
        if ($staf->role === 'admin') {
            abort(404);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staf->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $staf->name = $request->name;
        $staf->email = $request->email;
        
        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $staf->password = Hash::make($request->password);
        }
        
        $staf->save();

        return redirect()->route('staf.index')->with('success', 'Akun staf berhasil diperbarui.');
    }

    /**
     * Menghapus staf dari database.
     */
    public function destroy(User $staf)
    {
        // Pastikan kita tidak mencoba menghapus admin
        if ($staf->role === 'admin') {
            abort(404);
        }

        $staf->delete();
        return redirect()->route('staf.index')->with('success', 'Akun staf berhasil dihapus.');
    }
}
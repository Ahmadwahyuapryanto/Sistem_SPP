<?php

namespace App\Http\Controllers;

use App\Models\BiayaSpp;
use Illuminate\Http\Request;

class BiayaSppController extends Controller
{
    public function index()
    {
        $biayaSpps = BiayaSpp::latest()->paginate(10);
        return view('biaya_spp.index', compact('biayaSpps'));
    }

    public function create()
    {
        return view('biaya_spp.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_biaya' => 'required|string|max:255',
            'nominal' => 'required|numeric',
        ]);

        BiayaSpp::create($request->all());

        return redirect()->route('biaya-spp.index')->with('success', 'Biaya SPP berhasil ditambahkan.');
    }

    public function edit(BiayaSpp $biayaSpp)
    {
        return view('biaya_spp.edit', compact('biayaSpp'));
    }

    public function update(Request $request, BiayaSpp $biayaSpp)
    {
        $request->validate([
            'nama_biaya' => 'required|string|max:255',
            'nominal' => 'required|numeric',
        ]);

        $biayaSpp->update($request->all());

        return redirect()->route('biaya-spp.index')->with('success', 'Biaya SPP berhasil diupdate.');
    }

    public function destroy(BiayaSpp $biayaSpp)
    {
        $biayaSpp->delete();
        return redirect()->route('biaya-spp.index')->with('success', 'Biaya SPP berhasil dihapus.');
    }
}
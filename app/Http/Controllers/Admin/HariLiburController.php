<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    // 1. TAMPILKAN HALAMAN UTAMA
    public function index()
    {
        // Urutkan dari tanggal terbaru ke terlama
        $hari_libur = HariLibur::orderBy('tanggal', 'desc')->paginate(10);
        
        // PERBAIKAN: Mengarah ke folder 'hari_libur' (underscore)
        return view('admin.hari_libur.index', compact('hari_libur'));
    }

    // 2. SIMPAN DATA BARU
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal',
            'keterangan' => 'required|string|max:255',
        ], [
            'tanggal.unique' => 'Tanggal ini sudah terdaftar sebagai hari libur.'
        ]);

        HariLibur::create([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan
        ]);

        return back()->with('success', 'Hari libur berhasil ditambahkan!');
    }

    // 3. UPDATE DATA
    public function update(Request $request, $id)
    {
        $hariLibur = HariLibur::findOrFail($id);

        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal,' . $id,
            'keterangan' => 'required|string|max:255',
        ]);

        $hariLibur->update([
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan
        ]);

        return back()->with('success', 'Data hari libur berhasil diperbarui!');
    }

    // 4. HAPUS DATA
    public function destroy($id)
    {
        HariLibur::findOrFail($id)->delete();
        return back()->with('success', 'Hari libur berhasil dihapus!');
    }
}
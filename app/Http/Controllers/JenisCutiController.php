<?php

namespace App\Http\Controllers;

use App\Models\JenisCuti;
use Illuminate\Http\Request;

class JenisCutiController extends Controller
{
    public function index()
    {
        // Ambil data urut berdasarkan ID
        $jenis_cuti = JenisCuti::orderBy('id', 'asc')->get();
        return view('admin.jenis_cuti.index', compact('jenis_cuti'));
    }

    public function store(Request $request)
    {
        // Validasi
        $request->validate([
            'nama_cuti' => 'required|string|max:255|unique:jenis_cuti,nama_cuti',
            'max_hari' => 'required|integer|min:1',
        ], [
            'nama_cuti.unique' => 'Nama jenis cuti ini sudah ada!',
            'max_hari.min' => 'Maksimal hari minimal 1 hari.'
        ]);

        // Simpan
        JenisCuti::create([
            'nama_cuti' => $request->nama_cuti,
            'max_hari' => $request->max_hari
        ]);

        return redirect()->back()->with('success', 'Jenis cuti berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $jenis = JenisCuti::findOrFail($id);

        $request->validate([
            'nama_cuti' => 'required|string|max:255|unique:jenis_cuti,nama_cuti,' . $id,
            'max_hari' => 'required|integer|min:1',
        ]);

        $jenis->update([
            'nama_cuti' => $request->nama_cuti,
            'max_hari' => $request->max_hari
        ]);

        return redirect()->back()->with('success', 'Jenis cuti berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $jenis = JenisCuti::findOrFail($id);
        $jenis->delete();

        return redirect()->back()->with('success', 'Jenis cuti berhasil dihapus!');
    }
}
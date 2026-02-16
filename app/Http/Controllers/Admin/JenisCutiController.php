<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JenisCuti;
use Illuminate\Http\Request;

class JenisCutiController extends Controller
{
    public function index()
    {
        $jenis_cuti = JenisCuti::all();
        return view('admin.jenis_cuti.index', compact('jenis_cuti'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_cuti' => 'required|unique:jenis_cuti,nama_cuti',
            'max_hari' => 'required|integer|min:1',
        ]);

        JenisCuti::create($request->all());
        return back()->with('success', 'Jenis cuti berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $jc = JenisCuti::findOrFail($id);
        $jc->update($request->all());
        return back()->with('success', 'Jenis cuti berhasil diupdate');
    }

    public function destroy($id)
    {
        JenisCuti::findOrFail($id)->delete();
        return back()->with('success', 'Jenis cuti berhasil dihapus');
    }
}
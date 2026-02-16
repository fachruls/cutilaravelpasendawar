<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    // 1. TAMPILKAN HALAMAN FILTER
    public function index()
    {
        return view('admin.laporan.index');
    }

    // 2. PROSES EXPORT KE EXCEL
    public function export(Request $request)
    {
        // Validasi Tanggal
        $request->validate([
            'tanggal_awal' => 'required|date',
            'tanggal_akhir' => 'required|date|after_or_equal:tanggal_awal',
        ]);

        $awal = $request->tanggal_awal;
        $akhir = $request->tanggal_akhir;

        // Ambil Data Cuti yang DISETUJUI dalam rentang tanggal
        $laporan = Cuti::with('user')
            ->where('status', 'Disetujui') // Hanya yang sudah disetujui
            ->where(function($q) use ($awal, $akhir) {
                $q->whereBetween('tanggal_mulai', [$awal, $akhir])
                  ->orWhereBetween('tanggal_selesai', [$awal, $akhir]);
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // Nama File saat didownload
        $filename = "Laporan_Cuti_{$awal}_sd_{$akhir}.xls";

        // Trik Header agar browser membacanya sebagai Excel
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Return View Khusus Tabel (Tanpa Layout Admin)
        return view('admin.laporan.excel', compact('laporan', 'awal', 'akhir'));
    }
}
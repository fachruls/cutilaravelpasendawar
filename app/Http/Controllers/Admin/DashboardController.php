<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Cuti;
use App\Models\HariLibur; // Jangan lupa import model ini
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // 1. HALAMAN DASHBOARD ADMIN
    public function index()
    {
        // Hitung Statistik (Sesuai dashboard.php native)
        $stats = [
            'total_pegawai' => User::where('role', 'pegawai')->count(),
            'total_admin'   => User::whereIn('role', ['admin', 'pimpinan'])->count(),
            'cuti_menunggu' => Cuti::where('status', 'Menunggu')->count(),
            'cuti_disetujui'=> Cuti::where('status', 'Disetujui')->count(),
        ];

        // 5 Pengajuan Terbaru
        $cuti_terbaru = Cuti::with('user')->orderBy('id', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'cuti_terbaru'));
    }
    
    // 2. HALAMAN KALENDER CUTI (Fitur Baru)
    public function kalender(Request $request)
    {
        // A. Tentukan Bulan & Tahun (Default: Bulan ini jika tidak ada input)
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // B. Tentukan Tanggal Awal & Akhir Bulan tersebut
        $first_day = date('Y-m-d', strtotime("$tahun-$bulan-01"));
        $last_day = date('Y-m-d', strtotime("last day of $tahun-$bulan"));

        // C. Ambil Data Cuti yang BERIRISAN dengan bulan ini
        // Logikanya: Cuti mulai di bulan ini ATAU selesai di bulan ini ATAU melewati bulan ini
        $cuti_bulan = Cuti::with('user')
            ->where(function($q) use ($first_day, $last_day) {
                $q->whereBetween('tanggal_mulai', [$first_day, $last_day])
                  ->orWhereBetween('tanggal_selesai', [$first_day, $last_day])
                  ->orWhere(function($sub) use ($first_day, $last_day) {
                      $sub->where('tanggal_mulai', '<=', $first_day)
                          ->where('tanggal_selesai', '>=', $last_day);
                  });
            })
            ->orderBy('tanggal_mulai', 'asc')
            ->get();

        // D. Ambil Hari Libur Nasional di bulan ini
        $hari_libur = HariLibur::whereMonth('tanggal', $bulan)
                               ->whereYear('tanggal', $tahun)
                               ->get()
                               ->keyBy('tanggal'); // Index array menggunakan tanggal biar mudah di-cek di view

        return view('admin.kalender', compact('cuti_bulan', 'hari_libur', 'bulan', 'tahun'));
    }
}
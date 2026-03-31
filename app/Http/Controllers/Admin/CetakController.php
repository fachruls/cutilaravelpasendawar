<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cuti;
use App\Models\User;
use App\Models\HariLibur; 
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;       
use Carbon\CarbonPeriod; 

class CetakController extends Controller
{
    public function formulir($id)
    {
        // 1. Ambil Data
        $cuti = Cuti::with('user')->findOrFail($id);
        $currentUser = auth()->user();

        // 2. SECURITY CHECK
        if ($currentUser->role == 'pegawai' && $cuti->user_id != $currentUser->id) {
            abort(403, 'Akses Ditolak: Dokumen ini bukan milik Anda.');
        }

        // 3. VALIDASI STATUS
        if (!in_array($cuti->status, ['Disetujui', 'Ditolak'])) {
            return back()->with('error', 'Dokumen belum diproses, tidak bisa dicetak.');
        }

        // 4. MANUAL RELASI & PERBAIKAN PENCARIAN PEJABAT
        $user_atasan = User::where('name', 'LIKE', '%' . $cuti->atasan_langsung . '%')->first();
        
        // Cari pejabat berdasarkan nama yang diketik
        $user_pejabat = User::where('name', 'LIKE', '%' . $cuti->pejabat_berwenang . '%')->first();

        // [FIX]: Jika tidak ketemu (misal diketik 'Ketua PA' tapi nama asli 'Erik'),
        // Ambil otomatis user yang punya role 'pimpinan' (Ketua)
        if (!$user_pejabat) {
            $user_pejabat = User::where('role', 'pimpinan')->first();
        }

        $cuti->atasan = $user_atasan;
        $cuti->pejabat = $user_pejabat; // Data Ketua (termasuk NIP) disimpan disini

        // 5. LOGIKA HITUNG HARI KERJA (REAL-TIME)
        $start = Carbon::parse($cuti->tanggal_mulai);
        $end = Carbon::parse($cuti->tanggal_selesai);
        
        $libur_nasional = HariLibur::whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                                   ->get()
                                   ->pluck('tanggal')
                                   ->map(function ($date) {
                                       return $date->format('Y-m-d');
                                   })
                                   ->toArray();

        $total_hari_kerja = 0;
        $periode = CarbonPeriod::create($start, $end);

        foreach ($periode as $date) {
            $formatDate = $date->format('Y-m-d');
            if (!$date->isWeekend() && !in_array($formatDate, $libur_nasional)) {
                $total_hari_kerja++;
            }
        }

        $cuti->lama_hari_kerja = $total_hari_kerja; 

        // 6. GENERATE PDF
        $pdf = Pdf::loadView('admin.cuti.cetak_formulir', compact('cuti'))
                  ->setPaper('legal', 'portrait'); 

        $namaFile = 'Formulir_Cuti_' . str_replace(' ', '_', $cuti->user->name) . '_' . $cuti->tanggal_mulai . '.pdf';

        if (request()->has('download')) {
            return $pdf->download($namaFile);
        }

        return $pdf->stream($namaFile);
    }
}
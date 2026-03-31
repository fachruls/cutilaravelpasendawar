<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Cuti;
use App\Models\HariLibur; // <--- WAJIB DITAMBAHKAN
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * DASHBOARD UTAMA (Digunakan oleh semua role)
     */
    public function index()
    {
        $user = Auth::user();

        // 1. DASHBOARD ADMIN
        if ($user->role == 'admin') {
            $stats = [
                'total_pegawai' => User::where('role', 'pegawai')->count(),
                'total_admin'   => User::whereIn('role', ['pimpinan', 'admin'])->count(),
                'cuti_menunggu' => Cuti::where('status', 'Menunggu')->count(),
                'cuti_disetujui'=> Cuti::where('status', 'Disetujui')->count(),
            ];
            $cuti_terbaru = Cuti::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
            
            // ANALYTICS TREN CUTI PER BULAN TAHUN INI
            $tahunHitung = date('Y');
            $dataBulan = array_fill(0, 12, 0);
            $cutiThnIni = Cuti::whereYear('tanggal_mulai', $tahunHitung)->get();
            foreach($cutiThnIni as $c) {
                $bulanIdx = (int) Carbon::parse($c->tanggal_mulai)->format('n') - 1;
                $dataBulan[$bulanIdx]++;
            }

            return view('admin.dashboard', compact('stats', 'cuti_terbaru', 'dataBulan', 'tahunHitung'));
        } 
        
        // 2. DASHBOARD PIMPINAN & KASUBAG (DIGABUNG)
        // [UPDATE]: Kasubag boleh masuk sini
        elseif ($user->role == 'pimpinan' || $user->role == 'kasubag') {
            $total_cuti = Cuti::count();
            
            // [LOGIKA STATUS MENUNGGU SESUAI ROLE]
            if ($user->role == 'kasubag') {
                // Kasubag melihat yang 'Menunggu Verifikasi'
                $menunggu = Cuti::where('status', 'Menunggu Verifikasi')->count();
            } else {
                // Pimpinan melihat yang 'Menunggu Pejabat'
                $menunggu = Cuti::where('status', 'Menunggu Pejabat')->orWhere('status', 'Menunggu')->count();
            }

            $disetujui = Cuti::where('status', 'Disetujui')->count();
            $ditolak = Cuti::where('status', 'Ditolak')->count();
            $cuti_terbaru = Cuti::with('user')->orderBy('created_at', 'desc')->limit(5)->get();
            
            // ANALYTICS TREN CUTI PER BULAN TAHUN INI
            $tahunHitung = date('Y');
            $dataBulan = array_fill(0, 12, 0);
            $cutiThnIni = Cuti::whereYear('tanggal_mulai', $tahunHitung)->get();
            foreach($cutiThnIni as $c) {
                $bulanIdx = (int) Carbon::parse($c->tanggal_mulai)->format('n') - 1;
                $dataBulan[$bulanIdx]++;
            }

            return view('pimpinan.dashboard', compact('total_cuti', 'menunggu', 'disetujui', 'ditolak', 'cuti_terbaru', 'dataBulan', 'tahunHitung'));
        }

        // 3. DASHBOARD PEGAWAI (SOLUSI HITUNG MANUAL)
        elseif ($user->role == 'pegawai') {
            $hak_cuti = $user->hak_cuti_tahunan ?? 12;

            // AMBIL SEMUA DATA
            $semua_cuti = Cuti::where('user_id', $user->id)->get();

            // CARI TAHUN DARI DATA TERAKHIR
            $data_terakhir = $semua_cuti->where('status', 'Disetujui')->sortByDesc('tanggal_mulai')->first();
            $active_year = $data_terakhir ? Carbon::parse($data_terakhir->tanggal_mulai)->year : date('Y');

            // --- HITUNG MANUAL (LOOPING) ---
            $terpakai = 0;
            foreach ($semua_cuti as $cuti) {
                // 1. Cek Tahun
                $tahun_cuti = Carbon::parse($cuti->tanggal_mulai)->year;
                
                // 2. Cek Status (Harus mengandung kata 'Setuju')
                $is_approved = stripos($cuti->status, 'Setuju') !== false;

                // [PERBAIKAN DISINI]: Tambahkan syarat jenis_cuti harus 'Cuti Tahunan'
                if ($tahun_cuti == $active_year && $is_approved && $cuti->jenis_cuti == 'Cuti Tahunan') {
                    $terpakai += (int) $cuti->lama; 
                }
            }

            // HITUNG LAINNYA
            $sisa_cuti = max(0, $hak_cuti - $terpakai);
            
            $menunggu = $semua_cuti->filter(function($item) {
                return stripos($item->status, 'Tunggu') !== false || stripos($item->status, 'Menunggu') !== false;
            })->count();

            $cuti_mendatang = Cuti::where('user_id', $user->id)
                                  ->where('status', 'Disetujui')
                                  ->whereDate('tanggal_mulai', '>=', Carbon::now()->startOfDay())
                                  ->orderBy('tanggal_mulai', 'asc')
                                  ->limit(5)
                                  ->get();

            return view('pegawai.dashboard', compact(
                'hak_cuti', 'terpakai', 'sisa_cuti', 'menunggu', 'cuti_mendatang', 'active_year'
            ));
        } 
        
        else {
            abort(403, 'Unauthorized action.');
        }
    }

    // Alias untuk route khusus pimpinan
    public function pimpinanIndex() { return $this->index(); }

    /**
     * UPDATE PLH (PELAKSANA HARIAN)
     * Dipanggil dari form di dashboard Pimpinan/Atasan
     */
    public function updatePlh(Request $request)
    {
        $user = Auth::user();
        
        // SECURITY FIX: Hanya pimpinan, kasubag, atau atasan yang punya bawahan boleh set PLH
        $is_atasan = User::where('atasan_id', $user->id)->exists();
        if (!in_array($user->role, ['pimpinan', 'kasubag']) && !$is_atasan) {
            abort(403, 'Anda tidak memiliki akses untuk menunjuk Pelaksana Harian.');
        }

        // Validasi: Plh tidak boleh diri sendiri
        if($request->plh_id == $user->id) {
            return back()->with('error', 'Tidak bisa menunjuk diri sendiri sebagai Plh.');
        }

        // Validasi: Plh harus pegawai yang valid (bukan admin)
        if ($request->plh_id) {
            $plh_user = User::find($request->plh_id);
            if (!$plh_user || $plh_user->role == 'admin') {
                return back()->with('error', 'User yang dipilih tidak valid sebagai Plh.');
            }
        }

        // Update PLH (set null jika kosong)
        $user->update([
            'plh_id' => $request->plh_id ?: null
        ]);
        
        $status = $request->plh_id ? "diaktifkan" : "dinonaktifkan";
        $nama_plh = $request->plh_id ? User::find($request->plh_id)->name : '';

        return back()->with('success', "Status Plh berhasil $status" . ($nama_plh ? " ke $nama_plh" : "") . ".");
    }

    /**
     * MENAMPILKAN KALENDER CUTI (Untuk Pimpinan)
     * Perbaikan: Menambahkan pengambilan data Hari Libur
     */
    public function kalender(Request $request)
    {
        // 1. Tentukan Bulan & Tahun (Default: Saat Ini)
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        // 2. Ambil cuti disetujui PADA BULAN TERSEBUT
        $cuti_bulan = Cuti::with('user')
                    ->where('status', 'Disetujui')
                    ->where(function($query) use ($bulan, $tahun) {
                         $query->whereMonth('tanggal_mulai', $bulan)
                               ->whereYear('tanggal_mulai', $tahun);
                    })
                    ->get();

        // 3. Ambil Data HARI LIBUR (Supaya kalender tidak error)
        $hari_libur = HariLibur::whereMonth('tanggal', $bulan)
                        ->whereYear('tanggal', $tahun)
                        ->get()
                        ->keyBy('tanggal');

        // 4. Kirim ke View 'admin.kalender'
        return view('admin.kalender', compact('cuti_bulan', 'bulan', 'tahun', 'hari_libur'));
    }
}
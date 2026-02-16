<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PersetujuanController extends Controller
{
    // Helper: Cek apakah saya Plh dari User X?
    private function isPlhOf($original_user_id)
    {
        if (!$original_user_id) return false;
        $original = User::find($original_user_id);
        return $original && $original->plh_id == Auth::id();
    }

    // 1. DAFTAR CUTI (TAMPILAN ACC)
    public function index()
    {
        $user = Auth::user();

        // Proteksi: Pegawai Biasa DILARANG MASUK
        $is_kasubag = $user->role == 'kasubag'; 
        $is_pimpinan = $user->role == 'pimpinan';
        $is_atasan = User::where('atasan_id', $user->id)->exists(); 
        $is_plh    = User::where('plh_id', $user->id)->exists(); 

        if ($user->role == 'pegawai' && !$is_atasan && !$is_plh && !$is_kasubag) {
            abort(403, 'Anda tidak memiliki akses ke halaman persetujuan.');
        }

        // Query Utama
        $persetujuan = Cuti::with('user')
            ->where(function($query) use ($user, $is_kasubag, $is_pimpinan) {
                
                // [TUGAS KASUBAG]
                if ($is_kasubag) {
                    $query->orWhere('status', 'Menunggu Verifikasi');
                }

                // [TUGAS ATASAN]
                $query->orWhere(function($subQ) use ($user) {
                    $subQ->where('status', 'Menunggu Atasan')
                         ->whereHas('user', function($u) use ($user) {
                             $u->where(function($qBawahan) use ($user) {
                                 $qBawahan->where('atasan_id', $user->id)
                                          ->orWhereIn('atasan_id', function($plhQuery) use ($user) {
                                              $plhQuery->select('id')->from('users')->where('plh_id', $user->id);
                                          });
                             });
                         });
                });

                // [TUGAS PIMPINAN]
                if ($is_pimpinan) {
                    $query->orWhere('status', 'Menunggu Pejabat');
                }
            })
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('pimpinan.persetujuan.index', compact('persetujuan'));
    }

    // 2. PROSES SETUJU (ACC)
    public function setuju(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        $user = Auth::user();
        $is_plh_action = false;
        $pesan = '';

        // TAHAP 1: KASUBAG KEPEGAWAIAN VERIFIKASI
        if ($cuti->status == 'Menunggu Verifikasi' && $user->role == 'kasubag') {
            $cuti->update([
                'status' => 'Menunggu Atasan', 
            ]);
            $pesan = "Verifikasi Berhasil. Dokumen diteruskan ke Atasan Langsung.";
            $action = 'VERIFIKASI_KASUBAG';
        }
        
        // TAHAP 2: ATASAN LANGSUNG ACC
        elseif ($cuti->status == 'Menunggu Atasan') {
            // Validasi: Apakah benar atasan dia?
            $atasan_asli_id = $cuti->user->atasan_id;
            if ($user->id != $atasan_asli_id && !$this->isPlhOf($atasan_asli_id)) {
                abort(403, 'Anda bukan atasan langsung pegawai ini.');
            }
            if ($this->isPlhOf($atasan_asli_id)) $is_plh_action = true;

            // --- [LOGIKA JALAN TOL KHUSUS KETUA] ---
            if ($user->role == 'pimpinan') {
                // Jika yang ACC adalah KETUA (Pimpinan), langsung FINAL.
                // Tidak perlu oper ke 'Menunggu Pejabat' lagi.
                $cuti->update([
                    'status' => 'Disetujui', // Langsung Finish
                    'atasan_langsung' => $user->name,
                    'pejabat_berwenang' => $user->name, // Ketua merangkap Pejabat
                    'is_plh_atasan' => $is_plh_action,
                    'is_plh_pejabat' => $is_plh_action,
                    'ttd_atasan' => $this->copyTtd($user, $id),
                    'ttd_pejabat' => $this->copyTtd($user, $id), // TTD otomatis terisi di kolom pejabat juga
                    'catatan_atasan' => $request->catatan ?? 'Disetujui',
                    'catatan_pejabat' => $request->catatan ?? 'Disetujui',
                    'waktu_disetujui' => now(),
                ]);
                $pesan = "Disetujui Langsung oleh Ketua.";
                $action = 'PERSETUJUAN_FINAL_BYPASS';
            } 
            else {
                // --- [ALUR NORMAL: PEGAWAI BIASA] ---
                // Atasan biasa oper ke Ketua
                $cuti->update([
                    'status' => 'Menunggu Pejabat', 
                    'atasan_langsung' => $user->name,
                    'is_plh_atasan' => $is_plh_action,
                    'ttd_atasan' => $this->copyTtd($user, $id), 
                    'catatan_atasan' => $request->catatan ?? 'Disetujui',
                ]);
                $pesan = "Disetujui. Dokumen diteruskan ke Ketua.";
                $action = 'PERSETUJUAN_ATASAN';
            }
        }

        // TAHAP 3: PIMPINAN (KETUA) ACC FINAL (Untuk Alur Normal)
        elseif ($cuti->status == 'Menunggu Pejabat') {
            if ($user->role != 'pimpinan' && !$this->isPlhOf(User::where('role','pimpinan')->value('id'))) {
                abort(403, 'Hanya Ketua yang bisa menyetujui tahap akhir.');
            }
            if ($user->role != 'pimpinan') $is_plh_action = true;

            $cuti->update([
                'status' => 'Disetujui', 
                'pejabat_berwenang' => $user->name,
                'is_plh_pejabat' => $is_plh_action,
                'ttd_pejabat' => $this->copyTtd($user, $id), 
                'catatan_pejabat' => $request->catatan ?? 'Disetujui',
                'waktu_disetujui' => now(),
            ]);
            $pesan = "Cuti RESMI DISETUJUI.";
            $action = 'PERSETUJUAN_FINAL';
        }
        else {
            abort(403, 'Aksi tidak valid untuk status ini.');
        }

        // Catat Audit Log
        AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'details' => "Memproses cuti ID: $id",
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return back()->with('success', $pesan);
    }

    // Helper: Meng-copy Tanda Tangan
    private function copyTtd($user, $cutiId) {
        if ($user->ttd_path && Storage::disk('public')->exists($user->ttd_path)) {
            $ext = pathinfo($user->ttd_path, PATHINFO_EXTENSION);
            $path = "tanda_tangan_approval/ttd_{$user->id}_cuti_{$cutiId}_" . time() . ".{$ext}";
            Storage::disk('public')->copy($user->ttd_path, $path);
            return $path;
        }
        return null;
    }

    // 3. PROSES TOLAK
    public function tolak(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        $user = Auth::user();

        $has_access = ($cuti->status == 'Menunggu Verifikasi' && $user->role == 'kasubag') || 
                      ($cuti->status == 'Menunggu Atasan' && ($user->id == $cuti->user->atasan_id || $this->isPlhOf($cuti->user->atasan_id))) ||
                      ($cuti->status == 'Menunggu Pejabat' && ($user->role == 'pimpinan' || $this->isPlhOf(User::where('role','pimpinan')->value('id'))));

        if (!$has_access) abort(403, 'AKSES DITOLAK.');
        
        $status_baru = 'Ditolak'; 
        $alasan = $request->catatan ?? 'Ditolak';

        $updateData = ['status' => $status_baru];
        if ($cuti->status == 'Menunggu Atasan') {
            $updateData['catatan_atasan'] = $alasan;
            $updateData['atasan_langsung'] = $user->name;
        } elseif ($cuti->status == 'Menunggu Pejabat') {
            $updateData['catatan_pejabat'] = $alasan;
            $updateData['pejabat_berwenang'] = $user->name;
        }
        $cuti->update($updateData);

        // KEMBALIKAN SALDO JIKA DITOLAK
        if ($cuti->jenis_cuti == 'Cuti Tahunan') {
            $pemohon = $cuti->user;
            $pemohon->cuti_n += $cuti->lama; 
            $pemohon->save();
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'PENOLAKAN_CUTI',
            'details' => "Menolak cuti ID: $id. Alasan: $alasan",
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);
        
        return back()->with('error', 'Pengajuan cuti Ditolak.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\User;
use App\Models\AuditLog;
use App\Mail\NotifikasiCuti; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PersetujuanController extends Controller
{
    private function isPlhOf($original_user_id)
    {
        if (!$original_user_id) return false;
        $original = User::find($original_user_id);
        return $original && $original->plh_id == Auth::id();
    }

    public function index()
    {
        $user = Auth::user();

        // LOGIKA BARU: Mengenali PLH Kasubag
        $kasubag_asli = User::where('role', 'kasubag')->first();
        $is_kasubag = $user->role == 'kasubag' || ($kasubag_asli && $this->isPlhOf($kasubag_asli->id)); 
        
       $pimpinan_asli = User::where('role', 'pimpinan')->first();
        $is_pimpinan = $user->role == 'pimpinan' || ($pimpinan_asli && $this->isPlhOf($pimpinan_asli->id));
        $is_atasan = User::where('atasan_id', $user->id)->exists(); 
        $is_plh    = User::where('plh_id', $user->id)->exists(); 

        if ($user->role == 'pegawai' && !$is_atasan && !$is_plh && !$is_kasubag) {
            abort(403, 'Anda tidak memiliki akses ke halaman persetujuan.');
        }

        $persetujuan = Cuti::with('user')
            ->where(function($query) use ($user, $is_kasubag, $is_pimpinan) {
                
                if ($is_kasubag) {
                    $query->orWhere('status', 'Menunggu Verifikasi');
                }

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

                if ($is_pimpinan) {
                    $query->orWhere('status', 'Menunggu Pejabat');
                }
            })
            ->orderBy('created_at', 'asc')
            ->paginate(10);

        return view('pimpinan.persetujuan.index', compact('persetujuan'));
    }

    public function setuju(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        $user = Auth::user();
        $is_plh_action = false;
        $pesan = '';

        $target_email = null;
        $tipe_notif = '';

        // LOGIKA BARU: PLH Kasubag bisa menyetujui
        $kasubag_asli = User::where('role', 'kasubag')->first();
        if ($cuti->status == 'Menunggu Verifikasi' && ($user->role == 'kasubag' || ($kasubag_asli && $this->isPlhOf($kasubag_asli->id)))) {
            $cuti->update([
                'status' => 'Menunggu Atasan', 
            ]);
            $pesan = "Verifikasi Berhasil. Dokumen diteruskan ke Atasan Langsung.";
            $action = 'VERIFIKASI_KASUBAG';

            $atasan = User::find($cuti->user->atasan_id);
            if ($atasan && $atasan->email) {
                $target_email = $atasan->email;
                $tipe_notif = 'atasan';
            }
        }
        
        elseif ($cuti->status == 'Menunggu Atasan') {
            $atasan_asli_id = $cuti->user->atasan_id;
            if ($user->id != $atasan_asli_id && !$this->isPlhOf($atasan_asli_id)) {
                abort(403, 'Anda bukan atasan langsung pegawai ini.');
            }
            if ($this->isPlhOf($atasan_asli_id)) $is_plh_action = true;

            if ($user->role == 'pimpinan') {
                $cuti->update([
                    'status' => 'Disetujui', 
                    'atasan_langsung' => $user->name,
                    'pejabat_berwenang' => $user->name, 
                    'is_plh_atasan' => $is_plh_action,
                    'is_plh_pejabat' => $is_plh_action,
                    'ttd_atasan' => $this->copyTtd($user, $id),
                    'ttd_pejabat' => $this->copyTtd($user, $id), 
                    'catatan_atasan' => $request->catatan ?? 'Disetujui',
                    'catatan_pejabat' => $request->catatan ?? 'Disetujui',
                    'waktu_disetujui' => now(),
                ]);
                $pesan = "Disetujui Langsung oleh Ketua.";
                $action = 'PERSETUJUAN_FINAL_BYPASS';

                if ($cuti->user->email) {
                    $target_email = $cuti->user->email;
                    $tipe_notif = 'disetujui';
                }
            } 
            else {
                $cuti->update([
                    'status' => 'Menunggu Pejabat', 
                    'atasan_langsung' => $user->name,
                    'is_plh_atasan' => $is_plh_action,
                    'ttd_atasan' => $this->copyTtd($user, $id), 
                    'catatan_atasan' => $request->catatan ?? 'Disetujui',
                ]);
                $pesan = "Disetujui. Dokumen diteruskan ke Ketua.";
                $action = 'PERSETUJUAN_ATASAN';

                $ketua = User::where('role', 'pimpinan')->first();
                if ($ketua && $ketua->email) {
                    $target_email = $ketua->email;
                    $tipe_notif = 'ketua';
                }
            }
        }

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

            if ($cuti->user->email) {
                $target_email = $cuti->user->email;
                $tipe_notif = 'disetujui';
            }
        }
        else {
            abort(403, 'Aksi tidak valid untuk status ini.');
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => $action,
            'details' => "Memproses cuti ID: $id",
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        if ($target_email && $tipe_notif) {
            try {
                Mail::to($target_email)->send(new NotifikasiCuti($cuti, $tipe_notif));
            } catch (\Exception $e) {
                \Log::error("Gagal kirim email notifikasi ($tipe_notif): " . $e->getMessage());
            }
        }

        return back()->with('success', $pesan);
    }

    private function copyTtd($user, $cutiId) {
        if ($user->ttd_path && Storage::disk('public')->exists($user->ttd_path)) {
            $ext = pathinfo($user->ttd_path, PATHINFO_EXTENSION);
            $path = "tanda_tangan_approval/ttd_{$user->id}_cuti_{$cutiId}_" . time() . ".{$ext}";
            Storage::disk('public')->copy($user->ttd_path, $path);
            return $path;
        }
        return null;
    }

    public function tolak(Request $request, $id)
    {
        $cuti = Cuti::findOrFail($id);
        $user = Auth::user();

        // LOGIKA BARU: PLH Kasubag bisa menolak
        $kasubag_asli = User::where('role', 'kasubag')->first();
        $has_access = ($cuti->status == 'Menunggu Verifikasi' && ($user->role == 'kasubag' || ($kasubag_asli && $this->isPlhOf($kasubag_asli->id)))) || 
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
        
        DB::beginTransaction();
        try {
            $cuti->update($updateData);

            if ($cuti->jenis_cuti == 'Cuti Tahunan') {
                $pemohon = User::where('id', $cuti->user_id)->lockForUpdate()->first();
                $pemohon->cuti_n += $cuti->lama; 
                
                $hak_n = $pemohon->hak_cuti_tahunan ?? 12;
                if ($pemohon->cuti_n > $hak_n) {
                    $overflow_n = $pemohon->cuti_n - $hak_n;
                    $pemohon->cuti_n = $hak_n;
                    $pemohon->cuti_n1 += $overflow_n;
                    
                    if ($pemohon->cuti_n1 > 6) {
                        $overflow_n1 = $pemohon->cuti_n1 - 6;
                        $pemohon->cuti_n1 = 6;
                        $pemohon->cuti_n2 += $overflow_n1;
                        
                        if ($pemohon->cuti_n2 > 6) {
                            $pemohon->cuti_n2 = 6;
                        }
                    }
                }
                $pemohon->save();
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Terjadi kesalahan sistem saat menolak cuti.']);
        }

        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'PENOLAKAN_CUTI',
            'details' => "Menolak cuti ID: $id. Alasan: $alasan",
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        try {
            if ($cuti->user->email) {
                Mail::to($cuti->user->email)->send(new NotifikasiCuti($cuti, 'ditolak'));
            }
        } catch (\Exception $e) {
            \Log::error("Gagal kirim email penolakan: " . $e->getMessage());
        }
        
        return back()->with('error', 'Pengajuan cuti Ditolak.');
    }
    
    public function rekap()
    {
        // Menarik semua data cuti yang sudah sah disetujui
        $data_cuti = Cuti::with('user')
            ->where('status', 'Disetujui')
            ->orderBy('updated_at', 'desc')
            ->get();

        // Melempar tumpukan data tersebut ke halaman tampilan
        return view('rekap', compact('data_cuti'));
    }
}
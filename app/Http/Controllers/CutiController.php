<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\HariLibur;
use App\Models\AuditLog;
use App\Models\User;
use App\Mail\NotifikasiCuti;
use App\Notifications\CutiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CutiController extends Controller
{
    public function index()
    {
        $riwayat_cuti = Cuti::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return view('pegawai.cuti.index', compact('riwayat_cuti'));
    }

    public function create()
    {
        $jenis_cuti = JenisCuti::all();
        $user = Auth::user(); 
        
        return view('pegawai.cuti.create', compact('jenis_cuti', 'user'));
    }

    public function hitungHari(Request $request)
    {
        try {
            $start = Carbon::parse($request->mulai);
            $end = Carbon::parse($request->selesai);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Format tanggal salah']);
        }
        
        $libur_nasional = HariLibur::whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                                   ->get()
                                   ->mapWithKeys(function ($item) {
                                       return [$item->tanggal->format('Y-m-d') => $item->nama];
                                   })
                                   ->toArray();
        
        $lama_cuti = 0;
        $detail = [];
        $periode = CarbonPeriod::create($start, $end);

        foreach ($periode as $date) {
            $formatDate = $date->format('Y-m-d');
            $status = 'KERJA';
            $keterangan = '';

            if ($date->isWeekend()) {
                $status = 'LIBUR';
                $keterangan = $date->isSaturday() ? 'Sabtu' : 'Minggu';
            } 
            elseif (array_key_exists($formatDate, $libur_nasional)) {
                $status = 'LIBUR';
                $keterangan = $libur_nasional[$formatDate];
            } 
            else {
                $lama_cuti++;
            }

            $detail[] = [
                'tanggal' => $date->translatedFormat('d-m-Y'),
                'hari' => $date->locale('id')->dayName, 
                'status' => $status, 
                'keterangan' => $keterangan
            ];
        }

        return response()->json([
            'success' => true, 
            'hari_kerja' => $lama_cuti, 
            'detail' => $detail
        ]);
    }

   public function store(Request $request)
    {
        $request->validate([
            'jenis_cuti' => 'required',
            'alasan' => 'required',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'file_surat' => 'nullable|mimes:pdf|max:5120',
            'atasan_langsung' => 'required',
            'pejabat_berwenang' => 'required',
            'alamat_selama_cuti' => 'required',
            'no_hp' => 'required',
            'ttd_pegawai' => 'required',
        ]);

        $req_hitung = new Request(['mulai' => $request->tanggal_mulai, 'selesai' => $request->tanggal_selesai]);
        $hasil_hitung = $this->hitungHari($req_hitung)->getData();
        $lama = $hasil_hitung->hari_kerja; 

        DB::beginTransaction();
        try {
            if ($request->jenis_cuti == 'Cuti Tahunan') {
                $user = User::where('id', Auth::id())->lockForUpdate()->first();
                
                $saldo_n2 = (int) $user->cuti_n2; 
                $saldo_n1 = (int) $user->cuti_n1; 
                $saldo_n  = (int) $user->cuti_n;  
                
                $total_saldo = $saldo_n2 + $saldo_n1 + $saldo_n;
                $sisa_permintaan = $lama; 

                if ($lama > $total_saldo) {
                    DB::rollBack();
                    return back()
                        ->withErrors(['msg' => "Saldo tidak cukup! Total Saldo: $total_saldo, Permintaan: $lama hari."])
                        ->withInput();
                }

            if ($sisa_permintaan > 0 && $saldo_n2 > 0) {
                if ($sisa_permintaan >= $saldo_n2) {
                    $sisa_permintaan -= $saldo_n2; 
                    $saldo_n2 = 0; 
                } else {
                    $saldo_n2 -= $sisa_permintaan; 
                    $sisa_permintaan = 0; 
                }
            }

            if ($sisa_permintaan > 0 && $saldo_n1 > 0) {
                if ($sisa_permintaan >= $saldo_n1) {
                    $sisa_permintaan -= $saldo_n1; 
                    $saldo_n1 = 0; 
                } else {
                    $saldo_n1 -= $sisa_permintaan; 
                    $sisa_permintaan = 0; 
                }
            }

            if ($sisa_permintaan > 0) {
                $saldo_n -= $sisa_permintaan; 
                $sisa_permintaan = 0;
            }

            $user->update([
                'cuti_n' => $saldo_n,
                'cuti_n1' => $saldo_n1,
                'cuti_n2' => $saldo_n2
            ]);
        }

        $ttd_path = null;
        if ($request->ttd_pegawai) {
            $image_parts = explode(";base64,", $request->ttd_pegawai);
            if (count($image_parts) >= 2) {
                $image_base64 = base64_decode($image_parts[1]);
                $fileName = 'ttd_' . Auth::id() . '_' . time() . '.png';
                $ttd_path = 'tanda_tangan_pegawai/' . $fileName;
                Storage::disk('public')->put($ttd_path, $image_base64);
            }
        }

        $file_path = null;
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');
            // FIX: Gunakan ->extension() bawaan mimes Laravel bukan getClientOriginalExtension()
            $safeName = 'lampiran_' . time() . '_' . Str::random(10) . '.' . $file->extension();
            $file_path = $file->storeAs('surat_cuti', $safeName, 'local'); 
        }

        $status_awal = 'Menunggu Verifikasi';

        $cuti = Cuti::create([
            'user_id' => Auth::id(),
            'jenis_cuti' => $request->jenis_cuti,
            'alasan' => $request->alasan,
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'lama' => $lama,
            'status' => $status_awal,
            'file_surat' => $file_path,
            'ttd_path' => $ttd_path,
            'atasan_langsung' => $request->atasan_langsung,
            'pejabat_berwenang' => $request->pejabat_berwenang,
            'alamat_selama_cuti' => $request->alamat_selama_cuti,
            'no_hp' => $request->no_hp,
            'tanggal_usulan' => now(), 
        ]);

        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'PENGAJUAN_CUTI',
            'details' => "Mengajukan cuti {$request->jenis_cuti} ($lama hari)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        // FITUR BARU: Notifikasi ke Kasubag via Email queue & In-App
        try {
            $kasubag = User::where('role', 'kasubag')->first();
            if ($kasubag) {
                if ($kasubag->email) {
                    Mail::to($kasubag->email)->send(new NotifikasiCuti($cuti, 'kasubag'));
                }
                $kasubag->notify(new CutiNotification($cuti, 'kasubag', 'Pegawai ' . Auth::user()->name . ' mengajukan cuti baru.'));
            }
        } catch (\Exception $e) {
            \Log::error("Email/Notif Error Kasubag: " . $e->getMessage());
        }

        DB::commit();
        return redirect()->route('cuti.index')->with('success', 'Pengajuan berhasil! Menunggu verifikasi kepegawaian.');
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error("Error Store Cuti: " . $e->getMessage());
        return back()->withErrors(['msg' => 'Terjadi kesalahan sistem saat memproses cuti, silakan coba lagi.'])->withInput();
    }
    }

    public function destroy($id)
    {
        $cuti = Cuti::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if ($cuti->status == 'Ditolak') {
            return back()->withErrors(['msg' => 'Pengajuan yang ditolak tidak bisa dibatalkan.']);
        }
        
        if ($cuti->file_surat && Storage::disk('local')->exists($cuti->file_surat)) {
            Storage::disk('local')->delete($cuti->file_surat);
        }
        if ($cuti->ttd_path && Storage::disk('public')->exists($cuti->ttd_path)) {
            Storage::disk('public')->delete($cuti->ttd_path);
        }
        
        DB::beginTransaction();
        try {
            // FIX: Pengembalian Kuota jika Cuti Dibatalkan
            if ($cuti->jenis_cuti == 'Cuti Tahunan') {
                $pemohon = User::where('id', Auth::id())->lockForUpdate()->first();
                $pemohon->cuti_n += $cuti->lama;
                
                // Distribusi ulang sisa kuota ke N-1 dan N-2 jika meluap
                $hak_n = $pemohon->hak_cuti_tahunan ?? 12;
                if ($pemohon->cuti_n > $hak_n) {
                    $overflow_n = $pemohon->cuti_n - $hak_n;
                    $pemohon->cuti_n = $hak_n;
                    $pemohon->cuti_n1 += $overflow_n;
                    
                    if ($pemohon->cuti_n1 > 6) {
                        $overflow_n1 = $pemohon->cuti_n1 - 6;
                        $pemohon->cuti_n1 = 6;
                        $pemohon->cuti_n2 += $overflow_n1;
                        
                        // Limit N-2
                        if ($pemohon->cuti_n2 > 6) {
                            $pemohon->cuti_n2 = 6;
                        }
                    }
                }
                $pemohon->save();
            }

            $cuti->delete();
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['msg' => 'Gagal membatalkan cuti, silakan coba lagi.']);
        }
        
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'BATAL_CUTI',
            'details' => "Membatalkan pengajuan cuti ID: $id",
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);
        $user = Auth::user();

        $is_owner = $cuti->user_id == $user->id;
        $is_atasan = $user->id == $cuti->user->atasan_id;
        $is_pimpinan = $user->role == 'pimpinan';
        $is_admin = $user->role == 'admin';
        $is_kasubag = $user->role == 'kasubag'; 

        $is_plh_atasan = $cuti->user->atasan && $cuti->user->atasan->plh_id == $user->id;

        if (!$is_owner && !$is_atasan && !$is_pimpinan && !$is_admin && !$is_plh_atasan && !$is_kasubag) {
            abort(403, 'MAAF! Dokumen ini bersifat RAHASIA. Anda tidak punya hak akses.');
        }

        if (!$cuti->file_surat || !Storage::disk('local')->exists($cuti->file_surat)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return Storage::disk('local')->download($cuti->file_surat);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Cuti;
use App\Models\JenisCuti;
use App\Models\HariLibur;
use App\Models\AuditLog;
use App\Models\User;
use App\Mail\NotifikasiCuti;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class CutiController extends Controller
{
    /**
     * 1. MENAMPILKAN RIWAYAT CUTI PEGAWAI
     * Menampilkan daftar cuti yang pernah diajukan oleh user yang sedang login.
     */
    public function index()
    {
        // Ambil data cuti milik user sendiri, urutkan dari yang terbaru
        $riwayat_cuti = Cuti::where('user_id', Auth::id())
                            ->orderBy('created_at', 'desc')
                            ->get();
                            
        return view('pegawai.cuti.index', compact('riwayat_cuti'));
    }

    /**
     * 2. MENAMPILKAN FORM PENGAJUAN CUTI
     * Halaman form input tanggal, alasan, dll.
     */
    public function create()
    {
        $jenis_cuti = JenisCuti::all();
        $user = Auth::user(); // Kirim data user untuk info sisa cuti di view
        
        return view('pegawai.cuti.create', compact('jenis_cuti', 'user'));
    }

    /**
     * 3. API HITUNG HARI (AJAX)
     * Digunakan oleh JavaScript di form untuk menghitung jumlah hari kerja secara real-time.
     * Tidak menghitung Sabtu, Minggu, dan Hari Libur Nasional.
     */
    public function hitungHari(Request $request)
    {
        try {
            $start = Carbon::parse($request->mulai);
            $end = Carbon::parse($request->selesai);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Format tanggal salah']);
        }
        
        // Ambil data Hari Libur Nasional dari database di range tanggal tersebut
        $libur_nasional = HariLibur::whereBetween('tanggal', [$start->format('Y-m-d'), $end->format('Y-m-d')])
                                   ->get()
                                   ->mapWithKeys(function ($item) {
                                       return [$item->tanggal->format('Y-m-d') => $item->nama];
                                   })
                                   ->toArray();
        
        $lama_cuti = 0;
        $detail = [];
        $periode = CarbonPeriod::create($start, $end);

        // Loop setiap hari untuk cek apakah hari kerja atau libur
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
                // Jika bukan weekend dan bukan libur nasional, hitung sebagai hari kerja
                $lama_cuti++;
            }

            // Simpan detail untuk debug/tampilan jika perlu
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

    /**
     * 4. PROSES SIMPAN PENGAJUAN CUTI (INTI SISTEM)
     * Validasi, Hitung Ulang, Potong Saldo (FIFO), Upload File, Simpan ke DB.
     */
   public function store(Request $request)
    {
        // 1. VALIDASI INPUT
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

        // 2. HITUNG DURASI HARI KERJA (Server Side)
        $req_hitung = new Request(['mulai' => $request->tanggal_mulai, 'selesai' => $request->tanggal_selesai]);
        $hasil_hitung = $this->hitungHari($req_hitung)->getData();
        $lama = $hasil_hitung->hari_kerja; // Jumlah hari yang diminta

        // 3. LOGIKA PEMOTONGAN SALDO BERJENJANG (N-2 -> N-1 -> N)
        // Hanya jalan kalau jenis cutinya "Cuti Tahunan"
        if ($request->jenis_cuti == 'Cuti Tahunan') {
            $user = User::find(Auth::id());
            
            // Ambil saldo dan pastikan angka (0 jika null)
            $saldo_n2 = (int) $user->cuti_n2; // Sisa 2 Tahun Lalu
            $saldo_n1 = (int) $user->cuti_n1; // Sisa Tahun Lalu
            $saldo_n  = (int) $user->cuti_n;  // Jatah Tahun Ini
            
            $total_saldo = $saldo_n2 + $saldo_n1 + $saldo_n;
            $sisa_permintaan = $lama; // Hutang hari yang harus dipotong

            // Cek kecukupan saldo
            if ($lama > $total_saldo) {
                return back()
                    ->withErrors(['msg' => "Saldo tidak cukup! Total Saldo: $total_saldo, Permintaan: $lama hari."])
                    ->withInput();
            }

            // A. POTONG N-2 DULU (Prioritas 1)
            if ($sisa_permintaan > 0 && $saldo_n2 > 0) {
                if ($sisa_permintaan >= $saldo_n2) {
                    $sisa_permintaan -= $saldo_n2; // Masih kurang, lanjut ke N-1
                    $saldo_n2 = 0; // Habis
                } else {
                    $saldo_n2 -= $sisa_permintaan; // Cukup di N-2
                    $sisa_permintaan = 0; // Selesai
                }
            }

            // B. POTONG N-1 (Prioritas 2)
            if ($sisa_permintaan > 0 && $saldo_n1 > 0) {
                if ($sisa_permintaan >= $saldo_n1) {
                    $sisa_permintaan -= $saldo_n1; // Masih kurang, lanjut ke N
                    $saldo_n1 = 0; // Habis
                } else {
                    $saldo_n1 -= $sisa_permintaan; // Cukup di N-1
                    $sisa_permintaan = 0; // Selesai
                }
            }

            // C. POTONG N (Prioritas Terakhir)
            if ($sisa_permintaan > 0) {
                $saldo_n -= $sisa_permintaan; // Ambil dari jatah tahun ini
                $sisa_permintaan = 0;
            }

            // Simpan perubahan saldo ke Database User
            $user->update([
                'cuti_n' => $saldo_n,
                'cuti_n1' => $saldo_n1,
                'cuti_n2' => $saldo_n2
            ]);
        }

        // 4. SIMPAN TANDA TANGAN
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

        // 5. UPLOAD FILE LAMPIRAN
        $file_path = null;
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');
            $safeName = 'lampiran_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
            $file_path = $file->storeAs('surat_cuti', $safeName, 'local'); 
        }

        // 6. SIMPAN DATA CUTI (Status Awal: Menunggu Verifikasi)
        $status_awal = 'Menunggu Verifikasi';

        Cuti::create([
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

        // 7. CATAT LOG
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'PENGAJUAN_CUTI',
            'details' => "Mengajukan cuti {$request->jenis_cuti} ($lama hari)",
            'ip_address' => $request->ip(),
            'user_agent' => $request->header('User-Agent'),
        ]);

        return redirect()->route('cuti.index')->with('success', 'Pengajuan berhasil! Menunggu verifikasi kepegawaian.');
    }

    /**
     * 5. MEMBATALKAN PENGAJUAN CUTI
     * Hanya bisa jika status belum diproses (Disetujui/Ditolak).
     */
    public function destroy($id)
    {
        $cuti = Cuti::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        
        if ($cuti->status == 'Disetujui' || $cuti->status == 'Ditolak') {
            return back()->withErrors(['msg' => 'Pengajuan yang sudah selesai tidak bisa dibatalkan.']);
        }
        
        // Hapus File Fisik
        if ($cuti->file_surat && Storage::disk('local')->exists($cuti->file_surat)) {
            Storage::disk('local')->delete($cuti->file_surat);
        }
        if ($cuti->ttd_path && Storage::disk('public')->exists($cuti->ttd_path)) {
            Storage::disk('public')->delete($cuti->ttd_path);
        }
        
        $cuti->delete();
        
        AuditLog::create([
            'user_id' => Auth::id(),
            'action' => 'BATAL_CUTI',
            'details' => "Membatalkan pengajuan cuti ID: $id",
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);

        return back()->with('success', 'Pengajuan berhasil dibatalkan.');
    }

    /**
     * 6. DOWNLOAD LAMPIRAN (SECURE)
     * Memastikan hanya orang yang berhak yang bisa download file.
     */
    public function download($id)
    {
        $cuti = Cuti::findOrFail($id);
        $user = Auth::user();

        // Cek Hak Akses:
        // 1. Pemilik Cuti
        // 2. Atasan Langsung
        // 3. Pimpinan
        // 4. Admin
        // 5. Kasubag (NEW)
        
        $is_owner = $cuti->user_id == $user->id;
        $is_atasan = $user->id == $cuti->user->atasan_id;
        $is_pimpinan = $user->role == 'pimpinan';
        $is_admin = $user->role == 'admin';
        $is_kasubag = $user->role == 'kasubag'; // Kasubag boleh lihat

        // Cek juga PLH Atasan
        $is_plh_atasan = $cuti->user->atasan && $cuti->user->atasan->plh_id == $user->id;

        if (!$is_owner && !$is_atasan && !$is_pimpinan && !$is_admin && !$is_plh_atasan && !$is_kasubag) {
            abort(403, 'MAAF! Dokumen ini bersifat RAHASIA. Anda tidak punya hak akses.');
        }

        // Cek keberadaan file
        if (!$cuti->file_surat || !Storage::disk('local')->exists($cuti->file_surat)) {
            abort(404, 'File tidak ditemukan di server.');
        }

        return Storage::disk('local')->download($cuti->file_surat);
    }
}
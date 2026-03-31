<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HariLibur;
use Illuminate\Http\Request;

class HariLiburController extends Controller
{
    // 1. TAMPILKAN HALAMAN UTAMA
    public function index()
    {
        // Urutkan dari tanggal terbaru ke terlama
        $hari_libur = HariLibur::orderBy('tanggal', 'desc')->paginate(10);
        
        return view('admin.hari_libur.index', compact('hari_libur'));
    }

    // 2. SIMPAN DATA BARU
    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal',
            'keterangan' => 'required|string|max:255',
        ], [
            'tanggal.unique' => 'Tanggal ini sudah terdaftar sebagai hari libur.'
        ]);

        HariLibur::create([
            'tanggal' => $request->tanggal,
            'nama' => $request->keterangan
        ]);

        $msg = 'Hari libur resmi ditambahkan!';
        $isWeekend = \Carbon\Carbon::parse($request->tanggal)->isWeekend();

        if (!$isWeekend) {
            $sync = $this->syncCutiQuota($request->tanggal);
            if ($sync['refund'] > 0) {
                $msg .= " Otomatis merefund {$sync['refund']} hari saldo pegawai (tumpang tindih).";
            }
        }

        return back()->with('success', $msg);
    }

    // 3. UPDATE DATA
    public function update(Request $request, $id)
    {
        $hariLibur = HariLibur::findOrFail($id);
        $tanggalLama = $hariLibur->tanggal->format('Y-m-d');

        $request->validate([
            'tanggal' => 'required|date|unique:hari_libur,tanggal,' . $id,
            'keterangan' => 'required|string|max:255',
        ]);

        $hariLibur->update([
            'tanggal' => $request->tanggal,
            'nama' => $request->keterangan
        ]);

        $msg = 'Data hari libur berhasil diperbarui!';
        $isLamaWeekend = \Carbon\Carbon::parse($tanggalLama)->isWeekend();
        $isBaruWeekend = \Carbon\Carbon::parse($request->tanggal)->isWeekend();

        if (!$isLamaWeekend || !$isBaruWeekend) {
            $sync = $this->syncCutiQuota($tanggalLama, $request->tanggal);
            if ($sync['refund'] > 0) $msg .= " (+{$sync['refund']} hari salo direfund).";
            if ($sync['deduct'] > 0) $msg .= " (-{$sync['deduct']} hari saldo ditarik balik karena perubahan libur).";
        }

        return back()->with('success', $msg);
    }

    // 4. HAPUS DATA
    public function destroy($id)
    {
        $hariLibur = HariLibur::findOrFail($id);
        $tanggalLama = $hariLibur->tanggal->format('Y-m-d');
        
        $hariLibur->delete();

        $msg = 'Hari libur berhasil dihapus!';
        $isWeekend = \Carbon\Carbon::parse($tanggalLama)->isWeekend();

        if (!$isWeekend) {
            $sync = $this->syncCutiQuota($tanggalLama);
            if ($sync['deduct'] > 0) {
                $msg .= " Menagih kembali {$sync['deduct']} hari saldo pegawai yang cutinya sempat terpotong.";
            }
        }

        return back()->with('success', $msg);
    }

    // ==========================================================
    // ROBOT JENDERAL: PENGAWAS CUTI UNIVERSAL (REVERSE-SYNC)
    // ==========================================================
    private function syncCutiQuota($tanggalLiburLama, $tanggalLiburBaru = null)
    {
        $tanggals = array_filter([$tanggalLiburLama, $tanggalLiburBaru]);
        if (empty($tanggals)) return ['refund' => 0, 'deduct' => 0];
        
        $minDate = min($tanggals);
        $maxDate = max($tanggals);

        $overlappingCutis = \App\Models\Cuti::whereIn('status', ['Menunggu', 'Menunggu Pejabat', 'Menunggu Verifikasi', 'Disetujui'])
            ->where('jenis_cuti', 'Cuti Tahunan')
            ->where('tanggal_mulai', '<=', $maxDate)
            ->where('tanggal_selesai', '>=', $minDate)
            ->get();

        if ($overlappingCutis->isEmpty()) return ['refund' => 0, 'deduct' => 0];

        $cutiController = app(\App\Http\Controllers\CutiController::class);
        $refundCount = 0;
        $deductCount = 0;

        foreach ($overlappingCutis as $cuti) {
            $reqHitung = new \Illuminate\Http\Request([
                'mulai' => $cuti->tanggal_mulai,
                'selesai' => $cuti->tanggal_selesai
            ]);
            $hasil = $cutiController->hitungHari($reqHitung)->getData();
            
            if ($hasil && isset($hasil->hari_kerja)) {
                $lamaBaru = $hasil->hari_kerja;
                $selisih = $cuti->lama - $lamaBaru; 
                
                if ($selisih != 0) {
                    $cuti->lama = $lamaBaru;
                    $cuti->lama_hari_kerja = $lamaBaru;
                    $cuti->save();

                    $pemohon = \App\Models\User::find($cuti->user_id);
                    if ($pemohon) {
                        $hak_n = $pemohon->hak_cuti_tahunan ?? 12;
                        
                        if ($selisih > 0) {
                            // Cuti bertambah pendek -> Refund saldo
                            for ($i = 0; $i < $selisih; $i++) {
                                if ($pemohon->cuti_n < $hak_n) {
                                    $pemohon->increment('cuti_n');
                                } elseif ($pemohon->cuti_n1 < 6) {
                                    $pemohon->increment('cuti_n1');
                                } elseif ($pemohon->cuti_n2 < 6) {
                                    $pemohon->increment('cuti_n2');
                                }
                            }
                            $refundCount += $selisih;
                        } else {
                            // Cuti bertambah panjang -> Tagih (deduct) saldo 
                            $toDeduct = abs($selisih);
                            for ($i = 0; $i < $toDeduct; $i++) {
                                if ($pemohon->cuti_n > 0) {
                                    $pemohon->decrement('cuti_n');
                                } elseif ($pemohon->cuti_n1 > 0) {
                                    $pemohon->decrement('cuti_n1');
                                } elseif ($pemohon->cuti_n2 > 0) {
                                    $pemohon->decrement('cuti_n2');
                                }
                            }
                            $deductCount += $toDeduct;
                        }
                    }
                }
            }
        }
        return ['refund' => $refundCount, 'deduct' => $deductCount];
    }
}
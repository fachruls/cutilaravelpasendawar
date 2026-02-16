<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cuti;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UjiSistemCutiTest extends TestCase
{
    use RefreshDatabase;

    // 1. TES AUDIT LOG (SUDAH DIPERBAIKI TEKS-NYA)
    public function test_audit_log_menggunakan_kolom_details()
    {
        $user = User::factory()->create(['role' => 'pegawai']);

        // Buat data cuti dummy
        $cuti = Cuti::create([
            'user_id' => $user->id,
            'jenis_cuti' => 'Cuti Tahunan',
            'alasan' => 'Tes Audit',
            'tanggal_mulai' => now(),
            'tanggal_selesai' => now(),
            'lama' => 1,
            'status' => 'Menunggu Verifikasi',
            'atasan_langsung' => 'Atasan',
            'pejabat_berwenang' => 'Pejabat',
            'alamat_selama_cuti' => 'Test',
            'no_hp' => '0812',
            'tanggal_usulan' => now(),
        ]);

        // Hapus (Aksi ini memicu AuditLog)
        $this->actingAs($user)->delete(route('cuti.destroy', $cuti->id));

        // [PERBAIKAN]: Sesuaikan teks 'details' dengan output asli controller
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $user->id,
            'action' => 'BATAL_CUTI',
            'details' => "Membatalkan pengajuan cuti ID: {$cuti->id}", 
        ]);
    }

    // 2. TES KETUA TIDAK BISA ACC SEBELUM WAKTUNYA
    public function test_ketua_tidak_bisa_acc_staf_sebelum_atasan_langsung()
    {
        // Aktor
        $ketua = User::factory()->create(['role' => 'pimpinan']);
        $atasan = User::factory()->create(['role' => 'pegawai', 'jabatan' => 'Kasubag Umum']);
        $staf = User::factory()->create(['role' => 'pegawai', 'atasan_id' => $atasan->id]);

        // Staf mengajukan cuti (Status Awal: Menunggu Verifikasi)
        $cuti = Cuti::create([
            'user_id' => $staf->id,
            'jenis_cuti' => 'Cuti Tahunan', 
            'alasan' => 'Tes Loncat',
            'tanggal_mulai' => now(), 'tanggal_selesai' => now(), 'lama' => 1,
            'status' => 'Menunggu Verifikasi', // Masih di meja Kasubag
            'atasan_langsung' => $atasan->name,
            'pejabat_berwenang' => $ketua->name,
            'alamat_selama_cuti' => '-', 'no_hp' => '-', 'tanggal_usulan' => now()
        ]);

        // Ketua mencoba menyetujui padahal status masih 'Menunggu Verifikasi'
        // Harusnya GAGAL atau status tidak berubah jadi Disetujui
        $this->actingAs($ketua)->put(route('pimpinan.persetujuan.setuju', $cuti->id));

        // Pastikan status TIDAK berubah menjadi Disetujui
        $this->assertDatabaseMissing('cuti', [
            'id' => $cuti->id,
            'status' => 'Disetujui',
        ]);
    }

    // 3. TES KHUSUS: PANITERA (Atasan Langsung = KETUA)
    // Alur Benar: Panitera -> Kasubag Verif -> Ketua ACC (Final)
    public function test_jalan_tol_panitera_langsung_disetujui_ketua()
    {
        // A. PERSIAPAN AKTOR
        $kasubag = User::factory()->create(['role' => 'kasubag', 'name' => 'Bu Yuni']);
        $ketua = User::factory()->create(['role' => 'pimpinan', 'name' => 'Pak Ketua']);
        
        // Panitera (Atasan langsungnya adalah KETUA)
        $panitera = User::factory()->create([
            'role' => 'pegawai', 
            'jabatan' => 'Panitera',
            'atasan_id' => $ketua->id 
        ]);

        // B. PANITERA AJUKAN CUTI
        $this->actingAs($panitera)->post(route('cuti.store'), [
            'jenis_cuti' => 'Cuti Tahunan',
            'alasan' => 'Cuti Panitera',
            'tanggal_mulai' => now()->next('Monday')->format('Y-m-d'),
            'tanggal_selesai' => now()->next('Monday')->format('Y-m-d'),
            'atasan_langsung' => $ketua->name,
            'pejabat_berwenang' => $ketua->name,
            'alamat_selama_cuti' => 'Rumah',
            'no_hp' => '0812',
            'ttd_pegawai' => 'data:image/png;base64,dummy',
        ]);

        $cuti = Cuti::where('user_id', $panitera->id)->first();
        
        // Cek 1: Masuk Kasubag dulu (Menunggu Verifikasi)
        expect($cuti->status)->toBe('Menunggu Verifikasi');


        // C. KASUBAG VERIFIKASI (INI YANG KEMARIN MISS DI TES LAMA)
        $this->actingAs($kasubag)
             ->put(route('pimpinan.persetujuan.setuju', $cuti->id));

        $cuti->refresh();
        // Setelah Kasubag, status jadi 'Menunggu Atasan' (yaitu Ketua)
        expect($cuti->status)->toBe('Menunggu Atasan');


        // D. KETUA ACC SEBAGAI ATASAN LANGSUNG
        // Karena Ketua adalah Pimpinan Tertinggi, setelah dia ACC sebagai atasan,
        // sistem biasanya lanjut ke 'Menunggu Pejabat' atau langsung 'Disetujui'.
        // Kita simulasikan ACC Ketua dua kali (Atasan & Pejabat) untuk memastikan sampai Final.
        
        $this->actingAs($ketua)
             ->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        
        $cuti->refresh();

        // Jika sistem butuh 2x klik (Atasan -> Pejabat -> Final), kita klik sekali lagi
        if ($cuti->status == 'Menunggu Pejabat') {
            $this->actingAs($ketua)
                 ->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        }

        // HASIL AKHIR WAJIB DISETUJUI
        $this->assertDatabaseHas('cuti', [
            'id' => $cuti->id,
            'status' => 'Disetujui',
            'pejabat_berwenang' => $ketua->name // Pastikan kolom ini terisi
        ]);
    }
}
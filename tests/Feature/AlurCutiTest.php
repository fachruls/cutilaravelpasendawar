<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cuti;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AlurCutiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * SKENARIO 1: ALUR NORMAL (PEGAWAI BIASA)
     * Pegawai -> Kasubag (Verif) -> Atasan (Acc) -> Ketua (Final)
     */
    public function test_alur_normal_pegawai_biasa_sampai_final()
    {
        // 1. SETUP AKTOR
        $kasubag = User::factory()->create(['role' => 'kasubag', 'name' => 'Bu Kasubag']);
        $ketua   = User::factory()->create(['role' => 'pimpinan', 'name' => 'Pak Ketua']);
        
        // Atasan Biasa (Bukan Ketua)
        $atasan  = User::factory()->create(['role' => 'pegawai', 'name' => 'Pak Kabag', 'jabatan' => 'Kabag Umum']);
        
        // Pegawai Biasa (Bawahan Pak Kabag)
        $pegawai = User::factory()->create([
            'role' => 'pegawai', 
            'name' => 'Staf Biasa',
            'atasan_id' => $atasan->id // Atasannya Pak Kabag
        ]);

        // 2. PEGAWAI AJUKAN CUTI
        $this->actingAs($pegawai)->post(route('cuti.store'), [
            'jenis_cuti' => 'Cuti Tahunan',
            'alasan' => 'Liburan Normal',
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'atasan_langsung' => $atasan->name,
            'pejabat_berwenang' => $ketua->name,
            'alamat_selama_cuti' => 'Rumah',
            'no_hp' => '0812345',
            'ttd_pegawai' => 'data:image/png;base64,dummy_ttd',
        ]);

        $cuti = Cuti::where('user_id', $pegawai->id)->first();
        $this->assertEquals('Menunggu Verifikasi', $cuti->status); // Cek Masuk Kasubag

        // 3. KASUBAG VERIFIKASI
        $this->actingAs($kasubag)->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        $cuti->refresh();
        $this->assertEquals('Menunggu Atasan', $cuti->status); // Cek Masuk Atasan

        // 4. ATASAN BIASA ACC (Harus Lanjut ke Pejabat)
        $this->actingAs($atasan)->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        $cuti->refresh();
        $this->assertEquals('Menunggu Pejabat', $cuti->status); // Cek Masuk Ketua

        // 5. KETUA ACC FINAL
        $this->actingAs($ketua)->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        $cuti->refresh();
        $this->assertEquals('Disetujui', $cuti->status); // Cek Final
        $this->assertEquals($ketua->name, $cuti->pejabat_berwenang);
    }

    /**
     * SKENARIO 2: ALUR SPESIAL (PEGAWAI BAWAHAN KETUA)
     * Pegawai -> Kasubag (Verif) -> Ketua (Langsung Final)
     */
    public function test_alur_spesial_bawahan_ketua_langsung_final()
    {
        // 1. SETUP AKTOR
        $kasubag = User::factory()->create(['role' => 'kasubag', 'name' => 'Bu Kasubag']);
        $ketua   = User::factory()->create(['role' => 'pimpinan', 'name' => 'Pak Ketua']);
        
        // Pegawai Spesial (Contoh: Panitera) -> Atasannya langsung Ketua
        $panitera = User::factory()->create([
            'role' => 'pegawai', 
            'name' => 'Pak Panitera',
            'atasan_id' => $ketua->id // Atasannya Langsung Ketua
        ]);

        // 2. PANITERA AJUKAN CUTI
        $this->actingAs($panitera)->post(route('cuti.store'), [
            'jenis_cuti' => 'Cuti Tahunan',
            'alasan' => 'Cuti Penting',
            'tanggal_mulai' => now()->addDays(1)->format('Y-m-d'),
            'tanggal_selesai' => now()->addDays(3)->format('Y-m-d'),
            'atasan_langsung' => $ketua->name,
            'pejabat_berwenang' => $ketua->name,
            'alamat_selama_cuti' => 'Rumah',
            'no_hp' => '0812345',
            'ttd_pegawai' => 'data:image/png;base64,dummy_ttd',
        ]);

        $cuti = Cuti::where('user_id', $panitera->id)->first();
        $this->assertEquals('Menunggu Verifikasi', $cuti->status); // Cek Masuk Kasubag

        // 3. KASUBAG VERIFIKASI
        $this->actingAs($kasubag)->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        $cuti->refresh();
        $this->assertEquals('Menunggu Atasan', $cuti->status); // Cek Masuk Ketua (Sebagai Atasan)

        // 4. KETUA ACC (SEBAGAI ATASAN) -> HARUS LANGSUNG FINAL
        $this->actingAs($ketua)->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        $cuti->refresh();
        
        // --- INI POIN KRUSIALNYA ---
        // Status harus langsung 'Disetujui', BUKAN 'Menunggu Pejabat'
        $this->assertEquals('Disetujui', $cuti->status); 
        $this->assertEquals($ketua->name, $cuti->pejabat_berwenang);
    }
}
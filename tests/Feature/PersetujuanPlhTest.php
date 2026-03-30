<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Cuti;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PersetujuanPlhTest extends TestCase
{
    use RefreshDatabase;

    public function test_plh_bisa_menyetujui_sebagai_atasan()
    {
        // 1. Setup Aktor
        $atasan_asli = User::factory()->create(['role' => 'pegawai', 'name' => 'Atasan Asli']);
        $pegawai = User::factory()->create(['role' => 'pegawai', 'atasan_id' => $atasan_asli->id]);
        
        // 2. Setup PLH (Kasih mandat dari atasan_asli ke user_plh)
        $user_plh = User::factory()->create(['role' => 'pegawai', 'name' => 'User PLH']);
        $atasan_asli->update(['plh_id' => $user_plh->id]);

        // SETUP KETUA (Agara Notifikasi Pimpinan Terkirim)
        $ketua = User::factory()->create(['role' => 'pimpinan', 'name' => 'Bapak Ketua']);

        // 3. User lain tanpa akses
        $penyusup = User::factory()->create(['role' => 'pegawai', 'name' => 'Penyusup']);

        // 4. Bikin pengajuan cuti yang statusnya Menunggu Atasan
        $cuti = Cuti::create([
            'user_id' => $pegawai->id,
            'jenis_cuti' => 'Cuti Tahunan',
            'alasan' => 'Liburan',
            'tanggal_mulai' => now(), 'tanggal_selesai' => now(), 'lama' => 1,
            'status' => 'Menunggu Atasan',
            'atasan_langsung' => $atasan_asli->name,
            'alamat_selama_cuti' => '-', 'no_hp' => '-', 'tanggal_usulan' => now()
        ]);

        // 5. Tes Akses Ditolak untuk Penyusup
        $response_penyusup = $this->actingAs($penyusup)->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        $response_penyusup->assertStatus(403);
        $this->assertDatabaseHas('cuti', ['id' => $cuti->id, 'status' => 'Menunggu Atasan']);

        // 6. Tes Akses Berhasil untuk PLH
        // Walau role pegawainya biasa, tapi karena dia PLH nya Atasan Asli, wajib diterima.
        $response_plh = $this->actingAs($user_plh)->put(route('pimpinan.persetujuan.setuju', $cuti->id));
        
        // Harus sukses (302 redirect kembali)
        $response_plh->assertStatus(302);
        
        // Status harus berubah dan mencatat is_plh_atasan = true
        $this->assertDatabaseHas('cuti', [
            'id' => $cuti->id,
            'status' => 'Menunggu Pejabat',
            'is_plh_atasan' => 1
        ]);
        
        // Cek notifikasi tabel
        $this->assertDatabaseCount('notifications', 1);
    }
}

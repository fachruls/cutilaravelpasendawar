<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class UpdateSisaCuti extends Command
{
    // Nama perintah yang akan diketik di terminal
    protected $signature = 'cuti:update';
    
    // Penjelasan singkat fungsi perintah ini
    protected $description = 'Pembaruan sisa cuti tahunan sesuai aturan MA dan BKN';

    public function handle()
    {
        // Ambil semua pengguna yang berhak mendapat cuti
        $users = User::whereIn('role', ['pegawai', 'kasubag', 'pimpinan'])->get();

        foreach ($users as $user) {
            // Sisa tahun lalu N-1 digeser jadi N-2 maksimal 6 hari
            $n2_baru = min($user->cuti_n1, 6);
            
            // Sisa tahun ini N digeser jadi N-1 maksimal 6 hari
            $n1_baru = min($user->cuti_n, 6);
            
            // Jatah tahun baru N diisi ulang sesuai hak tahunan
            $n_baru = $user->hak_cuti_tahunan ?? 12;

            // Simpan pembaruan ke dalam tabel pengguna
            $user->update([
                'cuti_n2' => $n2_baru,
                'cuti_n1' => $n1_baru,
                'cuti_n' => $n_baru
            ]);
        }

        $this->info('Sisa cuti seluruh pegawai berhasil diperbarui otomatis');
    }
}
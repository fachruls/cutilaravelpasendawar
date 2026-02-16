<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Akun ADMIN
        User::create([
            'name' => 'Super Admin',
            'username' => 'admin',
            'nip' => '123456789',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'jabatan' => 'Administrator IT',
            'hak_cuti_tahunan' => 12
        ]);
        
        // 2. Buat Akun PIMPINAN (Ketua)
        User::create([
            'name' => 'Bapak Ketua',
            'username' => 'ketua',
            'nip' => '19800101',
            'email' => 'ketua@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'pimpinan',
            'jabatan' => 'Ketua Pengadilan',
            'hak_cuti_tahunan' => 12
        ]);

        // 3. PANGGIL SEEDER HARI LIBUR (Ini yang tadi kurang)
        $this->call(HariLiburSeeder::class);
    }
}
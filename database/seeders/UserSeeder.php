<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Akun ADMIN
        User::updateOrCreate(
            ['nip' => 'admin123'], // Cek: Jika NIP ini sudah ada, jangan buat baru (update saja)
            [
                'name' => 'Administrator Sistem',
                'email' => 'admin@pengadilan.go.id',
                'password' => Hash::make('password'), // Password default: 'password'
                'role' => 'admin',
                'jabatan' => 'Admin IT',
                'unit_kerja' => 'Bagian Umum'
            ]
        );

        // 2. Buat Akun PIMPINAN (Ketua Pengadilan)
        User::updateOrCreate(
            ['nip' => '19800101'],
            [
                'name' => 'Bapak Ketua Pengadilan',
                'email' => 'ketua@pengadilan.go.id',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
                'jabatan' => 'Ketua Pengadilan',
                'unit_kerja' => 'Pimpinan'
            ]
        );

        // 3. Buat Akun PEGAWAI BIASA
        User::updateOrCreate(
            ['nip' => '19900505'],
            [
                'name' => 'Andi Pegawai',
                'email' => 'andi@pengadilan.go.id',
                'password' => Hash::make('password'),
                'role' => 'pegawai',
                'jabatan' => 'Staf Panitera',
                'unit_kerja' => 'Kepaniteraan'
            ]
        );
    }
}
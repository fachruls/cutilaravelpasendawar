<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HariLiburSeeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel dulu agar tidak duplikat
        DB::table('hari_libur')->truncate();

        // Daftar Hari Libur 2026 (Contoh Prediksi)
        $data = [
            ['nama' => 'Tahun Baru 2026 Masehi', 'tanggal' => '2026-01-01'],
            ['nama' => 'Isra Mikraj Nabi Muhammad SAW', 'tanggal' => '2026-01-27'],
            ['nama' => 'Tahun Baru Imlek 2577 Kongzili', 'tanggal' => '2026-02-17'],
            ['nama' => 'Hari Suci Nyepi Tahun Baru Saka 1948', 'tanggal' => '2026-03-19'],
            ['nama' => 'Hari Raya Idul Fitri 1447 Hijriah', 'tanggal' => '2026-03-20'],
            ['nama' => 'Cuti Bersama Idul Fitri', 'tanggal' => '2026-03-23'], // Contoh Cuti Bersama
            ['nama' => 'Cuti Bersama Idul Fitri', 'tanggal' => '2026-03-24'],
            ['nama' => 'Wafat Isa Al Masih', 'tanggal' => '2026-04-03'],
            ['nama' => 'Hari Buruh Internasional', 'tanggal' => '2026-05-01'],
            ['nama' => 'Kenaikan Isa Al Masih', 'tanggal' => '2026-05-14'],
            ['nama' => 'Hari Raya Waisak 2570 BE', 'tanggal' => '2026-05-31'],
            ['nama' => 'Hari Raya Idul Adha 1447 Hijriah', 'tanggal' => '2026-05-27'],
            ['nama' => 'Tahun Baru Islam 1448 Hijriah', 'tanggal' => '2026-06-16'],
            ['nama' => 'Hari Kemerdekaan RI', 'tanggal' => '2026-08-17'],
            ['nama' => 'Maulid Nabi Muhammad SAW', 'tanggal' => '2026-08-25'],
            ['nama' => 'Hari Raya Natal', 'tanggal' => '2026-12-25'],
        ];

        // Masukkan ke database
        DB::table('hari_libur')->insert($data);
    }
}
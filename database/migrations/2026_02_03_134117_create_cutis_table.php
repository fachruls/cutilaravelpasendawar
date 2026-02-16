<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuti', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Data Utama
            $table->string('jenis_cuti');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->integer('lama');
            $table->text('alasan');
            
            // Detail Kontak (WAJIB ADA)
            $table->string('alamat_selama_cuti')->nullable();
            $table->string('no_hp')->nullable();
            
            // File & Tanda Tangan Pemohon (INI YANG KEMARIN HILANG)
            $table->string('file_surat')->nullable();
            $table->string('ttd_path')->nullable(); // Kolom TTD Pemohon
            
            // Status
            // Ganti enum jadi string (VARCHAR) dan set default baru
$table->string('status')->default('Menunggu Verifikasi');
            
            // Approval Atasan
            $table->string('atasan_langsung')->nullable();
            $table->string('ttd_atasan')->nullable(); // Kolom TTD Atasan
            $table->text('catatan_atasan')->nullable();
            
            // Approval Pejabat
            $table->string('pejabat_berwenang')->nullable();
            $table->string('ttd_pejabat')->nullable(); // Kolom TTD Pejabat
            $table->text('catatan_pejabat')->nullable();
            $table->text('catatan_pimpinan')->nullable();
            
            // Waktu
            $table->date('tanggal_usulan')->nullable();
            $table->dateTime('waktu_disetujui')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuti');
    }
};
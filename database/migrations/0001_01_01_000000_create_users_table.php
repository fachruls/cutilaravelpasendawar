<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->unique()->nullable();
            $table->string('nip')->unique()->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            
            // --- DATA PEGAWAI & JABATAN ---
            $table->string('role')->default('pegawai'); 
            $table->string('jabatan')->nullable();
            $table->string('golongan')->nullable();
            $table->string('unit_kerja')->nullable();
            
            // --- INI YANG TADI BIKIN ERROR (SEKARANG SUDAH ADA) ---
            $table->date('tmt_jabatan')->nullable(); 
            $table->date('tmt_masuk')->nullable();   
            // -----------------------------------------------------

            $table->string('no_hp')->nullable();
            $table->text('alamat')->nullable();
            
            // --- TANDA TANGAN & FOTO ---
            $table->string('ttd_path')->nullable(); 
            $table->string('foto_profil')->nullable();
            
            // --- ATASAN & CUTI ---
            $table->unsignedBigInteger('atasan_id')->nullable();
            $table->integer('hak_cuti_tahunan')->default(12);
            
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
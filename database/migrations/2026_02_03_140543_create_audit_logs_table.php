<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke User
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Kolom Data (Sekarang LENGKAP)
            $table->string('action'); // Contoh: LOGIN, LOGOUT
            $table->text('details')->nullable(); // Contoh: "Login berhasil"
            
            // INI YANG TADI BIKIN ERROR (Sekarang kita adakan kolomnya)
            $table->string('ip_address')->nullable(); // Mencatat IP
            $table->string('user_agent')->nullable(); // Mencatat Browser
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
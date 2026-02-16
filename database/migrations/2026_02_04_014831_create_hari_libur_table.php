<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari_libur', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // <--- PASTIKAN INI 'nama', BUKAN 'keterangan'
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari_libur');
    }
};
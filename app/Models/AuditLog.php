<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- Penting buat Test
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Kita pakai guarded kosong biar Laravel otomatis izinkan semua kolom (Anti Ribet)
    protected $guarded = [];

    // Relasi ke User (Tetap kita pertahankan biar bisa dipanggil $log->user->name)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
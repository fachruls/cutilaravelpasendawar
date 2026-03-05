<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory;

    // Menggunakan fillable sebagai daftar putih untuk mencegah celah mass assignment
    protected $fillable = [
        'user_id',
        'action',
        'details',
        'ip_address',
        'user_agent',
    ];

    // Relasi ke User tetap kita pertahankan agar fungsionalitas tidak rusak
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
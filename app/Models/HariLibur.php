<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    // Nama tabel di database
    protected $table = 'hari_libur';

    // Kolom yang boleh diisi (SESUAI DATABASE TERBARU)
    protected $fillable = ['tanggal', 'nama'];
    
    // Casting agar tanggal dibaca sebagai object Date
    protected $casts = [
        'tanggal' => 'date',
    ];
}
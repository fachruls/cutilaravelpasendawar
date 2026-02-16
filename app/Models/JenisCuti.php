<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisCuti extends Model
{
    use HasFactory;

    protected $table = 'jenis_cuti'; // Pastikan nama tabel benar

    protected $fillable = [
        'nama_cuti',
        'max_hari',
    ];
}
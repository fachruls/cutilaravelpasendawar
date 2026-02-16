<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;
    
    protected $table = 'cuti';

    // Pastikan semua kolom TTD ada disini
    protected $fillable = [
        'user_id',
        'jenis_cuti',
        'is_plh_atasan',  // <--- TAMBAHAN BARU
        'is_plh_pejabat',
        'alasan',
        'tanggal_mulai',
        'tanggal_selesai',
        'lama',
        'status',
        'alamat_selama_cuti',
        'no_hp',
        'file_surat',
        'ttd_path',          // <--- Wajib
        'atasan_langsung',
        'ttd_atasan',        // <--- Wajib
        'catatan_atasan',
        'pejabat_berwenang',
        'ttd_pejabat',       // <--- Wajib
        'catatan_pejabat',
        'catatan_pimpinan',
        'waktu_disetujui',
        'tanggal_usulan'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
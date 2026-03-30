<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'nip',
        'username',
        'role', // Role: 'admin', 'pimpinan', 'pegawai', 'kasubag'
        'jabatan',
        'unit_kerja',
        'golongan',
        'no_hp',
        'alamat',
        'hak_cuti_tahunan',

        // Data Baru (Wajib Ada sesuai Migrasi Final)
        'tmt_jabatan',    // Tanggal Mulai Jabatan
        'tmt_masuk',      // Tanggal Masuk Kerja
        'atasan_id',      // Relasi ke ID User lain (Pengganti atasan_langsung)
        'ttd_path',       // File Tanda Tangan
        'foto_profil',    // File Foto Profil
        
        'plh_id',         // <--- INI DIA! WAJIB ADA AGAR BISA DISIMPAN
        
        // [FITUR BARU: LOGIKA SISA CUTI]
        'cuti_n',   // Jatah Tahun Ini
        'cuti_n1',  // Sisa Tahun Lalu
        'cuti_n2',  // Sisa 2 Tahun Lalu
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'tmt_jabatan' => 'date', 
            'tmt_masuk' => 'date',   
        ];
    }

    // ==========================================
    // RELASI DATABASE
    // ==========================================

    public function atasan()
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }

    public function bawahan()
    {
        return $this->hasMany(User::class, 'atasan_id');
    }

    public function plh()
    {
        return $this->belongsTo(User::class, 'plh_id');
    }

    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }
}
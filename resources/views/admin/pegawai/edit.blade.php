@extends('layouts.app')

@section('content')
<div style="background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%); border-radius: 20px; padding: 24px 30px; color: white; position: relative; overflow: hidden; margin-bottom: 24px; box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);">
    <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
    <i class="fas fa-user-edit" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); font-size: 4rem; opacity: 0.1;"></i>
    <h4 class="fw-bold m-0" style="position: relative; z-index: 1;"><i class="fas fa-user-edit me-2"></i>Edit Data Pegawai</h4>
    <p class="m-0 mt-1" style="opacity: 0.85; font-size: 0.9rem; position: relative; z-index: 1;">Perbarui informasi dan konfigurasi pegawai.</p>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <h5 class="m-0 text-success">Form Edit Pegawai</h5>
        <span class="badge {{ $pegawai->role == 'pimpinan' ? 'bg-success' : 'bg-secondary' }}">
            {{ ucfirst($pegawai->role) }}
        </span>
    </div>
    
    <div class="card-body p-4">
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.pegawai.update', $pegawai->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" name="nama" value="{{ old('nama', $pegawai->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP</label>
                    <input type="number" class="form-control" name="nip" value="{{ old('nip', $pegawai->nip) }}" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Username Login</label>
                    <input type="text" class="form-control" name="username" value="{{ old('username', $pegawai->username) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role</label>
                    <select class="form-select" name="role" required>
                        <option value="pegawai" {{ $pegawai->role == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                        <option value="pimpinan" {{ $pegawai->role == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Jabatan</label>
                    <input type="text" class="form-control" name="jabatan" value="{{ old('jabatan', $pegawai->jabatan) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Golongan</label>
                    <input type="text" class="form-control" name="golongan" value="{{ old('golongan', $pegawai->golongan) }}" placeholder="Isi IV/a, IX, atau -">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">TMT Jabatan</label>
                    <input type="date" class="form-control" name="tmt_jabatan" value="{{ old('tmt_jabatan', $pegawai->tmt_jabatan) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label text-primary fw-bold">Tanggal Masuk (TMT)</label>
                    <input type="date" class="form-control border-primary" name="tmt_masuk" value="{{ old('tmt_masuk', $pegawai->tmt_masuk) }}">
                    <small class="text-muted fst-italic">Digunakan untuk hitung masa kerja otomatis.</small>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Atasan Langsung (Untuk Approval)</label>
                    <select name="atasan_id" class="form-select">
                        <option value="">-- Pilih Atasan (Jika Ada) --</option>
                        <option value="">Tidak Ada (Langsung ke Ketua)</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" {{ $pegawai->atasan_id == $u->id ? 'selected' : '' }}>
                                {{ $u->name }} - {{ $u->jabatan }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $pegawai->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">No. HP</label>
                    <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp', $pegawai->no_hp) }}">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <textarea class="form-control" name="alamat" rows="2">{{ old('alamat', $pegawai->alamat) }}</textarea>
            </div>

            <div class="card bg-warning bg-opacity-10 border-warning mb-4">
                <div class="card-header bg-warning text-dark fw-bold">
                    <i class="fas fa-wallet me-2"></i>Konfigurasi Saldo Cuti (N, N-1, N-2)
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Jatah Tahun Ini (N)</label>
                            <input type="number" class="form-control border-warning" name="cuti_n" value="{{ old('cuti_n', $pegawai->cuti_n) }}">
                            <div class="form-text">Saldo utama tahun berjalan.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Sisa Tahun Lalu (N-1)</label>
                            <input type="number" class="form-control border-warning" name="cuti_n1" value="{{ old('cuti_n1', $pegawai->cuti_n1) }}">
                            <div class="form-text">Akan dipotong duluan (Prioritas 2).</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small">Sisa 2 Thn Lalu (N-2)</label>
                            <input type="number" class="form-control border-warning" name="cuti_n2" value="{{ old('cuti_n2', $pegawai->cuti_n2) }}">
                            <div class="form-text">Wajib habis duluan (Prioritas 1).</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Hak Cuti Tahunan</label>
                    <input type="number" class="form-control" name="hak_cuti_tahunan" value="{{ old('hak_cuti_tahunan', $pegawai->hak_cuti_tahunan) }}" required min="0">
                </div>
                <div class="col-md-6">
                    <div class="alert alert-warning py-2 mb-2">
                        <i class="fas fa-lock me-1"></i> Ganti Password (Opsional)
                    </div>
                    <div class="input-group mb-2">
                        <input type="password" class="form-control" name="password_baru" placeholder="Password Baru">
                    </div>
                    <input type="password" class="form-control" name="password_konfirmasi" placeholder="Konfirmasi Password Baru">
                    <small class="text-muted fst-italic">*Kosongkan jika tidak ingin mengubah password.</small>
                </div>
            </div>

            <div class="d-flex justify-content-between border-top pt-4">
                <button type="button" class="btn btn-danger" onclick="if(confirm('Yakin ingin menghapus pegawai ini? Data cuti terkait akan hilang!')) document.getElementById('delete-form').submit();">
                    <i class="fas fa-trash me-1"></i> Hapus Pegawai
                </button>

                <div class="d-flex gap-2">
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times me-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </div>
        </form>

        <form id="delete-form" action="{{ route('admin.pegawai.destroy', $pegawai->id) }}" method="POST" class="d-none">
            @csrf
            @method('DELETE')
        </form>

    </div>
</div>
@endsection
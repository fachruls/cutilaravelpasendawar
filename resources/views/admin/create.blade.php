@extends('layouts.app')

@section('content')
<div class="greeting mt-3 mb-4">
    <h4><i class="fas fa-user-plus me-2"></i>Tambah Pegawai Baru</h4>
</div>

<div class="card">
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

        <form action="{{ route('admin.pegawai.store') }}" method="POST">
            @csrf
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="nama" value="{{ old('nama') }}" required placeholder="Contoh: Ahmad Fauzi, S.H.">
                </div>
                <div class="col-md-6">
                    <label class="form-label">NIP <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="nip" value="{{ old('nip') }}" required placeholder="198xxxxxxxxxxx">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Username Login <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="username" value="{{ old('username') }}" required placeholder="Username untuk login">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" name="password" required minlength="6" placeholder="Minimal 6 karakter">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Role (Hak Akses) <span class="text-danger">*</span></label>
                    <select class="form-select" name="role" required>
                        <option value="">-- Pilih Role --</option>
                        <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                        <option value="pimpinan" {{ old('role') == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="jabatan" value="{{ old('jabatan') }}" required placeholder="Contoh: Panitera Muda">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">TMT Jabatan <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="tmt_jabatan" value="{{ old('tmt_jabatan') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Golongan <span class="text-danger">*</span></label>
                    <select class="form-select" name="golongan" required>
                        <option value="">-- Pilih Golongan --</option>
                        @foreach(['I/a', 'I/b', 'I/c', 'I/d', 'II/a', 'II/b', 'II/c', 'II/d', 'III/a', 'III/b', 'III/c', 'III/d', 'IV/a', 'IV/b', 'IV/c', 'IV/d', 'IV/e'] as $gol)
                            <option value="{{ $gol }}" {{ old('golongan') == $gol ? 'selected' : '' }}>{{ $gol }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Email (Opsional)</label>
                    <input type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="email@contoh.com">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hak Cuti Tahunan (Hari) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="hak_cuti_tahunan" value="{{ old('hak_cuti_tahunan', 12) }}" required min="0">
                    <div class="form-text text-muted">Default: 12 hari/tahun</div>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('admin.pegawai.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i> Batal
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i> Simpan Data
                </button>
            </div>

        </form>
    </div>
</div>
@endsection
@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    
    <div class="d-flex align-items-center mb-4">
        <i class="fas fa-user-circle fa-2x me-3 text-success"></i>
        <div>
            <h3 class="m-0 fw-bold">Pengaturan Profil</h3>
            <p class="text-muted m-0">Kelola informasi akun, password, dan tanda tangan digital.</p>
        </div>
    </div>

    @if(session('status') === 'profile-updated' || session('status') === 'password-updated' || session('status') === 'tanda-tangan-updated')
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> Perubahan berhasil disimpan!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        
        <div class="col-lg-6 order-lg-2">
            <div class="card border-0 shadow-sm rounded-3 h-100 border-top-success" style="border-top: 4px solid #0f6b3d;">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-success"><i class="fas fa-signature me-2"></i>Spesimen Tanda Tangan Digital</h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-muted small mb-4">
                        Upload gambar tanda tangan Anda (Format <strong>.PNG</strong> dengan background transparan) agar bisa digunakan untuk menyetujui surat cuti secara otomatis.
                    </p>

                    <div class="text-center mb-4 p-3 bg-light rounded border border-dashed">
                        @if(Auth::user()->ttd_path && file_exists(storage_path('app/public/'.Auth::user()->ttd_path)))
                            <p class="small text-muted mb-2">Tanda Tangan Saat Ini:</p>
                            <img src="{{ asset('storage/' . Auth::user()->ttd_path) }}" alt="TTD Saya" style="height: 100px; object-fit: contain;">
                        @else
                            <div class="py-4 text-center">
                                <i class="fas fa-times-circle text-danger mb-2 fa-2x"></i>
                                <p class="text-danger fw-bold m-0">Belum ada tanda tangan!</p>
                                <small class="text-muted">Fitur persetujuan otomatis tidak akan berfungsi.</small>
                            </div>
                        @endif
                    </div>

                    <form method="post" action="{{ route('profile.upload_ttd') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Pilih File Tanda Tangan (PNG)</label>
                            <input type="file" name="ttd_image" class="form-control" accept=".png" required>
                            @error('ttd_image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success fw-bold">
                                <i class="fas fa-upload me-2"></i>Simpan Tanda Tangan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6 order-lg-1">
            
            <div class="card border-0 shadow-sm rounded-3 mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-id-card me-2"></i>Informasi Akun</h6>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Simpan Profil</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-dark"><i class="fas fa-key me-2"></i>Ganti Password</h6>
                </div>
                <div class="card-body p-4">
                    <form method="post" action="{{ route('password.update') }}">
                        @csrf
                        @method('put')

                        <div class="mb-3">
                            <label class="form-label">Password Lama</label>
                            <input type="password" name="current_password" class="form-control">
                            @error('current_password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password Baru</label>
                            <input type="password" name="password" class="form-control">
                            @error('password', 'updatePassword') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" name="password_confirmation" class="form-control">
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-dark">Ganti Password</button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
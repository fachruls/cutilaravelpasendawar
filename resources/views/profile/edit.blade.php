@extends('layouts.app')

@section('content')
<style>
    .profile-header {
        background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
        box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);
    }
    .profile-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .profile-header::after {
        content: '';
        position: absolute;
        bottom: -30px; right: 100px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .profile-header h3 { font-weight: 700; font-size: 1.4rem; margin-bottom: 4px; position: relative; z-index: 1; }
    .profile-header p { opacity: 0.85; margin: 0; font-size: 0.9rem; position: relative; z-index: 1; }
    .profile-header .header-icon {
        position: absolute; right: 30px; top: 50%; transform: translateY(-50%);
        font-size: 4rem; opacity: 0.1;
    }

    .profile-card {
        border: none; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        background: white; overflow: hidden;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
    }
    .profile-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .profile-card .card-header {
        background: white;
        border-bottom: 2px solid #f1f5f9;
        padding: 18px 24px;
    }
    .profile-card .card-header .header-icon-sm {
        width: 38px; height: 38px;
        border-radius: 10px;
        display: inline-flex; align-items: center; justify-content: center;
        color: white; font-size: 0.9rem; margin-right: 10px;
    }
    .profile-card .card-body { padding: 24px; }

    .form-control, .form-select {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 11px 14px;
        transition: all 0.25s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.1);
    }
    .form-label { font-weight: 600; color: #374151; font-size: 0.9rem; }

    .btn-primary-custom {
        background: linear-gradient(135deg, #107c41, #0a5c30);
        border: none; color: white; border-radius: 10px;
        padding: 10px 24px; font-weight: 600;
        transition: all 0.25s;
    }
    .btn-primary-custom:hover {
        background: linear-gradient(135deg, #0a5c30, #064020);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(16, 124, 65, 0.3);
        color: white;
    }

    .ttd-preview {
        border: 2px dashed #d1d5db;
        border-radius: 14px;
        background: #f9fafb;
        padding: 20px;
        text-align: center;
        transition: border-color 0.2s;
    }
    .ttd-preview:hover { border-color: #107c41; }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .animate-in { animation: fadeInUp 0.4s ease-out forwards; }
</style>

<div class="container-fluid px-4 py-2">

    {{-- HEADER --}}
    <div class="profile-header animate-in">
        <i class="fas fa-user-cog header-icon"></i>
        <h3><i class="fas fa-user-circle me-2"></i>Pengaturan Profil</h3>
        <p>Kelola informasi akun, password, dan tanda tangan digital Anda.</p>
    </div>

    @if(session('status') === 'profile-updated' || session('status') === 'password-updated' || session('status') === 'tanda-tangan-updated')
        <div class="alert alert-success alert-dismissible fade show mb-4 rounded-3 d-flex align-items-center" role="alert" style="border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-3 fs-4 text-success"></i>
            <div><strong>Berhasil!</strong> Perubahan berhasil disimpan!</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        
        {{-- TTD DIGITAL --}}
        <div class="col-lg-6 order-lg-2 animate-in" style="animation-delay: 0.1s;">
            <div class="profile-card h-100">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon-sm" style="background: linear-gradient(135deg, #107c41, #059669);">
                            <i class="fas fa-signature"></i>
                        </div>
                        <h6 class="m-0 fw-bold text-dark">Spesimen Tanda Tangan Digital</h6>
                    </div>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-4">
                        Upload gambar tanda tangan Anda (Format <strong>.PNG</strong> dengan background transparan) agar bisa digunakan untuk menyetujui surat cuti secara otomatis.
                    </p>

                    <div class="ttd-preview mb-4">
                        @if(Auth::user()->ttd_path && file_exists(storage_path('app/public/'.Auth::user()->ttd_path)))
                            <p class="small text-muted mb-2 fw-bold">Tanda Tangan Saat Ini:</p>
                            <img src="{{ asset('storage/' . Auth::user()->ttd_path) }}" alt="TTD Saya" style="height: 100px; object-fit: contain;">
                        @else
                            <div class="py-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-2"
                                     style="width: 50px; height: 50px; background: #fee2e2;">
                                    <i class="fas fa-times-circle text-danger fa-lg"></i>
                                </div>
                                <p class="text-danger fw-bold m-0">Belum ada tanda tangan!</p>
                                <small class="text-muted">Fitur persetujuan otomatis tidak akan berfungsi.</small>
                            </div>
                        @endif
                    </div>

                    <form method="post" action="{{ route('profile.upload_ttd') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Pilih File Tanda Tangan (PNG)</label>
                            <input type="file" name="ttd_image" class="form-control" accept=".png" required>
                            @error('ttd_image')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn-primary-custom">
                                <i class="fas fa-upload me-2"></i>Simpan Tanda Tangan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- INFORMASI AKUN + GANTI PASSWORD --}}
        <div class="col-lg-6 order-lg-1">
            
            {{-- INFORMASI AKUN --}}
            <div class="profile-card mb-4 animate-in" style="animation-delay: 0.05s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon-sm" style="background: linear-gradient(135deg, #3b82f6, #2563eb);">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <h6 class="m-0 fw-bold text-dark">Informasi Akun</h6>
                    </div>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.update') }}">
                        @csrf
                        @method('patch')

                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn-primary-custom">
                                <i class="fas fa-save me-1"></i> Simpan Profil
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- GANTI PASSWORD --}}
            <div class="profile-card animate-in" style="animation-delay: 0.15s;">
                <div class="card-header">
                    <div class="d-flex align-items-center">
                        <div class="header-icon-sm" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                            <i class="fas fa-key"></i>
                        </div>
                        <h6 class="m-0 fw-bold text-dark">Ganti Password</h6>
                    </div>
                </div>
                <div class="card-body">
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
                            <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold">
                                <i class="fas fa-lock me-1"></i> Ganti Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
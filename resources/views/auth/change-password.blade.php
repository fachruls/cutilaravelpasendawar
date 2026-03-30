@extends('layouts.app')

@section('content')
<style>
    .pw-header {
        background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
        box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);
    }
    .pw-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .pw-header h3 { font-weight: 700; font-size: 1.4rem; margin-bottom: 4px; position: relative; z-index: 1; }
    .pw-header p { opacity: 0.85; margin: 0; font-size: 0.9rem; position: relative; z-index: 1; }
    .pw-header .header-icon {
        position: absolute; right: 30px; top: 50%; transform: translateY(-50%);
        font-size: 4rem; opacity: 0.1;
    }

    .pw-card {
        border: none; border-radius: 16px;
        box-shadow: 0 2px 16px rgba(0,0,0,0.06);
        background: white; overflow: hidden;
        transition: transform 0.25s, box-shadow 0.25s;
    }
    .pw-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    .pw-card .card-header {
        background: white;
        border-bottom: 2px solid #f1f5f9;
        padding: 18px 24px;
    }
    .pw-card .card-body { padding: 24px 28px; }

    .form-control {
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        padding: 11px 14px;
        transition: all 0.25s;
    }
    .form-control:focus {
        border-color: #107c41;
        box-shadow: 0 0 0 3px rgba(16, 124, 65, 0.1);
    }

    .input-group-text {
        border-radius: 10px 0 0 10px;
        border: 1.5px solid #e2e8f0;
        border-right: none;
        background: #f8fafc;
    }
    .input-group .form-control { border-left: none; }
    .input-group .btn { border-radius: 0 10px 10px 0; }

    .btn-save {
        background: linear-gradient(135deg, #107c41, #0a5c30);
        border: none; color: white; border-radius: 10px;
        padding: 12px 30px; font-weight: 700;
        transition: all 0.25s;
    }
    .btn-save:hover {
        background: linear-gradient(135deg, #0a5c30, #064020);
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(16, 124, 65, 0.3);
        color: white;
    }

    @keyframes fadeInUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .animate-in { animation: fadeInUp 0.4s ease-out forwards; }
</style>

<div class="row justify-content-center">
    <div class="col-md-6 col-lg-5">

        {{-- HEADER --}}
        <div class="pw-header animate-in mt-2">
            <i class="fas fa-shield-alt header-icon"></i>
            <h3><i class="fas fa-lock me-2"></i>Ubah Password</h3>
            <p>Perbarui password akun Anda secara berkala untuk keamanan.</p>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4 d-flex align-items-center" role="alert" style="border-left: 4px solid #10b981;">
                <i class="fas fa-check-circle me-3 fs-4 text-success"></i>
                <div><strong>Berhasil!</strong> {{ session('success') }}</div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- MAIN CARD --}}
        <div class="pw-card animate-in" style="animation-delay: 0.1s;">
            <div class="card-header">
                <div class="d-flex align-items-center">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-3 me-3"
                         style="width: 38px; height: 38px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                        <i class="fas fa-key"></i>
                    </div>
                    <h6 class="m-0 fw-bold text-dark">Formulir Ubah Password</h6>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('password.change.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Password Lama</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key text-muted"></i></span>
                            <input type="password" name="password_lama" id="passLama" class="form-control @error('password_lama') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passLama', 'iconLama')">
                                <i class="fas fa-eye" id="iconLama"></i>
                            </button>
                            @error('password_lama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password_baru" id="passBaru" class="form-control @error('password_baru') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passBaru', 'iconBaru')">
                                <i class="fas fa-eye" id="iconBaru"></i>
                            </button>
                            @error('password_baru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text small"><i class="fas fa-info-circle me-1"></i> Minimal 6 karakter.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check-double text-muted"></i></span>
                            <input type="password" name="password_baru_confirmation" id="passKonfirm" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passKonfirm', 'iconKonfirm')">
                                <i class="fas fa-eye" id="iconKonfirm"></i>
                            </button>
                        </div>
                        <div class="form-text text-danger small"><i class="fas fa-exclamation-triangle me-1"></i> Harus SAMA PERSIS dengan Password Baru</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save me-2"></i>Simpan Password Baru
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-light border rounded-pill fw-medium">
                            <i class="fas fa-arrow-left me-1"></i> Kembali ke Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function togglePass(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("fa-eye");
            icon.classList.add("fa-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("fa-eye-slash");
            icon.classList.add("fa-eye");
        }
    }
</script>
@endsection
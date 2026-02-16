@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card mt-4 shadow-sm border-0">
            <div class="card-header text-white fw-bold" style="background: #0f6b3d;">
                <i class="fas fa-lock me-2"></i>Ubah Password
            </div>
            <div class="card-body p-4">
                
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.change.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Password Lama</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-key text-muted"></i></span>
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
                            <span class="input-group-text bg-light"><i class="fas fa-lock text-muted"></i></span>
                            <input type="password" name="password_baru" id="passBaru" class="form-control @error('password_baru') is-invalid @enderror" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passBaru', 'iconBaru')">
                                <i class="fas fa-eye" id="iconBaru"></i>
                            </button>
                            @error('password_baru')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-text small">Minimal 6 karakter.</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">Konfirmasi Password Baru</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="fas fa-check-double text-muted"></i></span>
                            <input type="password" name="password_baru_confirmation" id="passKonfirm" class="form-control" required>
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePass('passKonfirm', 'iconKonfirm')">
                                <i class="fas fa-eye" id="iconKonfirm"></i>
                            </button>
                        </div>
                        <div class="form-text text-danger small">* Harus SAMA PERSIS dengan Password Baru</div>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-success fw-bold" style="background: #0f6b3d;">
                            <i class="fas fa-save me-2"></i>Simpan Password Baru
                        </button>
                        <a href="{{ route('dashboard') }}" class="btn btn-light border">Batal</a>
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
@extends('layouts.app')

@section('content')
<div class="container pb-5">
    <div class="card border-0 shadow-sm mb-4" style="background: #0f6b3d; color: white; border-radius: 10px;">
        <div class="card-body d-flex align-items-center p-3">
            <i class="fas fa-user-plus fa-2x me-3"></i>
            <div>
                <h4 class="m-0 fw-bold">Tambah Pegawai Baru</h4>
                <p class="m-0 small opacity-75">Isi form berikut untuk mendaftarkan pegawai atau pimpinan</p>
            </div>
        </div>
    </div>

    @if ($errors->any())
    <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4">
        <div class="d-flex align-items-center mb-2">
            <i class="fas fa-exclamation-triangle me-2 fs-5"></i>
            <strong>Gagal Menambah Pegawai!</strong>
        </div>
        <ul class="mb-0 ps-3">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('admin.pegawai.store') }}" method="POST">
        @csrf
        
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-success"><i class="fas fa-id-card me-2"></i>Identitas Akun</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="nama" class="form-control" placeholder="Nama Pegawai" value="{{ old('nama') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">NIP <span class="text-danger">*</span></label>
                            <input type="number" name="nip" class="form-control" placeholder="Nomor Induk Pegawai" value="{{ old('nip') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Role / Hak Akses <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <option value="" selected disabled>-- Pilih Role --</option>
                                <option value="pegawai" {{ old('role') == 'pegawai' ? 'selected' : '' }}>Pegawai</option>
                                <option value="kasubag" {{ old('role') == 'kasubag' ? 'selected' : '' }}>Kasubag Kepegawaian</option>
                                <option value="pimpinan" {{ old('role') == 'pimpinan' ? 'selected' : '' }}>Pimpinan</option>
                            </select>
                            <div class="form-text text-muted small">
                                <i class="fas fa-info-circle"></i> <strong>Kasubag</strong> verifikasi awal. <strong>Pimpinan</strong> persetujuan akhir.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Username <span class="text-danger">*</span></label>
                            <input type="text" name="username" class="form-control" placeholder="Username Login" value="{{ old('username') }}" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Email</label>
                            <input type="email" name="email" class="form-control" placeholder="email@contoh.com" value="{{ old('email') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Password Login <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="fas fa-key"></i></span>
                                <input type="text" name="password" class="form-control" placeholder="Buat password..." required>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card border-0 shadow-sm h-100 rounded-3">
                    <div class="card-header bg-white py-3">
                        <h6 class="m-0 fw-bold text-success"><i class="fas fa-briefcase me-2"></i>Jabatan & Struktur</h6>
                    </div>
                    <div class="card-body p-4">
                        
                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Jabatan</label>
                                <input type="text" name="jabatan" class="form-control" placeholder="Contoh: Staff Umum" value="{{ old('jabatan') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Golongan</label>
                                <input type="text" name="golongan" class="form-control" placeholder="Isi IV/a, IX, atau -" value="{{ old('golongan') }}">
                                <small class="text-muted" style="font-size: 0.7rem;">* Ketik manual (Contoh: IV/a, IX, atau -)</small>
                            </div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">TMT Jabatan</label>
                                <input type="date" name="tmt_jabatan" class="form-control" value="{{ old('tmt_jabatan') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">Tanggal Masuk (TMT)</label>
                                <input type="date" name="tmt_masuk" class="form-control border-primary" value="{{ old('tmt_masuk') }}">
                                <small class="text-muted" style="font-size: 0.7rem;">*Untuk hitung masa kerja otomatis</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Atasan Langsung (Approval)</label>
                            <select name="atasan_id" class="form-select">
                                <option value="">-- Pilih Atasan (Jika Ada) --</option>
                                <option value="">Tidak Ada (Langsung ke Ketua)</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ old('atasan_id') == $u->id ? 'selected' : '' }}>
                                        {{ $u->name }} - {{ $u->jabatan }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Pilih pejabat yang akan menyetujui tahap pertama cuti pegawai ini.</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Jatah Cuti Tahunan <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="hak_cuti_tahunan" class="form-control" value="{{ old('hak_cuti_tahunan', 12) }}" required>
                                <span class="input-group-text">Hari</span>
                            </div>
                        </div>

                        <div class="row g-2 mb-3 bg-light p-2 rounded">
                            <div class="col-12"><small class="fw-bold text-success">Saldo Awal (Opsional - Default 12/0/0):</small></div>
                            <div class="col-md-4">
                                <input type="number" name="cuti_n" class="form-control form-control-sm" placeholder="Thn Ini (12)">
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="cuti_n1" class="form-control form-control-sm" placeholder="Sisa Lalu (0)">
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="cuti_n2" class="form-control form-control-sm" placeholder="Sisa 2Thn (0)">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">No. HP / WhatsApp</label>
                            <input type="text" name="no_hp" class="form-control" value="{{ old('no_hp') }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Alamat Lengkap</label>
                            <textarea name="alamat" class="form-control" rows="2">{{ old('alamat') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mt-4 gap-2">
            <a href="{{ route('admin.pegawai.index') }}" class="btn btn-light border px-4">Batal</a>
            <button type="submit" class="btn btn-success px-5 fw-bold" style="background: #0f6b3d; border-color: #0f6b3d;">
                <i class="fas fa-save me-2"></i>Simpan Data
            </button>
        </div>
    </form>
</div>
@endsection
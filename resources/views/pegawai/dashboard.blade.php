@extends('layouts.app')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold text-dark mb-1">Dashboard Pegawai</h4>
        <p class="text-muted mb-0">Selamat datang kembali, <span class="text-primary fw-semibold">{{ Auth::user()->name }}</span>!</p>
    </div>
    <div class="d-none d-md-block">
        <span class="badge bg-white text-secondary shadow-sm py-2 px-3 border">
            <i class="fas fa-calendar-alt me-2 text-primary"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
        </span>
    </div>
</div>

@php
    // LOGIKA PHP: Cek Atasan & Plh
    $is_atasan = \App\Models\User::where('atasan_id', Auth::id())->exists();
    
    // Ambil pegawai lain untuk opsi Plh
    $pegawai_lain = \App\Models\User::where('id', '!=', Auth::id())
                                    ->orderBy('name')
                                    ->get(['id', 'name']);
@endphp

{{-- ========================================================== --}}
{{-- 1. SECTION DELEGASI WEWENANG (Hanya muncul jika Atasan) --}}
{{-- ========================================================== --}}
@if($is_atasan)
<div class="card border-0 mb-4 overflow-hidden position-relative">
    {{-- Hiasan Background (Absrak) --}}
    <div style="position: absolute; top: 0; right: 0; width: 150px; height: 100%; background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(16, 124, 65, 0.1) 100%);"></div>
    
    <div class="card-body p-4">
        <div class="row align-items-center g-3">
            <div class="col-md-7">
                <div class="d-flex align-items-start">
                    <div class="me-3 mt-1">
                        <div class="d-flex align-items-center justify-content-center rounded-circle" 
                             style="width: 50px; height: 50px; background-color: #e6f4ea; color: #107c41;">
                            <i class="fas fa-user-shield fa-lg"></i>
                        </div>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Delegasi Wewenang (Plh)</h5>
                        <p class="text-muted small mb-2">
                            Kelola pelimpahan tugas persetujuan cuti kepada pegawai lain saat Anda berhalangan hadir.
                        </p>
                        
                        @if(Auth::user()->plh_id)
                            <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <small class="fw-bold">Status: DIALIHKAN ke {{ \App\Models\User::find(Auth::user()->plh_id)->name ?? '-' }}</small>
                            </div>
                        @else
                            <div class="d-inline-flex align-items-center px-3 py-1 rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                                <i class="fas fa-check-circle me-2"></i>
                                <small class="fw-bold">Status: ANDA AKTIF</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-5">
                <div class="bg-white p-3 rounded-3 shadow-sm border">
                    <form action="{{ route('plh.update') }}" method="POST">
                        @csrf
                        <label class="small text-muted fw-bold mb-2">Atur Pelaksana Harian (Plh):</label>
                        <div class="input-group">
                            <select name="plh_id" class="form-select form-select-sm border-secondary-subtle">
                                <option value="">-- Saya Aktif Kembali (Hapus Plh) --</option>
                                @foreach($pegawai_lain as $pg)
                                    <option value="{{ $pg->id }}" {{ Auth::user()->plh_id == $pg->id ? 'selected' : '' }}>
                                        Limpahkan ke: {{ $pg->name }}
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-sm btn-primary px-3">
                                <i class="fas fa-save me-1"></i> Simpan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- ========================================================== --}}
{{-- 2. STATISTIC CARDS (Modern Gradient Style) --}}
{{-- ========================================================== --}}
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card h-100 text-white border-0" 
             style="background: linear-gradient(135deg, #107c41 0%, #15803d 100%); position: relative; overflow: hidden;">
             <i class="fas fa-calendar-check position-absolute" style="font-size: 5rem; top: 10px; right: -20px; opacity: 0.15;"></i>
            
            <div class="card-body position-relative z-1 p-4">
                <h6 class="text-uppercase mb-2 text-white-50 small fw-bold">Hak Cuti Tahunan {{ $active_year }}</h6>
                <div class="d-flex align-items-baseline">
                    <h2 class="display-5 fw-bold mb-0 me-2">{{ $hak_cuti }}</h2>
                    <span class="fs-6 opacity-75">Hari</span>
                </div>
                <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                    <small><i class="fas fa-info-circle me-1"></i> Total kuota tahunan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card h-100 text-white border-0" 
             style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); position: relative; overflow: hidden;">
            <i class="fas fa-file-invoice position-absolute" style="font-size: 5rem; top: 10px; right: -20px; opacity: 0.15;"></i>
            
            <div class="card-body position-relative z-1 p-4">
                <h6 class="text-uppercase mb-2 text-white-50 small fw-bold">Sudah Dipakai</h6>
                <div class="d-flex align-items-baseline">
                    <h2 class="display-5 fw-bold mb-0 me-2">{{ $terpakai }}</h2>
                    <span class="fs-6 opacity-75">Hari</span>
                </div>
                <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                    <small><i class="fas fa-chart-line me-1"></i> Cuti disetujui</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card h-100 text-white border-0" 
             style="background: linear-gradient(135deg, #d97706 0%, #b45309 100%); position: relative; overflow: hidden;">
            <i class="fas fa-hourglass-half position-absolute" style="font-size: 5rem; top: 10px; right: -20px; opacity: 0.15;"></i>
            
            <div class="card-body position-relative z-1 p-4">
                <h6 class="text-uppercase mb-2 text-white-50 small fw-bold">Sisa Kuota</h6>
                <div class="d-flex align-items-baseline">
                    <h2 class="display-5 fw-bold mb-0 me-2">{{ $sisa_cuti }}</h2>
                    <span class="fs-6 opacity-75">Hari</span>
                </div>
                <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                    <small><i class="fas fa-wallet me-1"></i> Dapat digunakan</small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 col-lg-3">
        <div class="card h-100 text-white border-0" 
             style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); position: relative; overflow: hidden;">
            <i class="fas fa-clock position-absolute" style="font-size: 5rem; top: 10px; right: -20px; opacity: 0.15;"></i>
            
            <div class="card-body position-relative z-1 p-4">
                <h6 class="text-uppercase mb-2 text-white-50 small fw-bold">Dalam Proses</h6>
                <div class="d-flex align-items-baseline">
                    <h2 class="display-5 fw-bold mb-0 me-2">{{ $menunggu }}</h2>
                    <span class="fs-6 opacity-75">Ajuan</span>
                </div>
                <div class="mt-3 pt-3 border-top border-white border-opacity-25">
                    <small><i class="fas fa-sync-alt me-1"></i> Menunggu persetujuan</small>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ========================================================== --}}
{{-- 3. BOTTOM SECTION (Pengumuman & Tabel) --}}
{{-- ========================================================== --}}
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom-0">
                <h6 class="fw-bold m-0 text-dark"><i class="fas fa-bullhorn me-2 text-primary"></i>Informasi Penting</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item px-4 py-3 border-light">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded p-2">
                                    <i class="fas fa-file-contract"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Aturan Pengajuan</small>
                                <div class="text-dark small mt-1">Pengajuan cuti tahunan minimal dilakukan <strong>3 hari kerja</strong> sebelum tanggal pelaksanaan.</div>
                            </div>
                        </div>
                    </li>
                    <li class="list-group-item px-4 py-3 border-light">
                        <div class="d-flex">
                            <div class="flex-shrink-0 me-3">
                                <div class="bg-warning bg-opacity-10 text-warning rounded p-2">
                                    <i class="fas fa-phone-alt"></i>
                                </div>
                            </div>
                            <div>
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 0.7rem;">Kontak</small>
                                <div class="text-dark small mt-1">Pastikan Nomor HP yang terdaftar selalu aktif selama masa cuti berlangsung.</div>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="p-4 bg-light rounded-bottom text-center">
                    <small class="text-muted">Butuh bantuan teknis? Hubungi <a href="#" class="text-decoration-none fw-bold">Admin</a></small>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h6 class="fw-bold m-0 text-dark"><i class="fas fa-calendar-day me-2 text-primary"></i>Cuti Mendatang</h6>
                <a href="{{ route('cuti.index') }}" class="btn btn-sm btn-outline-primary px-3 rounded-pill" style="font-size: 0.75rem;">
                    Lihat Semua
                </a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3 border-0">Tanggal</th>
                                <th class="py-3 border-0">Jenis Cuti</th>
                                <th class="pe-4 py-3 border-0 text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cuti_mendatang as $cm)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <i class="far fa-calendar text-muted me-2"></i>
                                    {{ \Carbon\Carbon::parse($cm->tanggal_mulai)->format('d/m/Y') }}
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border fw-normal">
                                        {{ $cm->jenis_cuti }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">
                                        <i class="fas fa-check me-1"></i> Disetujui
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="mb-3 text-muted opacity-25">
                                            <i class="fas fa-calendar-times fa-3x"></i>
                                        </div>
                                        <h6 class="text-muted small fw-bold mb-1">Tidak ada jadwal cuti</h6>
                                        <p class="text-muted small mb-0 opacity-75">Anda belum mengajukan cuti untuk waktu dekat.</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
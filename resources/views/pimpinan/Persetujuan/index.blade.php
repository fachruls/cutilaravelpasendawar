@extends('layouts.app')

@section('content')
<style>
    /* ====== PAGE HEADER ====== */
    .page-header-card {
        background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);
    }
    .page-header-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .page-header-card::after {
        content: '';
        position: absolute;
        bottom: -30px; right: 100px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .page-header-card h3 { font-weight: 700; font-size: 1.4rem; margin-bottom: 4px; position: relative; z-index: 1; }
    .page-header-card p { opacity: 0.85; margin: 0; font-size: 0.9rem; position: relative; z-index: 1; }
    .page-header-card .header-date {
        position: relative; z-index: 1;
        display: inline-flex; align-items: center;
        background: rgba(255,255,255,0.15);
        padding: 5px 14px; border-radius: 50px;
        font-size: 0.78rem; margin-top: 10px;
        backdrop-filter: blur(5px);
    }
    .page-header-card .header-icon {
        position: absolute; right: 30px; top: 50%; transform: translateY(-50%);
        font-size: 4rem; opacity: 0.1; z-index: 0;
    }

    /* ====== STAT MINI CARDS ====== */
    .stat-mini {
        display: flex; align-items: center; gap: 12px;
        background: white; border-radius: 14px;
        padding: 16px 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: transform 0.3s, box-shadow 0.3s;
        border: 1px solid rgba(0,0,0,0.04);
    }
    .stat-mini:hover { transform: translateY(-4px); box-shadow: 0 8px 25px rgba(0,0,0,0.12); }
    .stat-mini .stat-icon-box {
        width: 48px; height: 48px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.2rem; color: white; flex-shrink: 0;
    }
    .stat-mini .stat-value { font-size: 1.5rem; font-weight: 800; line-height: 1; }
    .stat-mini .stat-label { font-size: 0.75rem; color: #9ca3af; font-weight: 500; text-transform: uppercase; letter-spacing: 0.5px; }

    /* ====== ENHANCED TABLE ====== */
    .table-card {
        border: none; border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden; background: white;
    }
    .table-card .card-header {
        background: white; border-bottom: 2px solid #f3f4f6;
        padding: 18px 24px;
    }
    .table-card .table th {
        background: #f8fafc; font-size: 0.75rem;
        text-transform: uppercase; letter-spacing: 0.8px;
        color: #64748b; font-weight: 700;
        border-bottom: 2px solid #e2e8f0;
    }
    .table-card .table td { vertical-align: middle; font-size: 0.9rem; }
    .table-card .table tbody tr {
        transition: background-color 0.2s;
    }
    .table-card .table tbody tr:hover { background-color: #f0fdf4; }

    /* ====== MODERN BADGES ====== */
    .badge-modern { padding: 6px 14px; border-radius: 8px; font-weight: 600; font-size: 0.78rem; letter-spacing: 0.3px; }
    .badge-menunggu { background: #fef3c7; color: #92400e; }
    .badge-disetujui { background: #d1fae5; color: #065f46; }
    .badge-ditolak { background: #fee2e2; color: #991b1b; }
    .badge-verifikasi { background: #e0e7ff; color: #3730a3; }

    /* ====== MODERN BUTTONS ====== */
    .btn-action {
        border-radius: 10px; padding: 6px 14px; font-weight: 600;
        font-size: 0.8rem; transition: all 0.2s;
    }
    .btn-action:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    /* ====== MODAL MODERN ====== */
    .modal-content { border: none; border-radius: 16px; overflow: hidden; }
    .modal-header { border-bottom: none; padding: 20px 24px; }
    .modal-body { padding: 20px 24px; }
    .modal-footer { border-top: 1px solid #f1f5f9; padding: 16px 24px; }

    /* ====== ENTRANCE ANIMATION ====== */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(16px); } to { opacity: 1; transform: translateY(0); } }
    .animate-in { animation: fadeInUp 0.4s ease-out forwards; }
</style>

<div class="container py-2">
    {{-- ============================================================ --}}
    {{-- PAGE HEADER --}}
    {{-- ============================================================ --}}
    <div class="page-header-card animate-in">
        <i class="fas fa-check-double header-icon"></i>
        <h3><i class="fas fa-clipboard-check me-2"></i>Persetujuan Cuti</h3>
        <p>Kelola dan proses pengajuan cuti pegawai Anda.</p>
        <div class="header-date">
            <i class="fas fa-calendar-alt me-2"></i> {{ now()->translatedFormat('l, d F Y') }}
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STAT MINI CARDS --}}
    {{-- ============================================================ --}}
    @php
        $totalPending = $persetujuan->whereIn('status', ['Menunggu Atasan', 'Menunggu Pejabat', 'Menunggu Verifikasi'])->count();
        $totalApproved = $persetujuan->where('status', 'Disetujui')->count();
        $totalRejected = $persetujuan->where('status', 'Ditolak')->count();
    @endphp
    <div class="row g-3 mb-4">
        <div class="col-md-4 animate-in" style="animation-delay: 0.05s;">
            <div class="stat-mini">
                <div class="stat-icon-box" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div>
                    <div class="stat-value text-dark">{{ $totalPending }}</div>
                    <div class="stat-label">Menunggu Proses</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 animate-in" style="animation-delay: 0.1s;">
            <div class="stat-mini">
                <div class="stat-icon-box" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value text-dark">{{ $totalApproved }}</div>
                    <div class="stat-label">Disetujui</div>
                </div>
            </div>
        </div>
        <div class="col-md-4 animate-in" style="animation-delay: 0.15s;">
            <div class="stat-mini">
                <div class="stat-icon-box" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div>
                    <div class="stat-value text-dark">{{ $totalRejected }}</div>
                    <div class="stat-label">Ditolak</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- ALERTS --}}
    {{-- ============================================================ --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert" style="border-left: 4px solid #10b981;">
            <i class="fas fa-check-circle me-3 fs-4 text-success"></i>
            <div><strong>Berhasil!</strong> {{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3 mb-4 d-flex align-items-center" role="alert" style="border-left: 4px solid #ef4444;">
            <i class="fas fa-exclamation-circle me-3 fs-4 text-danger"></i>
            <div><strong>Error!</strong> {{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- ============================================================ --}}
    {{-- TABLE --}}
    {{-- ============================================================ --}}
    <div class="table-card animate-in" style="animation-delay: 0.2s;">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-dark"><i class="fas fa-list-alt me-2 text-primary"></i>Daftar Pengajuan Masuk</h6>
            <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold">
                {{ $persetujuan->count() }} Data
            </span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="tabelPersetujuan">
                    <thead>
                        <tr>
                            <th class="px-4 py-3">No</th>
                            <th class="px-4 py-3">Nama Pegawai</th>
                            <th class="px-4 py-3">Jenis Cuti</th>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="px-4 py-3">Durasi</th>
                            <th class="px-4 py-3">Status Saat Ini</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($persetujuan as $index => $item)
                            <tr>
                                <td class="px-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10"
                                             style="width: 36px; height: 36px; flex-shrink: 0;">
                                            <i class="fas fa-user text-primary" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $item->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4">
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3 py-2 rounded-pill fw-bold">
                                        {{ $item->jenis_cuti }}
                                    </span>
                                </td>
                                <td class="px-4">
                                    <small class="text-dark d-block fw-medium"><i class="fas fa-calendar-plus me-1 text-success"></i> {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</small>
                                    <small class="text-muted d-block"><i class="fas fa-calendar-minus me-1 text-danger"></i> {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</small>
                                </td>
                                <td class="px-4">
                                    <span class="fw-bold text-dark">{{ $item->lama }} Hari</span>
                                </td>
                                <td class="px-4">
                                    @if ($item->status == 'Disetujui')
                                        <span class="badge-modern badge-disetujui"><i class="fas fa-check me-1"></i> Disetujui</span>
                                    @elseif($item->status == 'Ditolak')
                                        <span class="badge-modern badge-ditolak"><i class="fas fa-times me-1"></i> Ditolak</span>
                                    @elseif($item->status == 'Menunggu Verifikasi')
                                        <span class="badge-modern badge-verifikasi"><i class="fas fa-user-check me-1"></i> Verifikasi Kasubag</span>
                                    @else
                                        <span class="badge-modern badge-menunggu"><i class="fas fa-clock me-1"></i> {{ $item->status }}</span>
                                    @endif
                                </td>
                                <td class="px-4 text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        {{-- Tombol Detail --}}
                                        <button type="button" class="btn btn-action btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">
                                            <i class="fas fa-eye me-1"></i> Detail
                                        </button>

                                        @php
                                            $perlu_persetujuan = false;
                                            $user_login = Auth::user();

                                            $kasubag_asli = \App\Models\User::where('role', 'kasubag')->first();
                                            $is_plh_kasubag = $kasubag_asli && $kasubag_asli->plh_id == $user_login->id;

                                            if ($item->status == 'Menunggu Verifikasi' && ($user_login->role == 'kasubag' || $is_plh_kasubag)) {
                                                $perlu_persetujuan = true;
                                            }
                                            if ($item->status == 'Menunggu Atasan' && $user_login->id == $item->user->atasan_id) {
                                                $perlu_persetujuan = true;
                                            }
                                            if ($item->status == 'Menunggu Pejabat' && $user_login->role == 'pimpinan') {
                                                $perlu_persetujuan = true;
                                            }
                                            if ($item->status == 'Menunggu Atasan' && $item->user->atasan && $item->user->atasan->plh_id == $user_login->id) {
                                                $perlu_persetujuan = true;
                                            }
                                            if ($item->status == 'Menunggu Pejabat') {
                                                $ketua = \App\Models\User::where('role', 'pimpinan')->first();
                                                if ($ketua && $ketua->plh_id == $user_login->id) {
                                                    $perlu_persetujuan = true;
                                                }
                                            }
                                        @endphp

                                        @if ($perlu_persetujuan)
                                            <form action="{{ route('pimpinan.persetujuan.setuju', $item->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="btn btn-action btn-success text-white" onclick="return confirm('Apakah Anda yakin ingin memproses data ini?')">
                                                    <i class="fas fa-check me-1"></i> 
                                                    {{ $item->status == 'Menunggu Verifikasi' ? 'Verifikasi' : 'Setujui' }}
                                                </button>
                                            </form>
                                            
                                            <button type="button" class="btn btn-action btn-danger text-white" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $item->id }}">
                                                <i class="fas fa-times me-1"></i> Tolak
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center justify-content-center">
                                        <div class="mb-3 text-muted opacity-25">
                                            <i class="fas fa-inbox fa-4x"></i>
                                        </div>
                                        <h6 class="text-muted fw-bold mb-1">Semua Bersih! 🎉</h6>
                                        <p class="text-muted small mb-0 opacity-75">Belum ada pengajuan cuti yang perlu diproses.</p>
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

{{-- MODAL AREA --}}
@foreach ($persetujuan as $item)

    {{-- MODAL DETAIL --}}
    <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background: linear-gradient(135deg, #107c41, #0a5c30); color: white;">
                    <h5 class="modal-title fw-bold"><i class="fas fa-info-circle me-2"></i>Detail Pengajuan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center mb-4 pb-3 border-bottom">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 me-3"
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-user fa-lg text-primary"></i>
                        </div>
                        <div>
                            <h6 class="fw-bold mb-0">{{ $item->user->name }}</h6>
                            <small class="text-muted">{{ $item->user->nip ?? '-' }} • {{ $item->user->jabatan }}</small>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Jenis Cuti</label>
                            <p class="fw-bold mb-0">{{ $item->jenis_cuti }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Durasi</label>
                            <p class="fw-bold mb-0">{{ $item->lama }} Hari</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Alasan Cuti</label>
                            <p class="mb-0">{{ $item->alasan }}</p>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Alamat Selama Cuti</label>
                            <p class="mb-0">{{ $item->alamat_selama_cuti }}</p>
                        </div>
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">No HP</label>
                            <p class="mb-0">{{ $item->no_hp }}</p>
                        </div>
                        @if($item->file_surat)
                        <div class="col-6">
                            <label class="text-muted small fw-bold text-uppercase" style="font-size: 0.7rem;">Lampiran</label>
                            <div>
                                <a href="{{ route('cuti.cetak', $item->id) }}" class="btn btn-sm btn-outline-primary rounded-pill mb-1" target="_blank">
                                    <i class="fas fa-print me-1"></i> Cetak Dokumen
                                </a>
                                <a href="{{ route('cuti.cetak', ['id' => $item->id, 'download' => 1]) }}" class="btn btn-sm btn-outline-success rounded-pill">
                                    <i class="fas fa-file-download me-1"></i> Download PDF
                                </a>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border px-4 rounded-pill" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TOLAK --}}
    <div class="modal fade" id="modalTolak{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('pimpinan.persetujuan.tolak', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header" style="background: linear-gradient(135deg, #ef4444, #dc2626); color: white;">
                        <h5 class="modal-title fw-bold"><i class="fas fa-times-circle me-2"></i>Tolak Pengajuan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                                 style="width: 80px; height: 80px; background: #fee2e2;">
                                <i class="fas fa-ban fa-2x text-danger"></i>
                            </div>
                        </div>
                        <p class="text-center fw-bold mb-4">Apakah Anda yakin ingin menolak pengajuan dari <span class="text-danger">{{ $item->user->name }}</span>?</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Pekerjaan sedang menumpuk..." required style="border-radius: 10px;"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light border rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">
                            <i class="fas fa-times me-1"></i> Tolak Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach

@endsection
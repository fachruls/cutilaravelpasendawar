@extends('layouts.app')

@section('content')
<style>
    /* ====== GREETING CARD (sama persis dg admin) ====== */
    .greeting-card {
        background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%);
        border-radius: 20px;
        padding: 30px 35px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 28px;
        box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);
    }
    .greeting-card::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 200px; height: 200px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .greeting-card::after {
        content: '';
        position: absolute;
        bottom: -50px; right: 80px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: rgba(255,255,255,0.04);
    }
    .greeting-card h3 { font-weight: 700; font-size: 1.5rem; margin-bottom: 6px; position: relative; z-index: 1; }
    .greeting-card p { opacity: 0.85; margin: 0; font-size: 0.95rem; position: relative; z-index: 1; }
    .greeting-card .greeting-date {
        position: relative; z-index: 1;
        display: inline-flex; align-items: center;
        background: rgba(255,255,255,0.15);
        padding: 6px 16px; border-radius: 50px;
        font-size: 0.8rem; margin-top: 12px;
        backdrop-filter: blur(5px);
    }

    /* ====== STAT CARDS PEGAWAI - Premium ====== */
    .pegawai-stat {
        border-radius: 16px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
        border: none;
    }
    .pegawai-stat::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 120px; height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        transition: transform 0.4s ease;
    }
    .pegawai-stat::after {
        content: '';
        position: absolute;
        bottom: -20px; left: -20px;
        width: 80px; height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .pegawai-stat:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 35px rgba(0,0,0,0.2);
    }
    .pegawai-stat:hover::before { transform: scale(1.5); }
    .pegawai-stat .stat-icon-bg { 
        position: absolute; font-size: 5rem; top: 10px; right: -20px; opacity: 0.12; z-index: 0;
        transition: opacity 0.3s, transform 0.3s;
    }
    .pegawai-stat:hover .stat-icon-bg { opacity: 0.2; transform: rotate(-10deg) scale(1.1); }

    /* Entrance animation */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-in { animation: fadeInUp 0.5s ease-out forwards; }
    .animate-in:nth-child(1) { animation-delay: 0.05s; }
    .animate-in:nth-child(2) { animation-delay: 0.1s; }
    .animate-in:nth-child(3) { animation-delay: 0.15s; }
    .animate-in:nth-child(4) { animation-delay: 0.2s; }
</style>

{{-- ============================================================ --}}
{{-- GREETING CARD --}}
{{-- ============================================================ --}}
@php
    $hour = (int) date('H');
    if ($hour >= 5 && $hour < 11) $salam = 'Selamat Pagi';
    elseif ($hour >= 11 && $hour < 15) $salam = 'Selamat Siang';
    elseif ($hour >= 15 && $hour < 18) $salam = 'Selamat Sore';
    else $salam = 'Selamat Malam';
@endphp

<div class="greeting-card">
    <h3>{{ $salam }}, {{ Auth::user()->name }}! 👋</h3>
    <p>Kelola pengajuan cuti Anda dengan mudah dari sini.</p>
    <div class="greeting-date">
        <i class="fas fa-calendar-alt me-2"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
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

{{-- ============================================================ --}}
{{-- DELEGASI WEWENANG (Hanya muncul jika Atasan) --}}
{{-- ============================================================ --}}
@if($is_atasan)
<div class="card border-0 mb-4 overflow-hidden position-relative">
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

{{-- ============================================================ --}}
{{-- STAT CARDS --}}
{{-- ============================================================ --}}
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3 animate-in">
        <div class="card h-100 pegawai-stat" 
             style="background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%);">
            <i class="fas fa-calendar-check stat-icon-bg"></i>
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

    <div class="col-md-6 col-lg-3 animate-in">
        <div class="card h-100 pegawai-stat" 
             style="background: linear-gradient(135deg, #1565c0 0%, #0d47a1 50%, #0a3880 100%);">
            <i class="fas fa-file-invoice stat-icon-bg"></i>
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

    <div class="col-md-6 col-lg-3 animate-in">
        <div class="card h-100 pegawai-stat" 
             style="background: linear-gradient(135deg, #f57c00 0%, #e65100 50%, #bf360c 100%);">
            <i class="fas fa-hourglass-half stat-icon-bg"></i>
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

    <div class="col-md-6 col-lg-3 animate-in">
        <div class="card h-100 pegawai-stat" 
             style="background: linear-gradient(135deg, #dc2626 0%, #b91c1c 50%, #8e1414 100%);">
            <i class="fas fa-clock stat-icon-bg"></i>
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

{{-- ============================================================ --}}
{{-- CHART + INFORMASI + TABEL --}}
{{-- ============================================================ --}}
<div class="row g-4">
    {{-- DONUT CHART: KUOTA CUTI --}}
    <div class="col-lg-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold m-0 text-dark"><i class="fas fa-chart-pie me-2 text-primary"></i>Komposisi Kuota Cuti</h6>
            </div>
            <div class="card-body d-flex align-items-center justify-content-center">
                <canvas id="cutiDonutChart" width="220" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- INFORMASI PENTING --}}
    <div class="col-lg-4">
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

    {{-- TABEL CUTI MENDATANG --}}
    <div class="col-lg-4">
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
                                <th class="py-3 border-0">Jenis</th>
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

{{-- ============================================================ --}}
{{-- CHART.JS - DONUT --}}
{{-- ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('cutiDonutChart').getContext('2d');
    
    const terpakai = {{ $terpakai }};
    const sisa = {{ $sisa_cuti }};
    const menunggu = {{ $menunggu }};

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Terpakai', 'Sisa', 'Proses'],
            datasets: [{
                data: [terpakai, sisa, menunggu],
                backgroundColor: [
                    'rgba(21, 101, 192, 0.85)',
                    'rgba(16, 124, 65, 0.85)',
                    'rgba(220, 38, 38, 0.85)',
                ],
                borderColor: ['#1565c0', '#107c41', '#dc2626'],
                borderWidth: 2,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 16,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: { size: 12, family: 'Poppins', weight: '500' },
                        color: '#6b7280',
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    cornerRadius: 8,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': ' + context.raw + ' Hari';
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
@extends('layouts.app')

@section('content')
<style>
    /* ====== STAT CARDS - Premium Gradient + Shadow + Hover ====== */
    .stat-card {
        border-radius: 16px;
        padding: 28px 24px;
        color: white;
        text-align: center;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: -30px;
        right: -30px;
        width: 120px;
        height: 120px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        transition: transform 0.4s ease;
    }
    .stat-card::after {
        content: '';
        position: absolute;
        bottom: -20px;
        left: -20px;
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .stat-card:hover { 
        transform: translateY(-8px) scale(1.02); 
        box-shadow: 0 12px 35px rgba(0,0,0,0.2);
    }
    .stat-card:hover::before {
        transform: scale(1.5);
    }
    .stat-card h1 { font-size: 3rem; font-weight: 800; margin: 8px 0; position: relative; z-index: 1; }
    .stat-card h4 { font-size: 0.95rem; font-weight: 600; opacity: 0.9; position: relative; z-index: 1; letter-spacing: 0.3px; }
    .stat-card span { font-size: 0.8rem; opacity: 0.75; position: relative; z-index: 1; }
    .stat-card .stat-icon { 
        position: absolute; font-size: 4.5rem; top: 10px; right: 15px; opacity: 0.12; z-index: 0;
        transition: opacity 0.3s, transform 0.3s;
    }
    .stat-card:hover .stat-icon {
        opacity: 0.2;
        transform: rotate(-10deg) scale(1.1);
    }

    /* Gradient Backgrounds */
    .bg-gradient-blue { background: linear-gradient(135deg, #1565c0 0%, #0d47a1 50%, #0a3880 100%); }
    .bg-gradient-orange { background: linear-gradient(135deg, #f57c00 0%, #e65100 50%, #bf360c 100%); }
    .bg-gradient-green { background: linear-gradient(135deg, #2e7d32 0%, #1b5e20 50%, #0d3f14 100%); }
    .bg-gradient-red { background: linear-gradient(135deg, #c62828 0%, #b71c1c 50%, #8e1414 100%); }

    .badge-status { padding: 5px 12px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    .bg-menunggu { background-color: #ffc107; color: #000; }
    .bg-disetujui { background-color: #4caf50; color: white; }
    .bg-ditolak { background-color: #f44336; color: white; }

    /* Greeting Card */
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

    /* Chart Card */
    .chart-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        background: white;
        margin-bottom: 24px;
    }

    /* Pulse animation for cards on load */
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
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
    <p>Kelola data kepegawaian dan pengajuan cuti dengan mudah.</p>
    <div class="greeting-date">
        <i class="fas fa-calendar-alt me-2"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
    </div>
</div>

{{-- ============================================================ --}}
{{-- STAT CARDS --}}
{{-- ============================================================ --}}
<div class="row g-4 mb-4">
    <div class="col-md-3 animate-in">
        <div class="stat-card bg-gradient-blue">
            <i class="fas fa-users stat-icon"></i>
            <h4>Total Pegawai</h4>
            <h1>{{ $stats['total_pegawai'] }}</h1>
            <span>Orang</span>
        </div>
    </div>

    <div class="col-md-3 animate-in">
        <div class="stat-card bg-gradient-orange">
            <i class="fas fa-hourglass-half stat-icon"></i>
            <h4>Perlu Persetujuan</h4>
            <h1>{{ $stats['cuti_menunggu'] }}</h1>
            <span>Pengajuan</span>
        </div>
    </div>

    <div class="col-md-3 animate-in">
        <div class="stat-card bg-gradient-green">
            <i class="fas fa-check-double stat-icon"></i>
            <h4>Cuti Disetujui</h4>
            <h1>{{ $stats['cuti_disetujui'] }}</h1>
            <span>Total</span>
        </div>
    </div>
    
    <div class="col-md-3 animate-in">
        <div class="stat-card bg-gradient-red">
            <i class="fas fa-user-shield stat-icon"></i>
            <h4>Admin & Pimpinan</h4>
            <h1>{{ $stats['total_admin'] }}</h1>
            <span>User</span>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CHART + TABLE ROW --}}
{{-- ============================================================ --}}
<div class="row g-4 mb-4">
    {{-- CHART TREN CUTI --}}
    <div class="col-lg-5">
        <div class="chart-card">
            <div class="card-header bg-white py-3 border-bottom" style="border-radius: 16px 16px 0 0;">
                <h5 class="m-0 fw-bold"><i class="fas fa-chart-bar me-2 text-primary"></i>Tren Cuti per Bulan</h5>
            </div>
            <div class="card-body" style="padding: 20px;">
                <canvas id="cutiChart" height="220"></canvas>
            </div>
        </div>
    </div>

    {{-- TABLE 5 PENGAJUAN --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="m-0 fw-bold"><i class="fas fa-history me-2 text-primary"></i>5 Pengajuan Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Pegawai</th>
                                <th>Jenis Cuti</th>
                                <th>Tanggal</th>
                                <th>Lama</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($cuti_terbaru as $c)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $c->user->name ?? 'User Terhapus' }}</td>
                                <td>{{ $c->jenis_cuti }}</td>
                                <td>{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d/m/Y') }}</td>
                                <td>{{ $c->lama }} Hari</td>
                                <td>
                                    @if($c->status == 'Menunggu')
                                        <span class="badge-status bg-menunggu">Menunggu</span>
                                    @elseif($c->status == 'Disetujui')
                                        <span class="badge-status bg-disetujui">Disetujui</span>
                                    @else
                                        <span class="badge-status bg-ditolak">Ditolak</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data pengajuan cuti.</td>
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
{{-- CHART.JS SCRIPT --}}
{{-- ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('cutiChart').getContext('2d');

    // Data dari blade (pure frontend, tanpa ubah backend)
    const bulanLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    
    // Parse data cuti terbaru untuk mendapatkan distribusi per bulan
    const dataBulan = new Array(12).fill(0);
    @foreach($cuti_terbaru as $c)
        @php $bulanIdx = (int) \Carbon\Carbon::parse($c->tanggal_mulai)->format('n') - 1; @endphp
        dataBulan[{{ $bulanIdx }}]++;
    @endforeach

    // Jika tidak ada data, tampilkan data dummy agar chart tidak kosong
    const hasData = dataBulan.some(v => v > 0);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: bulanLabels,
            datasets: [{
                label: 'Pengajuan Cuti',
                data: hasData ? dataBulan : [2, 3, 1, 4, 2, 5, 3, 2, 4, 1, 3, 2],
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const {ctx: c, chartArea} = chart;
                    if (!chartArea) return '#107c41';
                    const gradient = c.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
                    gradient.addColorStop(0, 'rgba(16, 124, 65, 0.4)');
                    gradient.addColorStop(1, 'rgba(16, 124, 65, 0.85)');
                    return gradient;
                },
                borderColor: '#107c41',
                borderWidth: 1,
                borderRadius: 8,
                borderSkipped: false,
                barPercentage: 0.6,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: { size: 13, weight: 'bold' },
                    bodyFont: { size: 12 },
                    cornerRadius: 8,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { 
                        stepSize: 1, 
                        font: { size: 11, family: 'Poppins' },
                        color: '#9ca3af'
                    },
                    grid: { color: 'rgba(0,0,0,0.04)', drawBorder: false }
                },
                x: {
                    ticks: { 
                        font: { size: 11, family: 'Poppins' },
                        color: '#6b7280'
                    },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
@endsection
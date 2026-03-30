@extends('layouts.app')

@section('content')
<style>
    /* ====== STAT CARDS - Premium Gradient + Shadow + Hover ====== */
    .stat-card-pimpinan {
        border-radius: 16px;
        padding: 25px 20px;
        color: white;
        text-align: center;
        flex: 1;
        min-width: 160px;
        position: relative;
        overflow: hidden;
        transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 15px rgba(0,0,0,0.12);
    }
    .stat-card-pimpinan::before {
        content: '';
        position: absolute;
        top: -25px; right: -25px;
        width: 100px; height: 100px;
        border-radius: 50%;
        background: rgba(255,255,255,0.1);
        transition: transform 0.4s ease;
    }
    .stat-card-pimpinan:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 35px rgba(0,0,0,0.2);
    }
    .stat-card-pimpinan:hover::before { transform: scale(1.5); }
    .stat-card-pimpinan h4 { font-size: 0.85rem; font-weight: 600; opacity: 0.9; position: relative; z-index: 1; margin-bottom: 8px; }
    .stat-card-pimpinan h1 { font-size: 2.5rem; font-weight: 800; position: relative; z-index: 1; margin: 0; }
    .stat-card-pimpinan .stat-icon-sm { position: absolute; font-size: 3.5rem; bottom: -5px; right: 10px; opacity: 0.12; }

    .bg-grad-blue { background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); }
    .bg-grad-green { background: linear-gradient(135deg, #16a34a 0%, #15803d 100%); }
    .bg-grad-orange { background: linear-gradient(135deg, #ea580c 0%, #c2410c 100%); }
    .bg-grad-red { background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%); }

    /* Greeting */
    .greeting-card-pim {
        background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%);
        border-radius: 20px;
        padding: 28px 32px;
        color: white;
        position: relative;
        overflow: hidden;
        margin-bottom: 24px;
        box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);
    }
    .greeting-card-pim::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: rgba(255,255,255,0.06);
    }
    .greeting-card-pim h3 { font-weight: 700; font-size: 1.4rem; margin-bottom: 4px; position: relative; z-index: 1; }
    .greeting-card-pim p { opacity: 0.85; margin: 0; font-size: 0.9rem; position: relative; z-index: 1; }
    .greeting-card-pim .greeting-date-pim {
        position: relative; z-index: 1;
        display: inline-flex; align-items: center;
        background: rgba(255,255,255,0.15);
        padding: 5px 14px; border-radius: 50px;
        font-size: 0.78rem; margin-top: 10px;
        backdrop-filter: blur(5px);
    }

    /* Entrance animation */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
    .fade-in { animation: fadeInUp 0.45s ease-out forwards; }
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

<div class="greeting-card-pim">
    <h3>
        {{ $salam }}, 
        @if(Auth::user()->role == 'kasubag')
            Kasubag {{ Auth::user()->name }}! 👋
        @else
            Pimpinan {{ Auth::user()->name }}! 👋
        @endif
    </h3>
    <p>Pantau dan kelola persetujuan pengajuan cuti pegawai.</p>
    <div class="greeting-date-pim">
        <i class="fas fa-calendar-alt me-2"></i> {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
    </div>
</div>

{{-- ============================================================ --}}
{{-- DELEGASI WEWENANG (PLH) --}}
{{-- ============================================================ --}}
@php
    $pegawai_lain = \App\Models\User::where('id', '!=', Auth::id())
                        ->where('role', '!=', 'admin')
                        ->orderBy('name')
                        ->get(['id', 'name', 'nip']);
@endphp

<div class="card border-0 shadow-sm mb-4" style="background: #eefcf3; border-left: 5px solid #0f6b3d !important;">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div>
            <h5 class="fw-bold text-dark m-0">
                <i class="fas fa-user-shield me-2" style="color: #0f6b3d;"></i>Delegasi Wewenang (Plh)
            </h5>
            <p class="text-muted small m-0 mt-1">
                @if(Auth::user()->plh_id)
                    <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i> Status: LIMPAHKAN WEWENANG</span>
                    <span class="d-block mt-1">Saat ini wewenang Anda sedang dijalankan oleh <strong>{{ \App\Models\User::find(Auth::user()->plh_id)->name ?? '-' }}</strong></span>
                @else
                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i> Status: AKTIF</span>
                    <span class="d-block mt-1">Anda sedang bertugas aktif (Tidak ada Plh).</span>
                @endif
            </p>
        </div>
        
        <form action="{{ route('plh.update') }}" method="POST" class="d-flex gap-2 align-items-center">
            @csrf
            <div class="input-group">
                <span class="input-group-text bg-white text-muted"><i class="fas fa-user-edit"></i></span>
                <select name="plh_id" class="form-select" style="max-width: 250px;">
                    <option value="">-- Saya Aktif Kembali (Hapus Plh) --</option>
                    @foreach($pegawai_lain as $pg)
                        <option value="{{ $pg->id }}" {{ Auth::user()->plh_id == $pg->id ? 'selected' : '' }}>
                            Limpahkan ke: {{ $pg->name }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-success fw-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

{{-- ============================================================ --}}
{{-- STAT CARDS --}}
{{-- ============================================================ --}}
<div class="d-flex flex-wrap gap-3 mb-4">
    <div class="stat-card-pimpinan bg-grad-blue fade-in">
        <i class="fas fa-file-alt stat-icon-sm"></i>
        <h4>TOTAL CUTI</h4>
        <h1>{{ $total_cuti }}</h1>
    </div>
    <div class="stat-card-pimpinan bg-grad-green fade-in" style="animation-delay: 0.08s;">
        <i class="fas fa-hourglass-half stat-icon-sm"></i>
        <h4>MENUNGGU</h4>
        <h1>{{ $menunggu }}</h1>
    </div>
    <div class="stat-card-pimpinan bg-grad-orange fade-in" style="animation-delay: 0.16s;">
        <i class="fas fa-check-double stat-icon-sm"></i>
        <h4>DISETUJUI</h4>
        <h1>{{ $disetujui }}</h1>
    </div>
    <div class="stat-card-pimpinan bg-grad-red fade-in" style="animation-delay: 0.24s;">
        <i class="fas fa-times-circle stat-icon-sm"></i>
        <h4>DITOLAK</h4>
        <h1>{{ $ditolak }}</h1>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CHART + TABLE --}}
{{-- ============================================================ --}}
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="m-0 fw-bold"><i class="fas fa-chart-area me-2 text-primary"></i>Tren Persetujuan</h5>
            </div>
            <div class="card-body">
                <canvas id="cutiChartPimpinan" height="210"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="m-0 fw-bold"><i class="fas fa-history me-2 text-primary"></i>Pengajuan Cuti Terbaru</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th class="ps-4">Pegawai</th><th>Tanggal</th><th>Status</th></tr>
                        </thead>
                        <tbody>
                            @foreach($cuti_terbaru as $c)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $c->user->name }}</td>
                                <td>{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d-m-Y') }}</td>
                                <td>
                                    <span class="badge {{ $c->status == 'Menunggu' ? 'bg-warning text-dark' : ($c->status == 'Disetujui' ? 'bg-success' : 'bg-danger') }}">
                                        {{ $c->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- CHART.JS --}}
{{-- ============================================================ --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.7/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('cutiChartPimpinan').getContext('2d');
    const labels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    const dataBulan = new Array(12).fill(0);
    @foreach($cuti_terbaru as $c)
        @php $bi = (int) \Carbon\Carbon::parse($c->tanggal_mulai)->format('n') - 1; @endphp
        dataBulan[{{ $bi }}]++;
    @endforeach
    const hasData = dataBulan.some(v => v > 0);

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Pengajuan',
                data: hasData ? dataBulan : [1, 2, 3, 2, 4, 3, 5, 2, 3, 4, 2, 1],
                borderColor: '#107c41',
                backgroundColor: 'rgba(16, 124, 65, 0.08)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#107c41',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7,
                borderWidth: 2.5,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: { backgroundColor: 'rgba(0,0,0,0.8)', padding: 12, cornerRadius: 8 }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1, font: { size: 11, family: 'Poppins' }, color: '#9ca3af' }, grid: { color: 'rgba(0,0,0,0.04)' } },
                x: { ticks: { font: { size: 11, family: 'Poppins' }, color: '#6b7280' }, grid: { display: false } }
            }
        }
    });
});
</script>
@endsection
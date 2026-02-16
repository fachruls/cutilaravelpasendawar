@extends('layouts.app')

@section('content')
<style>
    .stat-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        padding: 25px;
        color: white;
        text-align: center;
        transition: transform 0.3s;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-card h1 { font-size: 3rem; font-weight: 700; margin: 5px 0; }
    .stat-card h4 { font-size: 1.1rem; font-weight: 500; opacity: 0.9; }

    /* Warna Spesifik Admin */
    .bg-primary-dark { background: #0d47a1; }
    .bg-success-dark { background: #1b5e20; }
    .bg-warning-dark { background: #f57f17; }
    .bg-danger-dark  { background: #b71c1c; }
    
    .badge-status { padding: 5px 12px; border-radius: 50px; font-weight: 600; font-size: 0.75rem; }
    .bg-menunggu { background-color: #ffc107; color: #000; }
    .bg-disetujui { background-color: #4caf50; color: white; }
    .bg-ditolak { background-color: #f44336; color: white; }
</style>

<div class="mb-4">
    <h4><i class="fas fa-tachometer-alt me-2"></i>Dashboard Admin</h4>
    <p class="text-muted">Ringkasan data kepegawaian dan pengajuan cuti.</p>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="stat-card bg-primary-dark">
            <h4>Total Pegawai</h4>
            <h1>{{ $stats['total_pegawai'] }}</h1>
            <span>Orang</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg-warning-dark">
            <h4>Perlu Persetujuan</h4>
            <h1>{{ $stats['cuti_menunggu'] }}</h1>
            <span>Pengajuan</span>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card bg-success-dark">
            <h4>Cuti Disetujui</h4>
            <h1>{{ $stats['cuti_disetujui'] }}</h1>
            <span>Total</span>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card bg-danger-dark">
            <h4>Admin & Pimpinan</h4>
            <h1>{{ $stats['total_admin'] }}</h1>
            <span>User</span>
        </div>
    </div>
</div>

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
@endsection
@extends('layouts.app')

@section('content')
<style>
    .stat-box { display: flex; flex-wrap: wrap; gap: 20px; margin: 20px 0; }
    .stat-card { border-radius: 12px; padding: 20px; color: white; flex: 1; text-align: center; }
    .green { background: #2ecc71; } .blue { background: #3498db; } .orange { background: #f39c12; } .red { background: #e74c3c; }
    .card { border-radius: 12px; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.08); padding: 20px; }
</style>

<div class="greeting mt-3 mb-4">
    <h4>
        <i class="fas fa-user-tie me-2"></i>
        @if(Auth::user()->role == 'kasubag')
            Halo, Kasubag Kepegawaian!
        @else
            Halo, Pimpinan!
        @endif
    </h4>
</div>

@php
    $is_pejabat = true; // Karena ini halaman khusus Pimpinan/Kasubag
    $pegawai_lain = \App\Models\User::where('id', '!=', Auth::id())
                        ->where('role', '!=', 'admin') // Opsional: sembunyikan admin IT
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

<div class="stat-box">
    <div class="stat-card blue">
        <h4>TOTAL CUTI</h4> <h1>{{ $total_cuti }}</h1>
    </div>
    <div class="stat-card green">
        <h4>MENUNGGU</h4> <h1>{{ $menunggu }}</h1>
    </div>
    <div class="stat-card orange">
        <h4>DISETUJUI</h4> <h1>{{ $disetujui }}</h1>
    </div>
    <div class="stat-card red">
        <h4>DITOLAK</h4> <h1>{{ $ditolak }}</h1>
    </div>
</div>

<div class="card mt-4">
    <h5 class="mb-3">Pengajuan Cuti Terbaru</h5>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>Pegawai</th><th>Tanggal</th><th>Status</th></tr>
            </thead>
            <tbody>
                @foreach($cuti_terbaru as $c)
                <tr>
                    <td>{{ $c->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d-m-Y') }}</td>
                    <td>
                        <span class="badge {{ $c->status == 'Menunggu' ? 'bg-warning' : ($c->status == 'Disetujui' ? 'bg-success' : 'bg-danger') }}">
                            {{ $c->status }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
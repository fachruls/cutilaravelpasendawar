@extends('layouts.app')

@section('content')
<div style="background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%); border-radius: 20px; padding: 24px 30px; color: white; position: relative; overflow: hidden; margin-bottom: 24px; box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);">
    <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
    <i class="fas fa-history" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); font-size: 4rem; opacity: 0.1;"></i>
    <h4 class="fw-bold m-0" style="position: relative; z-index: 1;"><i class="fas fa-history me-2"></i>Audit Trail (Log Aktivitas)</h4>
    <p class="m-0 mt-1" style="opacity: 0.85; font-size: 0.9rem; position: relative; z-index: 1;">Pantau semua aktivitas pengguna dalam sistem.</p>
</div>

<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="m-0 text-primary"><i class="fas fa-filter me-2"></i>Filter Data</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('admin.audit') }}" method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal Awal</label>
                <input type="date" name="tanggal_awal" class="form-control" value="{{ request('tanggal_awal', date('Y-m-01')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Tanggal Akhir</label>
                <input type="date" name="tanggal_akhir" class="form-control" value="{{ request('tanggal_akhir', date('Y-m-d')) }}">
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Pengguna</label>
                <select name="user_id" class="form-select">
                    <option value="">-- Semua Pengguna --</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ request('user_id') == $u->id ? 'selected' : '' }}>
                            {{ $u->name }} ({{ ucfirst($u->role) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">Aktivitas</label>
                <select name="action" class="form-select">
                    <option value="">-- Semua Aktivitas --</option>
                    <option value="LOGIN" {{ request('action') == 'LOGIN' ? 'selected' : '' }}>Login</option>
                    <option value="PENGAJUAN_CUTI" {{ request('action') == 'PENGAJUAN_CUTI' ? 'selected' : '' }}>Pengajuan Cuti</option>
                    <option value="PERSETUJUAN_CUTI" {{ request('action') == 'PERSETUJUAN_CUTI' ? 'selected' : '' }}>Persetujuan Cuti</option>
                    <option value="TAMBAH_PEGAWAI" {{ request('action') == 'TAMBAH_PEGAWAI' ? 'selected' : '' }}>Tambah Pegawai</option>
                    <option value="EDIT_PEGAWAI" {{ request('action') == 'EDIT_PEGAWAI' ? 'selected' : '' }}>Edit Pegawai</option>
                    <option value="HAPUS_PEGAWAI" {{ request('action') == 'HAPUS_PEGAWAI' ? 'selected' : '' }}>Hapus Pegawai</option>
                </select>
            </div>

            <div class="col-12 text-end">
                <a href="{{ route('admin.audit') }}" class="btn btn-secondary me-2">
                    <i class="fas fa-undo me-1"></i> Reset
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Terapkan Filter
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="15%">Waktu</th>
                        <th width="15%">Pengguna</th>
                        <th width="10%">Role</th>
                        <th width="15%">Aktivitas</th>
                        <th>Deskripsi</th>
                        <th width="10%">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td>{{ $loop->iteration + $logs->firstItem() - 1 }}</td>
                        <td>
                            <small class="fw-bold">{{ $log->created_at->format('d/m/Y') }}</small><br>
                            <small class="text-muted">{{ $log->created_at->format('H:i:s') }}</small>
                        </td>
                        <td>
                            <span class="fw-bold">{{ $log->user->name ?? 'User Terhapus' }}</span><br>
                            <small class="text-muted">{{ $log->user->nip ?? '-' }}</small>
                        </td>
                        <td>
                            @php 
                                $role = $log->user->role ?? 'unknown';
                                $badge = match($role) {
                                    'admin' => 'bg-danger',
                                    'pimpinan' => 'bg-success',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $badge }}">{{ ucfirst($role) }}</span>
                        </td>
                        <td>
                            @php
                                $color = match($log->action) {
                                    'LOGIN' => 'bg-info text-dark',
                                    'PENGAJUAN_CUTI' => 'bg-warning text-dark',
                                    'PERSETUJUAN_CUTI' => 'bg-success',
                                    'PENOLAKAN_CUTI' => 'bg-danger',
                                    'TAMBAH_PEGAWAI', 'EDIT_PEGAWAI' => 'bg-primary',
                                    'HAPUS_PEGAWAI' => 'bg-dark',
                                    default => 'bg-secondary'
                                };
                            @endphp
                            <span class="badge {{ $color }}">{{ str_replace('_', ' ', $log->action) }}</span>
                        </td>
                        <td class="text-muted small">
                            {{ Str::limit($log->description, 80) }}
                        </td>
                        <td class="small text-muted font-monospace">
                            {{ $log->ip_address }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fas fa-history fa-3x mb-3"></i>
                            <p>Tidak ada log aktivitas pada periode ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-3 border-top">
            {{ $logs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
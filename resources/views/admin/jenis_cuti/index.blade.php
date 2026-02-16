@extends('layouts.app')

@section('content')
<div class="greeting mt-3 mb-4">
    <h4><i class="fas fa-list-alt me-2"></i>Manajemen Jenis Cuti</h4>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-5">
        <div class="card">
            <h5 class="card-title mb-3">Tambah Jenis Cuti Baru</h5>
            <form method="POST" action="{{ route('admin.jenis-cuti.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Nama Jenis Cuti:</label>
                    <input type="text" class="form-control" name="nama_cuti" placeholder="Contoh: Cuti Tahunan" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Maksimal Hari:</label>
                    <input type="number" class="form-control" name="max_hari" placeholder="Contoh: 12" min="1" required>
                    <div class="form-text text-muted">Jumlah jatah hari maksimal per tahun.</div>
                </div>
                <button type="submit" class="btn btn-submit w-100">
                    <i class="fas fa-plus me-1"></i>Tambah Jenis Cuti
                </button>
            </form>
        </div>
        
        <div class="card mt-4">
            <div class="alert alert-info mb-0">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Catatan Penting:</strong>
                <ul class="mb-0 mt-2 ps-3">
                    <li>Jenis cuti akan muncul di form pengajuan pegawai.</li>
                    <li>Pastikan nama cuti unik (tidak kembar).</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                <h5 class="mb-0">Daftar Jenis Cuti</h5>
                <span class="badge bg-primary">{{ $jenis_cuti->count() }} Data</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Cuti</th>
                            <th>Max Hari</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jenis_cuti as $jc)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $jc->nama_cuti }}</td>
                            <td>{{ $jc->max_hari }} hari</td>
                            <td>
                                <button class="btn btn-sm btn-warning text-white" data-bs-toggle="modal" data-bs-target="#editModal{{ $jc->id }}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                
                                <form action="{{ route('admin.jenis-cuti.destroy', $jc->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus {{ $jc->nama_cuti }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <div class="modal fade" id="editModal{{ $jc->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Edit Jenis Cuti</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <form method="POST" action="{{ route('admin.jenis-cuti.update', $jc->id) }}">
                                        @csrf
                                        @method('PUT') <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Jenis Cuti:</label>
                                                <input type="text" class="form-control" name="nama_cuti" value="{{ $jc->nama_cuti }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Maksimal Hari:</label>
                                                <input type="number" class="form-control" name="max_hari" value="{{ $jc->max_hari }}" min="1" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" class="btn btn-submit">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Belum ada data jenis cuti.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
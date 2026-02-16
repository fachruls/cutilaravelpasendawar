@extends('layouts.app')

@section('content')
<div class="greeting mt-3 mb-4">
    <h4><i class="fas fa-calendar-day me-2"></i>Manajemen Hari Libur Nasional</h4>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white border-bottom">
                <h5 class="card-title m-0 text-success"><i class="fas fa-plus-circle me-2"></i>Tambah Hari Libur</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.hari-libur.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tanggal <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" name="tanggal" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Keterangan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="keterangan" rows="3" placeholder="Contoh: Hari Raya Idul Fitri" required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save me-1"></i> Simpan Data
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8 mb-4">
        <div class="card h-100">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                <h5 class="card-title m-0 text-primary"><i class="fas fa-list me-2"></i>Daftar Hari Libur</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th width="5%" class="text-center">No</th>
                                <th width="25%">Tanggal</th>
                                <th>Keterangan</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($hari_libur as $hl)
                            <tr>
                                <td class="text-center">{{ $loop->iteration + $hari_libur->firstItem() - 1 }}</td>
                                <td>
                                    <span class="fw-bold">{{ \Carbon\Carbon::parse($hl->tanggal)->translatedFormat('d F Y') }}</span><br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($hl->tanggal)->translatedFormat('l') }}</small>
                                </td>
                                <td>{{ $hl->keterangan }}</td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning text-dark" data-bs-toggle="modal" data-bs-target="#editModal{{ $hl->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <form action="{{ route('admin.hari-libur.destroy', $hl->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin ingin menghapus hari libur ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <div class="modal fade" id="editModal{{ $hl->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title fw-bold"><i class="fas fa-edit me-2"></i>Edit Hari Libur</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.hari-libur.update', $hl->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label">Tanggal</label>
                                                    <input type="date" class="form-control" name="tanggal" value="{{ $hl->tanggal }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label">Keterangan</label>
                                                    <input type="text" class="form-control" name="keterangan" value="{{ $hl->keterangan }}" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary">Update</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="fas fa-calendar-times fa-3x mb-3"></i>
                                    <p>Belum ada data hari libur.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer bg-white">
                {{ $hari_libur->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
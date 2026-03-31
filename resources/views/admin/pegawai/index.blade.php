@extends('layouts.app')

@section('content')
<div style="background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%); border-radius: 20px; padding: 24px 30px; color: white; position: relative; overflow: hidden; margin-bottom: 24px; box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);">
    <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
    <div style="position: absolute; bottom: -30px; right: 100px; width: 120px; height: 120px; border-radius: 50%; background: rgba(255,255,255,0.04);"></div>
    <i class="fas fa-users" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); font-size: 4rem; opacity: 0.1;"></i>
    <div class="d-flex justify-content-between align-items-center" style="position: relative; z-index: 1;">
        <div>
            <h4 class="fw-bold m-0"><i class="fas fa-users me-2"></i>Data Pegawai</h4>
            <p class="m-0 mt-1" style="opacity: 0.85; font-size: 0.9rem;">Kelola data seluruh pegawai dan pimpinan.</p>
        </div>
        <a href="{{ route('admin.pegawai.create') }}" class="btn btn-light fw-bold px-4 rounded-pill">
            <i class="fas fa-user-plus me-1"></i> Tambah Pegawai
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('admin.pegawai.index') }}" method="GET" class="mb-3">
            <div class="input-group">
                <input type="text" name="cari" class="form-control" placeholder="Cari Nama atau NIP..." value="{{ request('cari') }}">
                <button class="btn btn-primary" type="submit">Cari</button>
                @if(request('cari'))
                    <a href="{{ route('admin.pegawai.index') }}" class="btn btn-outline-secondary">Reset</a>
                @endif
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Role</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pegawai as $p)
                    <tr>
                        <td>{{ $loop->iteration + $pegawai->firstItem() - 1 }}</td>
                        <td>{{ $p->nip }}</td>
                        <td>{{ $p->name }}</td> 
                        <td>{{ $p->jabatan }}</td>
                        <td>
                            @if($p->role == 'pimpinan') 
                                <span class="badge bg-success">Pimpinan</span>
                            @else 
                                <span class="badge bg-secondary">Pegawai</span> 
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('admin.pegawai.edit', $p->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            
                            <form id="delete-form-{{ $p->id }}" action="{{ route('admin.pegawai.destroy', $p->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-sm btn-danger" onclick="konfirmasiHapus('{{ $p->id }}', '{{ $p->name }}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="fas fa-inbox fa-2x mb-2"></i>
                            <p>Data pegawai tidak ditemukan.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            {{ $pegawai->links() }}
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function konfirmasiHapus(id, nama) {
        Swal.fire({
            title: 'Apakah Anda Yakin?',
            text: "Pegawai atas nama \"" + nama + "\" akan dihapus permanen! Data tidak bisa dikembalikan.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Jika user klik Ya, submit form secara manual
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="container pb-5">
    
    <div class="card border-0 shadow-sm mb-4 bg-primary text-white" style="border-radius: 12px; background: linear-gradient(135deg, #107c41 0%, #0a4d29 100%);">
        <div class="card-body d-flex align-items-center p-4">
            <div class="bg-white bg-opacity-25 rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                <i class="fas fa-history fa-2x text-white"></i>
            </div>
            <div>
                <h4 class="m-0 fw-bold">Riwayat Cuti Saya</h4>
                <p class="m-0 opacity-75">Pantau status dan cetak surat cuti Anda di sini.</p>
            </div>
        </div>
    </div>

    @if (session('success'))
    <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
        <i class="fas fa-check-circle me-3 fs-4"></i>
        <div>
            <strong>Berhasil!</strong> {{ session('success') }}
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
            <h6 class="m-0 fw-bold text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>Daftar Pengajuan</h6>
            <a href="{{ route('cuti.create') }}" class="btn btn-primary btn-sm fw-bold px-3 rounded-pill">
                <i class="fas fa-plus me-1"></i> Ajukan Cuti
            </a>
        </div>
        
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-uppercase text-muted small">
                        <tr>
                            <th class="px-4 py-3 border-0">No</th>
                            <th class="py-3 border-0">Jenis Cuti</th>
                            <th class="py-3 border-0">Tanggal Pelaksanaan</th>
                            <th class="py-3 border-0">Durasi</th>
                            <th class="py-3 border-0">Status</th>
                            <th class="py-3 border-0" style="width: 20%;">Keterangan</th>
                            <th class="px-4 py-3 border-0 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($riwayat_cuti as $c)
                        <tr>
                            <td class="px-4 fw-bold text-muted">{{ $loop->iteration }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $c->jenis_cuti }}</span>
                                <div class="small text-muted mt-1">
                                    <i class="far fa-clock me-1"></i> Diajukan: {{ $c->created_at->format('d/m/Y') }}
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-medium">{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format('d M Y') }}</span>
                                    <span class="text-muted small">s/d {{ \Carbon\Carbon::parse($c->tanggal_selesai)->format('d M Y') }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                                    {{ $c->lama }} Hari
                                </span>
                            </td>
                            <td>
                                @if($c->status == 'Disetujui')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                                        <i class="fas fa-check-circle me-1"></i> Disetujui
                                    </span>
                                    @if($c->pejabat_berwenang)
                                        <div class="small text-muted mt-1 fst-italic" style="font-size: 0.7rem;">
                                            Oleh: {{ \App\Models\User::find($c->pejabat_berwenang)->name ?? 'Pejabat' }}
                                        </div>
                                    @endif
                                @elseif($c->status == 'Ditolak')
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">
                                        <i class="fas fa-times-circle me-1"></i> Ditolak
                                    </span>
                                @else
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-2 rounded-pill">
                                        <i class="fas fa-hourglass-half me-1"></i> {{ $c->status }}
                                    </span>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted small">{{ Str::limit($c->alasan, 40) }}</span>
                                @if($c->catatan_pejabat)
                                    <div class="text-danger small mt-1 fw-bold">
                                        Note: {{ $c->catatan_pejabat }}
                                    </div>
                                @endif
                            </td>
                            <td class="px-4 text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    
                                    {{-- FITUR CETAK: Muncul jika Disetujui ATAU Ditolak --}}
                                    @if($c->status == 'Disetujui' || $c->status == 'Ditolak')
                                        <a href="{{ route('cuti.cetak', $c->id) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-circle" title="Cetak Surat" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('cuti.cetak', ['id' => $c->id, 'download' => 1]) }}" class="btn btn-outline-success btn-sm rounded-circle" title="Download PDF" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-file-download"></i>
                                        </a>
                                    @else
                                        <button class="btn btn-light text-muted btn-sm rounded-circle border" disabled style="width: 32px; height: 32px; padding: 0;">
                                            <i class="fas fa-print"></i>
                                        </button>
                                    @endif

                                    {{-- FITUR BATAL: Tersedia selama Belum Ditolak --}}
                                    @if(!str_contains($c->status, 'Ditolak'))
                                        <form action="{{ route('cuti.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pengajuan ini? Saldo hari akan dikembalikan ke akun Anda.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm rounded-circle" title="Batalkan Pengajuan" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center">
                                    <div class="mb-3 text-muted opacity-25">
                                        <i class="fas fa-folder-open fa-4x"></i>
                                    </div>
                                    <h6 class="text-muted fw-bold mb-1">Belum ada riwayat</h6>
                                    <p class="text-muted small mb-0 opacity-75">Anda belum pernah mengajukan cuti.</p>
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
@endsection
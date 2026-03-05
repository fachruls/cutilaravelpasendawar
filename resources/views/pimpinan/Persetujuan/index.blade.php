@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="fw-bold text-primary"><i class="fas fa-check-double me-2"></i>Persetujuan Cuti</h2>
                <div class="badge bg-white text-primary p-2 border shadow-sm">
                    <i class="fas fa-calendar-alt me-1"></i> {{ now()->translatedFormat('l, d F Y') }}
                </div>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white p-3">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-list me-2"></i>Daftar Pengajuan Masuk</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle mb-0" id="tabelPersetujuan">
                            <thead class="bg-light text-secondary">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">Nama Pegawai</th>
                                    <th class="px-4 py-3">Jenis Cuti</th>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="px-4 py-3">Durasi</th>
                                    <th class="px-4 py-3">Status Saat Ini</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($persetujuan as $index => $item)
                                    <tr>
                                        <td class="px-4">{{ $index + 1 }}</td>
                                        <td class="px-4 fw-bold text-dark">{{ $item->user->name }}</td>
                                        <td class="px-4">
                                            <span class="badge bg-info text-dark bg-opacity-10 border border-info px-3 py-2 rounded-pill">
                                                {{ $item->jenis_cuti }}
                                            </span>
                                        </td>
                                        <td class="px-4">
                                            <small class="text-muted d-block"><i class="fas fa-calendar-plus me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') }}</small>
                                            <small class="text-muted d-block"><i class="fas fa-calendar-minus me-1"></i> {{ \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') }}</small>
                                        </td>
                                        <td class="px-4 fw-bold">{{ $item->lama }} Hari</td>
                                        <td class="px-4">
                                            @if ($item->status == 'Disetujui')
                                                <span class="badge bg-success"><i class="fas fa-check me-1"></i> Disetujui</span>
                                            @elseif($item->status == 'Ditolak')
                                                <span class="badge bg-danger"><i class="fas fa-times me-1"></i> Ditolak</span>
                                            @elseif($item->status == 'Menunggu Verifikasi')
                                                <span class="badge bg-warning text-dark"><i class="fas fa-user-check me-1"></i> Verifikasi Kasubag</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i> {{ $item->status }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 text-center">
                                            <div class="d-flex gap-2 justify-content-center">
                                                {{-- Tombol Detail --}}
                                                <button type="button" class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#modalDetail{{ $item->id }}">
                                                    <i class="fas fa-eye"></i>
                                                </button>

                                                @php
                                                    $perlu_persetujuan = false;
                                                    $user_login = Auth::user();

                                                    // --- [LOGIKA BARU]: KASUBAG & PLH ---
                                                    $kasubag_asli = \App\Models\User::where('role', 'kasubag')->first();
                                                    $is_plh_kasubag = $kasubag_asli && $kasubag_asli->plh_id == $user_login->id;

                                                    if ($item->status == 'Menunggu Verifikasi' && ($user_login->role == 'kasubag' || $is_plh_kasubag)) {
                                                        $perlu_persetujuan = true;
                                                    }

                                                    // 1. TUGAS ATASAN LANGSUNG (Level 1)
                                                    if ($item->status == 'Menunggu Atasan' && $user_login->id == $item->user->atasan_id) {
                                                        $perlu_persetujuan = true;
                                                    }

                                                    // 2. TUGAS PEJABAT BERWENANG/KETUA (Level 2)
                                                    if ($item->status == 'Menunggu Pejabat' && $user_login->role == 'pimpinan') {
                                                        $perlu_persetujuan = true;
                                                    }

                                                    // 3. LOGIKA PLH (Pelaksana Harian)
                                                    if ($item->status == 'Menunggu Atasan' && $item->user->atasan && $item->user->atasan->plh_id == $user_login->id) {
                                                        $perlu_persetujuan = true;
                                                    }
                                                    if ($item->status == 'Menunggu Pejabat') {
                                                        $ketua = \App\Models\User::where('role', 'pimpinan')->first();
                                                        if ($ketua && $ketua->plh_id == $user_login->id) {
                                                            $perlu_persetujuan = true;
                                                        }
                                                    }
                                                @endphp

                                                @if ($perlu_persetujuan)
                                                    {{-- Tombol Setujui / Verifikasi --}}
                                                    <form action="{{ route('pimpinan.persetujuan.setuju', $item->id) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Apakah Anda yakin ingin memproses data ini?')">
                                                            <i class="fas fa-check"></i> 
                                                            {{-- Ubah teks tombol sesuai status cuti agar akurat untuk PLH --}}
                                                            {{ $item->status == 'Menunggu Verifikasi' ? 'Verifikasi' : 'Setujui' }}
                                                        </button>
                                                    </form>
                                                    
                                                    {{-- Tombol TOLAK --}}
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalTolak{{ $item->id }}">
                                                        <i class="fas fa-times"></i> Tolak
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" width="80" class="mb-3 opacity-50">
                                            <p>Belum ada pengajuan cuti yang perlu diproses.</p>
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
</div>

{{-- MODAL AREA --}}
@foreach ($persetujuan as $item)

    <div class="modal fade" id="modalDetail{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title"><i class="fas fa-info-circle me-2"></i>Detail Pengajuan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item"><strong>Nama:</strong> {{ $item->user->name }}</li>
                        <li class="list-group-item"><strong>NIP:</strong> {{ $item->user->nip ?? '-' }}</li>
                        <li class="list-group-item"><strong>Jabatan:</strong> {{ $item->user->jabatan }}</li>
                        <li class="list-group-item"><strong>Alasan Cuti:</strong> {{ $item->alasan }}</li>
                        <li class="list-group-item"><strong>Alamat Selama Cuti:</strong> {{ $item->alamat_selama_cuti }}</li>
                        <li class="list-group-item"><strong>No HP:</strong> {{ $item->no_hp }}</li>
                        @if($item->file_surat)
                        <li class="list-group-item">
                            <strong>Lampiran:</strong>
                            <a href="{{ route('cuti.cetak', $item->id) }}" class="btn btn-sm btn-outline-primary ms-2" target="_blank">
                                <i class="fas fa-file-pdf me-1"></i> Lihat Dokumen
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    {{-- MODAL TOLAK (Tetap Sama) --}}
    <div class="modal fade" id="modalTolak{{ $item->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="{{ route('pimpinan.persetujuan.tolak', $item->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title"><i class="fas fa-times-circle me-2"></i>Tolak Pengajuan</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3 text-danger">
                            <i class="fas fa-ban fa-4x"></i>
                        </div>
                        <p class="text-center fw-bold">Apakah Anda yakin ingin menolak pengajuan ini?</p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold">Alasan Penolakan <span class="text-danger">*</span></label>
                            <textarea name="catatan" class="form-control" rows="3" placeholder="Contoh: Pekerjaan sedang menumpuk..." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-danger px-4">Tolak Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endforeach

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#tabelPersetujuan').DataTable({
            responsive: true,
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json"
            }
        });
    });
</script>
@endsection
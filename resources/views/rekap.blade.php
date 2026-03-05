@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2 class="fw-bold text-primary mb-4"><i class="fas fa-print me-2"></i>Rekapitulasi Cuti Pegawai</h2>
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th>No</th>
                            <th>Nama Pegawai</th>
                            <th>Jenis Cuti</th>
                            <th>Mulai</th>
                            <th>Selesai</th>
                            <th>Aksi Cetak</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data_cuti as $index => $cuti)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td class="fw-bold">{{ $cuti->user->name }}</td>
                                <td><span class="badge bg-info text-dark">{{ $cuti->jenis_cuti }}</span></td>
                                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->format('d M Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('cuti.cetak_formulir', $cuti->id) }}" class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Cetak Formulir
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data pengajuan yang selesai diproses</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
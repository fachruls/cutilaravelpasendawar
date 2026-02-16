@extends('layouts.app')

@section('content')
<div class="container pb-5">
    <div class="card border-0 shadow-sm" style="background: #0f6b3d; color: white;">
        <div class="card-body p-4">
            <h4 class="mb-1"><i class="fas fa-file-excel me-2"></i>Laporan Rekapitulasi Cuti</h4>
            <p class="mb-0 opacity-75">Silakan pilih rentang tanggal untuk mengunduh laporan ke Excel.</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body p-5">
            <form action="{{ route('admin.laporan.export') }}" method="POST">
                @csrf
                <div class="row align-items-end g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Dari Tanggal</label>
                        <input type="date" name="tanggal_awal" class="form-control" required value="{{ date('Y-m-01') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Sampai Tanggal</label>
                        <input type="date" name="tanggal_akhir" class="form-control" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold">
                            <i class="fas fa-download me-2"></i>Download Excel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
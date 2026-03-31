@extends('layouts.app')

@section('content')
<div class="container pb-5">
    <div style="background: linear-gradient(135deg, #107c41 0%, #0a5c30 50%, #064020 100%); border-radius: 20px; padding: 24px 30px; color: white; position: relative; overflow: hidden; margin-bottom: 24px; box-shadow: 0 8px 30px rgba(16, 124, 65, 0.25);">
        <div style="position: absolute; top: -40px; right: -40px; width: 180px; height: 180px; border-radius: 50%; background: rgba(255,255,255,0.06);"></div>
        <i class="fas fa-file-excel" style="position: absolute; right: 30px; top: 50%; transform: translateY(-50%); font-size: 4rem; opacity: 0.1;"></i>
        <h4 class="fw-bold m-0" style="position: relative; z-index: 1;"><i class="fas fa-file-excel me-2"></i>Laporan Rekapitulasi Cuti</h4>
        <p class="m-0 mt-1" style="opacity: 0.85; font-size: 0.9rem; position: relative; z-index: 1;">Silakan pilih rentang tanggal untuk mengunduh laporan ke Excel.</p>
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
@if($c->status == 'Disetujui')
    <a href="{{ route('cuti.cetak', $c->id) }}" target="_blank" class="btn btn-primary btn-sm" title="Cetak Surat">
        <i class="fas fa-print"></i>
    </a>
@else
    <button class="btn btn-secondary btn-sm" disabled title="Menunggu Persetujuan">
        <i class="fas fa-print"></i>
    </button>
@endif
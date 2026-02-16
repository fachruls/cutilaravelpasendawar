@extends('layouts.app')

@section('content')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<style>
    /* Custom Style untuk FullCalendar */
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        min-height: 600px;
    }
    
    /* Warna Header Kalender */
    .fc-toolbar-title { font-size: 1.2rem !important; font-weight: bold; color: #333; }
    .fc-col-header-cell { background-color: #f8f9fa; padding: 10px 0; }
    
    /* Tombol Navigasi */
    .fc-button-primary { 
        background-color: #0f6b3d !important; 
        border-color: #0f6b3d !important; 
    }
    .fc-button-primary:hover { 
        background-color: #0a4d29 !important; 
    }
    .fc-day-today { background-color: #e6f4ea !important; }

    /* Styling Event (Kotak Cuti) */
    .fc-event {
        cursor: pointer;
        border: none;
        padding: 2px 4px;
        font-size: 0.85rem;
        border-radius: 4px;
        transition: transform 0.2s;
    }
    .fc-event:hover { transform: scale(1.02); }

    /* Responsif di HP */
    @media (max-width: 768px) {
        .fc-toolbar { flex-direction: column; gap: 10px; }
        .fc-toolbar-title { font-size: 1rem !important; }
    }
</style>

<div class="container pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="fas fa-calendar-alt me-2 text-success"></i>Kalender Cuti</h4>
            <p class="text-muted small mb-0">Pantau jadwal cuti seluruh pegawai.</p>
        </div>
    </div>

    <div id='calendar'></div>

    <div class="card border-0 shadow-sm mt-4">
        <div class="card-body">
            <h6 class="fw-bold small mb-3 text-uppercase text-muted"><i class="fas fa-info-circle me-2"></i>Keterangan:</h6>
            <div class="d-flex flex-wrap gap-3 small">
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle p-2 me-2" style="background: #ffc107;"> </span> Menunggu
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle p-2 me-2" style="background: #28a745;"> </span> Disetujui
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle p-2 me-2" style="background: #dc3545;"> </span> Ditolak
                </div>
                <div class="d-flex align-items-center">
                    <span class="badge rounded-circle p-2 me-2" style="background: #ff9f89;"> </span> Hari Libur
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">Detail Cuti Pegawai</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table table-borderless table-sm mb-0">
                    <tr><td width="35%" class="text-muted">Nama</td><td width="5%">:</td><td id="modal-nama" class="fw-bold">-</td></tr>
                    <tr><td class="text-muted">NIP</td><td>:</td><td id="modal-nip">-</td></tr>
                    <tr><td class="text-muted">Jabatan</td><td>:</td><td id="modal-jabatan">-</td></tr>
                    <tr><td colspan="3"><hr class="my-2"></td></tr>
                    <tr><td class="text-muted">Jenis Cuti</td><td>:</td><td id="modal-jenis">-</td></tr>
                    <tr><td class="text-muted">Tanggal</td><td>:</td><td id="modal-tanggal">-</td></tr>
                    <tr><td class="text-muted">Lama</td><td>:</td><td id="modal-lama">-</td></tr>
                    <tr><td class="text-muted">Alasan</td><td>:</td><td id="modal-alasan">-</td></tr>
                    <tr><td class="text-muted">Status</td><td>:</td><td id="modal-status" class="fw-bold">-</td></tr>
                </table>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        // 1. Siapkan Data dari PHP ke JSON
        var events = [
            // Loop Data Cuti
            @foreach($cuti_bulan as $c)
            {
                title: '{{ $c->user->name }} ({{ $c->jenis_cuti }})',
                start: '{{ $c->tanggal_mulai }}',
                // FullCalendar end date is exclusive, so we add 1 day visually? 
                // Biar aman kita pakai tanggal selesai asli di modal, tapi di visual kalender kadang perlu +1 hari
                // Untuk simplenya kita pakai tanggal selesai asli dulu.
                end: '{{ \Carbon\Carbon::parse($c->tanggal_selesai)->addDay()->format("Y-m-d") }}', 
                backgroundColor: '{{ $c->status == "Disetujui" ? "#28a745" : ($c->status == "Ditolak" ? "#dc3545" : "#ffc107") }}',
                borderColor: '{{ $c->status == "Disetujui" ? "#28a745" : ($c->status == "Ditolak" ? "#dc3545" : "#ffc107") }}',
                textColor: '{{ $c->status == "Menunggu" ? "#000" : "#fff" }}',
                extendedProps: {
                    nama: '{{ $c->user->name ?? "-" }}',
                    nip: '{{ $c->user->nip ?? "-" }}',
                    jabatan: '{{ $c->user->jabatan ?? "-" }}',
                    jenis: '{{ $c->jenis_cuti }}',
                    tanggal: '{{ \Carbon\Carbon::parse($c->tanggal_mulai)->format("d/m/Y") }} s/d {{ \Carbon\Carbon::parse($c->tanggal_selesai)->format("d/m/Y") }}',
                    lama: '{{ $c->lama }} Hari',
                    alasan: '{{ Str::limit($c->alasan, 50) }}',
                    status: '{{ $c->status }}'
                }
            },
            @endforeach

            // Loop Hari Libur
            @foreach($hari_libur as $tgl => $libur)
            {
                title: 'LIBUR: {{ $libur->keterangan }}',
                start: '{{ $tgl }}',
                display: 'background', // Tampil sebagai background merah
                backgroundColor: '#ff9f89'
            },
            @endforeach
        ];

        // 2. Inisialisasi Kalender
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialDate: '{{ $tahun }}-{{ sprintf("%02d", $bulan) }}-01', // Tanggal awal sesuai Controller
            locale: 'id', // Bahasa Indonesia
            
            // RESPONSIF LOGIC:
            // Desktop: Grid Bulan
            // HP: List (Daftar)
            initialView: window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth',
            
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listMonth' // Tombol ganti view
            },
            
            buttonText: {
                today: 'Hari Ini',
                month: 'Kalender',
                list: 'Daftar Agenda'
            },

            events: events,

            // Saat Event Cuti Diklik
            eventClick: function(info) {
                // Cek apakah ini hari libur (background) atau cuti (event)
                if (info.event.display === 'background') return;

                var props = info.event.extendedProps;
                
                // Isi Modal
                document.getElementById('modal-nama').innerText = props.nama;
                document.getElementById('modal-nip').innerText = props.nip;
                document.getElementById('modal-jabatan').innerText = props.jabatan;
                document.getElementById('modal-jenis').innerText = props.jenis;
                document.getElementById('modal-tanggal').innerText = props.tanggal;
                document.getElementById('modal-lama').innerText = props.lama;
                document.getElementById('modal-alasan').innerText = props.alasan;
                
                var statusEl = document.getElementById('modal-status');
                statusEl.innerText = props.status;
                
                // Warna Status Modal
                statusEl.className = 'fw-bold';
                if(props.status === 'Disetujui') statusEl.classList.add('text-success');
                else if(props.status === 'Ditolak') statusEl.classList.add('text-danger');
                else statusEl.classList.add('text-warning');

                // Tampilkan Modal
                var myModal = new bootstrap.Modal(document.getElementById('detailModal'));
                myModal.show();
            },

            // Deteksi Resize Layar (Otomatis ganti tampilan HP/Laptop)
            windowResize: function(view) {
                if (window.innerWidth < 768) {
                    calendar.changeView('listMonth');
                } else {
                    calendar.changeView('dayGridMonth');
                }
            }
        });

        calendar.render();
    });
</script>
@endsection
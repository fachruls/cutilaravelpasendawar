@extends('layouts.app')

@section('content')

<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.7/dist/signature_pad.umd.min.js"></script>

<style>
    :root {
        --primary-color: #0f6b3d;
        --border-radius: 12px;
    }

    /* Card Styling */
    .card-custom {
        border-radius: var(--border-radius);
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        border: none;
        background: white;
    }

    /* Form Elements */
    .form-label { font-weight: 600; color: #444; margin-bottom: 8px; }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 12px;
        transition: all 0.3s;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(15, 107, 61, 0.1);
    }

    /* Calculation Detail Box (FITUR YANG ANDA MINTA JANGAN HILANG) */
    .calculation-detail {
        background: #e8f5e9; 
        border-left: 4px solid var(--primary-color);
        padding: 15px; 
        margin-top: 10px; 
        border-radius: 0 8px 8px 0;
    }
    .detail-list { 
        max-height: 200px; 
        overflow-y: auto; 
        font-size: 0.9rem; 
        background: rgba(255,255,255,0.5);
        border-radius: 6px;
        padding: 10px;
    }
    .libur-hari { color: #e74c3c; font-weight: 600; }
    .kerja-hari { color: #28a745; font-weight: 600; }

    /* SIGNATURE PAD CUSTOM (RESPONSIF & SMOOTH) */
    .signature-wrapper {
        position: relative;
        width: 100%;
        height: 250px;
        background-color: #f8f9fa;
        border: 2px dashed #ccc;
        border-radius: 12px;
        overflow: hidden;
        cursor: crosshair;
        touch-action: none;
    }
    .signature-wrapper:hover { border-color: var(--primary-color); background-color: #fff; }
    .signature-pad { position: absolute; left: 0; top: 0; width: 100%; height: 100%; }
    .signature-placeholder {
        position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);
        color: #adb5bd; font-size: 1.5rem; pointer-events: none; opacity: 0.5;
        font-family: 'Cookie', cursive;
    }

    /* Buttons */
    .btn-submit { background: var(--primary-color); color: white; border-radius: 8px; padding: 12px 30px; font-weight: bold; border: none; }
    .btn-submit:hover { background: #0a4d2a; color: white; }
</style>

<div class="container pb-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex align-items-center mb-4 mt-3">
                <div class="bg-white p-3 rounded-circle shadow-sm me-3">
                    <i class="fas fa-file-signature fa-2x text-success"></i>
                </div>
                <div>
                    <h4 class="fw-bold m-0">Form Pengajuan Cuti</h4>
                    <p class="text-muted m-0 small">Lengkapi data di bawah ini untuk mengajukan cuti.</p>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger shadow-sm border-0 rounded-3 mb-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="card-custom p-4 p-md-5">
                <form action="{{ route('cuti.store') }}" method="POST" enctype="multipart/form-data" id="formCuti">
                    @csrf
                    
                    <h6 class="text-success fw-bold border-bottom pb-2 mb-4">I. DATA CUTI</h6>

                    <div class="alert alert-info border-0 shadow-sm mb-4">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-wallet fa-2x me-3 text-info"></i>
                            <div>
                                <h6 class="fw-bold mb-1">Sisa Cuti Tahunan Anda:</h6>
                                <div class="d-flex gap-2 small flex-wrap">
                                    <span class="badge bg-primary">Tahun Ini (N): {{ Auth::user()->cuti_n }} Hari</span>
                                    <span class="badge bg-secondary">Sisa Thn Lalu (N-1): {{ Auth::user()->cuti_n1 }} Hari</span>
                                    @if(Auth::user()->cuti_n2 > 0)
                                        <span class="badge bg-dark">Sisa 2 Thn Lalu (N-2): {{ Auth::user()->cuti_n2 }} Hari</span>
                                    @endif
                                </div>
                                <small class="text-muted mt-1 d-block">
                                    Total Akumulasi: <strong>{{ Auth::user()->cuti_n + Auth::user()->cuti_n1 + Auth::user()->cuti_n2 }} Hari</strong>
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                            <select class="form-select" id="jenis" name="jenis_cuti" required>
                                <option value="">-- Pilih Jenis Cuti --</option>
                                @foreach($jenis_cuti as $jc)
                                    <option value="{{ $jc->nama_cuti }}">{{ $jc->nama_cuti }} (Maks: {{ $jc->max_hari }} hari)</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Dari Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="mulai" name="tanggal_mulai" required onchange="hitungLama()">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Sampai Tanggal <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="selesai" name="tanggal_selesai" required onchange="hitungLama()">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Lama Cuti (Otomatis) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control bg-light fw-bold text-success" id="lama" name="lama_display" readonly placeholder="Pilih tanggal dulu..." style="font-size: 1.1rem;">
                            
                            <div class="calculation-detail" id="calculationDetail" style="display: none;">
                                <h6 class="fw-bold text-dark"><i class="fas fa-calculator me-1"></i> Rincian Perhitungan:</h6>
                                <div class="detail-list">
                                    <ul id="detailList" class="list-unstyled mb-0 ps-2"></ul>
                                </div>
                                <small class="text-muted mt-2 d-block fst-italic">* Sabtu, Minggu, dan Libur Nasional tidak memotong kuota cuti.</small>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Alasan Cuti <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="alasan" rows="2" placeholder="Jelaskan alasan cuti..." required></textarea>
                        </div>
                    </div>

                    <h6 class="text-success fw-bold border-bottom pb-2 mb-4 mt-5">II. KONTAK & ALAMAT</h6>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-12">
                            <label class="form-label">Alamat Selama Cuti</label>
                            <input type="text" class="form-control" name="alamat_selama_cuti" required placeholder="Alamat lengkap...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP Aktif</label>
                            <input type="text" class="form-control" name="no_hp" value="{{ $user->no_hp }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Lampiran (Opsional)</label>
                            <input type="file" class="form-control" name="file_surat">
                        </div>
                    </div>

                    <h6 class="text-success fw-bold border-bottom pb-2 mb-4 mt-5">III. VALIDASI & TANDA TANGAN</h6>

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Atasan Langsung</label>
                            <input type="text" class="form-control" name="atasan_langsung" value="{{ $user->atasan_langsung }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pejabat Berwenang</label>
                            <input type="text" class="form-control" name="pejabat_berwenang" value="Ketua Pengadilan Agama Sendawar" required>
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="form-label d-flex justify-content-between align-items-center">
                            <span>Tanda Tangan Digital <span class="text-danger">*</span></span>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="clear-signature">
                                <i class="fas fa-eraser me-1"></i> Ulangi
                            </button>
                        </label>
                        
                        <div class="signature-wrapper" id="signature-container">
                            <div class="signature-placeholder" id="placeholder-text">Tanda tangan disini...</div>
                            <canvas id="signature-pad" class="signature-pad"></canvas>
                        </div>
                        
                        <input type="hidden" name="ttd_pegawai" id="ttd_input">
                        
                        <small class="text-muted d-block mt-2">
                            <i class="fas fa-info-circle me-1"></i> Gunakan jari (HP) atau mouse (Laptop) untuk tanda tangan.
                        </small>
                    </div>

                    <div class="d-flex justify-content-end gap-3 border-top pt-4">
                        <a href="{{ route('dashboard') }}" class="btn btn-light border px-4">Batal</a>
                        <button type="submit" class="btn btn-submit px-5">
                            <i class="fas fa-paper-plane me-2"></i>Kirim Pengajuan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // --- 1. KONFIGURASI TANDA TANGAN (SMOOTH & RESPONSIVE) ---
    const wrapper = document.getElementById('signature-container');
    const canvas = document.getElementById('signature-pad');
    const placeholder = document.getElementById('placeholder-text');
    
    const signaturePad = new SignaturePad(canvas, {
        minWidth: 1.5,
        maxWidth: 3.5,
        penColor: "rgb(0, 0, 0)",
        velocityFilterWeight: 0.7,
        throttle: 8
    });

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const data = signaturePad.toData();
        canvas.width = wrapper.offsetWidth * ratio;
        canvas.height = wrapper.offsetHeight * ratio;
        canvas.getContext("2d").scale(ratio, ratio);
        signaturePad.clear(); 
        signaturePad.fromData(data);
    }
    window.addEventListener("resize", resizeCanvas);
    resizeCanvas();

    signaturePad.addEventListener("beginStroke", () => {
        placeholder.style.display = "none";
    });

    document.getElementById('clear-signature').addEventListener('click', function () {
        signaturePad.clear();
        placeholder.style.display = "block";
    });

    // --- 2. VALIDASI & SUBMIT ---
    document.getElementById('formCuti').addEventListener('submit', function (e) {
        if (signaturePad.isEmpty()) {
            e.preventDefault();
            wrapper.style.borderColor = "red";
            wrapper.style.animation = "shake 0.3s";
            setTimeout(() => wrapper.style.animation = "", 300);
            
            alert("Mohon tanda tangan terlebih dahulu!");
            wrapper.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return;
        }

        const dataURL = signaturePad.toDataURL("image/png");
        document.getElementById('ttd_input').value = dataURL;
    });

    // --- 3. HITUNG HARI & RINCIAN (FITUR YANG ANDA MINTA DIKEMBALIKAN) ---
    function hitungLama() {
        const mulai = document.getElementById('mulai').value;
        const selesai = document.getElementById('selesai').value;
        const lamaInput = document.getElementById('lama');
        const detailBox = document.getElementById('calculationDetail');
        const detailList = document.getElementById('detailList');

        if (mulai && selesai) {
            // Validasi Tanggal
            if (new Date(selesai) < new Date(mulai)) {
                alert("Tanggal selesai tidak boleh lebih kecil dari tanggal mulai!");
                document.getElementById('selesai').value = "";
                return;
            }

            lamaInput.value = "Menghitung...";
            detailBox.style.display = 'none';

            fetch("{{ route('cuti.hitung') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ mulai: mulai, selesai: selesai })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update Input Total Hari
                    lamaInput.value = data.hari_kerja + " Hari Kerja";
                    
                    // Render List Rincian (Looping)
                    let html = '';
                    data.detail.forEach(d => {
                        const colorClass = d.status === 'KERJA' ? 'kerja-hari' : 'libur-hari';
                        const icon = d.status === 'KERJA' ? '<i class="fas fa-check-circle"></i>' : '<i class="fas fa-times-circle"></i>';
                        const info = d.keterangan ? `(${d.keterangan})` : '';
                        
                        html += `<li class="mb-2 border-bottom pb-1">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span>${icon} <strong>${d.tanggal}</strong> [${d.hari}]</span>
                                        <span class="${colorClass} badge bg-light border">${d.status} ${info}</span>
                                    </div>
                                 </li>`;
                    });
                    
                    detailList.innerHTML = html;
                    detailBox.style.display = 'block'; // Tampilkan kembali kotak rincian
                } else {
                    lamaInput.value = "Tanggal tidak valid";
                    alert("Gagal menghitung: " + data.message);
                }
            })
            .catch(err => {
                console.error(err);
                lamaInput.value = "Error koneksi";
            });
        }
    }
</script>

<style>
@keyframes shake {
  0% { transform: translateX(0); }
  25% { transform: translateX(-5px); }
  50% { transform: translateX(5px); }
  75% { transform: translateX(-5px); }
  100% { transform: translateX(0); }
}
</style>
@endsection
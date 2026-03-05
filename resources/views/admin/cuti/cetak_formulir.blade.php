<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Formulir Cuti Pegawai</title>
    <style>
        /* 1. SETUP KERTAS A4 DENGAN MARGIN TIPIS */
        @page { 
            size: A4; 
            margin: 10mm 15mm 5mm 15mm; 
        }
        
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 9pt; 
            line-height: 1.1; 
            color: #000; 
        }

        /* 2. STYLE TABEL PADAT */
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-bottom: 2px; 
        }
        
        td, th { 
            border: 1px solid #000; 
            padding: 1px 3px; 
            vertical-align: top; 
        }

        /* Helper Classes */
        .text-center { text-align: center; }
        .text-bold { font-weight: bold; }
        .valign-middle { vertical-align: middle; }
        .no-border { border: none !important; }
        .no-border-left { border-left: none !important; }
        .no-border-right { border-right: none !important; }

        /* Checkbox Style */
        .check-box {
            display: inline-block;
            width: 10px; height: 10px;
            border: 1px solid #000;
            text-align: center;
            line-height: 9px;
            font-size: 8px;
            font-weight: bold;
        }

        /* Area Tanda Tangan COMPACT */
        .ttd-container {
            height: 50px; 
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .ttd-img {
            max-height: 45px;
            max-width: 90px;
            object-fit: contain;
        }

        /* Header Form */
        .header-lampiran { font-size: 8pt; text-align: right; margin-bottom: 5px; }
        .tujuan-surat { float: right; width: 50%; text-align: left; margin-bottom: 5px; font-size: 9pt; }
        .judul-form { text-align: center; font-weight: bold; font-size: 11pt; margin-top: 5px; text-decoration: underline; }
        .nomor-form { text-align: center; margin-bottom: 5px; }
        
        /* Spacer */
        .spacer { height: 3px; }
    </style>
</head>
<body onload="window.print()">
    <?php \Carbon\Carbon::setLocale('id'); ?>

    <div class="header-lampiran">
        LAMPIRAN II<br>
        SURAT EDARAN SEKRETARIS MAHKAMAH AGUNG<br>
        REPUBLIK INDONESIA NOMOR 1 TAHUN 2019
    </div>

    <div class="tujuan-surat">
        Sendawar, {{ \Carbon\Carbon::parse($cuti->tanggal_usulan)->translatedFormat('d F Y') }}<br>
        Kepada Yth.<br>
        Ketua Pengadilan Agama Sendawar<br>
        di -<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tempat
    </div>
    <div style="clear: both;"></div>

    <div class="judul-form">FORMULIR PERMINTAAN DAN PEMBERIAN CUTI</div>
    <div style="text-align: center; margin-bottom: 5px;">NOMOR : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/KPA.W17-A12/KP5.3/&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;/2026</div>

    <table>
        <tr><td colspan="4" class="text-bold">I. DATA PEGAWAI</td></tr>
        <tr>
            <td width="15%">NAMA</td>
            <td width="35%">{{ strtoupper($cuti->user->name) }}</td>
            <td width="15%">NIP</td>
            <td width="35%">{{ $cuti->user->nip }}</td>
        </tr>
        <tr>
            <td>JABATAN</td>
            <td>{{ strtoupper($cuti->user->jabatan) }}</td>
            <td>GOL. RUANG</td>
            <td>{{ strtoupper($cuti->user->golongan ?? '-') }}</td>
        </tr>
        <tr>
            <td>UNIT KERJA</td>
            <td>{{ strtoupper($cuti->user->unit_kerja ?? 'PA SENDAWAR') }}</td>
            <td>MASA KERJA</td>
            <td>
                @if($cuti->user->tmt_masuk)
                    @php
                        $start = \Carbon\Carbon::parse($cuti->user->tmt_masuk);
                        $diff = $start->diff(\Carbon\Carbon::now());
                    @endphp
                    {{ $diff->y }} Tahun {{ $diff->m }} Bulan
                @else
                    -
                @endif
            </td>
        </tr>
    </table>

    <div class="spacer"></div>

    <table>
        <tr><td colspan="4" class="text-bold">II. JENIS CUTI YANG DIAMBIL **</td></tr>
        <tr>
            <td width="40%">1. CUTI TAHUNAN</td>
            <td width="10%" class="text-center">@if($cuti->jenis_cuti == 'Cuti Tahunan') <span class="check-box">V</span> @endif</td>
            <td width="40%">2. CUTI BESAR</td>
            <td width="10%" class="text-center">@if($cuti->jenis_cuti == 'Cuti Besar') <span class="check-box">V</span> @endif</td>
        </tr>
        <tr>
            <td>3. CUTI SAKIT</td>
            <td class="text-center">@if($cuti->jenis_cuti == 'Cuti Sakit') <span class="check-box">V</span> @endif</td>
            <td>4. CUTI MELAHIRKAN</td>
            <td class="text-center">@if($cuti->jenis_cuti == 'Cuti Melahirkan') <span class="check-box">V</span> @endif</td>
        </tr>
        <tr>
            <td>5. CUTI KARENA ALASAN PENTING</td>
            <td class="text-center">@if($cuti->jenis_cuti == 'Cuti Alasan Penting') <span class="check-box">V</span> @endif</td>
            <td>6. CUTI DILUAR TANGGUNGAN NEGARA</td>
            <td class="text-center">@if($cuti->jenis_cuti == 'Cuti Diluar Tanggungan') <span class="check-box">V</span> @endif</td>
        </tr>
    </table>

    <div class="spacer"></div>

    <table>
        <tr><td class="text-bold">III. ALASAN CUTI</td></tr>
        <tr><td>{{ $cuti->alasan }}</td></tr>
    </table>

    <div class="spacer"></div>

    <table>
        <tr><td colspan="6" class="text-bold">IV. LAMANYA CUTI</td></tr>
        <tr>
            <td width="15%">Selama</td>
            <td width="20%">{{ $cuti->lama_hari_kerja ?? $cuti->lama }} Hari Kerja</td>
            <td width="15%">Mulai Tanggal</td>
            <td width="20%">{{ \Carbon\Carbon::parse($cuti->tanggal_mulai)->translatedFormat('d F Y') }}</td>
            <td width="5%">s.d</td>
            <td width="25%">{{ \Carbon\Carbon::parse($cuti->tanggal_selesai)->translatedFormat('d F Y') }}</td>
        </tr>
    </table>

    <div class="spacer"></div>

    <table>
        <tr><td colspan="5" class="text-bold">V. CATATAN CUTI ***</td></tr>
        <tr>
            <td colspan="3" style="width: 50%;">1. CUTI TAHUNAN</td>
            <td style="width: 30%;">2. CUTI BESAR</td>
            <td style="width: 20%;" class="text-center">PARAF PETUGAS CUTI</td>
        </tr>
        <tr>
            <td width="10%" class="text-center">TAHUN</td>
            <td width="10%" class="text-center">SISA</td>
            <td width="30%" class="text-center">KETERANGAN</td>
            <td>3. CUTI SAKIT</td>
            <td rowspan="4" class="valign-middle text-center"></td>
        </tr>
       @php 
            $tahun = date('Y'); 
            $kuota_awal = $sisa_cuti_tahunan ?? 12;
            $jumlah_diambil = ($cuti->jenis_cuti == 'Cuti Tahunan') ? ($cuti->lama_hari_kerja ?? $cuti->lama) : 0;
            $sisa_aktual = $kuota_awal - $jumlah_diambil;
        @endphp
        <tr>
            <td class="text-center">{{ $tahun - 2 }}</td><td class="text-center">0</td><td></td>
            <td>4. CUTI MELAHIRKAN</td>
        </tr>
        <tr>
            <td class="text-center">{{ $tahun - 1 }}</td><td class="text-center">0</td><td></td>
            <td>5. CUTI KARENA ALASAN PENTING</td>
        </tr>
        <tr>
            <td class="text-center">{{ $tahun }}</td><td class="text-center">{{ $sisa_aktual }}</td><td></td>
            <td>6. CUTI DILUAR TANGGUNGAN NEGARA</td>
        </tr>
    </table>

    <div class="spacer"></div>

    <table>
        <tr><td colspan="3" class="text-bold">VI. ALAMAT SELAMA MENJALANKAN CUTI</td></tr>
        <tr>
            <td width="50%" style="border-right: none; height: 60px; vertical-align: top;">
                {{ $cuti->alamat_selama_cuti }}<br>
                TELP: {{ $cuti->no_hp }}
            </td>
            <td width="10%" style="border-left: none; border-right: none;"></td>
            <td width="40%" class="text-center" style="border-left: none;">
                Hormat saya,<br>
                <div class="ttd-container">
                    @php $path = $cuti->ttd_path ? storage_path('app/public/' . $cuti->ttd_path) : null; @endphp
                    @if($path && file_exists($path))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($path)) }}" class="ttd-img">
                    @endif
                </div>
                <strong>{{ strtoupper($cuti->user->name) }}</strong><br>
                NIP. {{ $cuti->user->nip }}
            </td>
        </tr>
    </table>

    <div class="spacer"></div>

    <table>
        <tr><td colspan="4" class="text-bold">VII. PERTIMBANGAN ATASAN LANGSUNG **</td></tr>
        <tr>
            <td width="20%" class="text-center">DISETUJUI</td>
            <td width="20%" class="text-center">PERUBAHAN</td>
            <td width="20%" class="text-center">DITANGGUHKAN</td>
            <td width="40%" class="text-center">TIDAK DISETUJUI</td>
        </tr>
        <tr>
            <td class="text-center">@if(in_array($cuti->status, ['Disetujui', 'Menunggu Pejabat'])) <span class="check-box">V</span> @endif</td>
            <td></td><td></td>
            <td class="text-center">@if($cuti->status == 'Ditolak') <span class="check-box">V</span> @endif</td>
        </tr>
        <tr>
            <td colspan="3" class="no-border-right"></td>
            <td class="text-center no-border-left">
                @if($cuti->is_plh_atasan) Plh. @endif Atasan Langsung,<br>
                <div class="ttd-container">
                    @php $pathAtasan = $cuti->atasan && $cuti->atasan->ttd_path ? storage_path('app/public/' . $cuti->atasan->ttd_path) : null; @endphp
                    @if($pathAtasan && file_exists($pathAtasan))
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($pathAtasan)) }}" class="ttd-img">
                    @endif
                </div>
                <strong><u>{{ strtoupper($cuti->atasan_langsung) }}</u></strong><br>
                NIP. {{ $cuti->atasan->nip ?? '....................' }}
            </td>
        </tr>
    </table>

    <div class="spacer"></div>

    <table>
        <tr><td colspan="4" class="text-bold">VIII. KEPUTUSAN PEJABAT YANG BERWENANG MEMBERIKAN CUTI **</td></tr>
        <tr>
            <td class="text-center">DISETUJUI</td>
            <td class="text-center">PERUBAHAN</td>
            <td class="text-center">DITANGGUHKAN</td>
            <td class="text-center">TIDAK DISETUJUI</td>
        </tr>
        <tr>
            <td class="text-center">@if($cuti->status == 'Disetujui') <span class="check-box">V</span> @endif</td>
            <td></td><td></td>
            <td class="text-center">@if($cuti->status == 'Ditolak' && $cuti->atasan_langsung == $cuti->pejabat_berwenang) <span class="check-box">V</span> @endif</td>
        </tr>
        <tr>
            <td colspan="3" class="no-border-right"></td>
            <td class="text-center no-border-left">
                @if($cuti->is_plh_pejabat) Plh. @endif Ketua Pengadilan Agama Sendawar,<br>
                <div class="ttd-container">
                    @php $pathPejabat = $cuti->pejabat && $cuti->pejabat->ttd_path ? storage_path('app/public/' . $cuti->pejabat->ttd_path) : null; @endphp
                    @if($pathPejabat && file_exists($pathPejabat) && $cuti->status == 'Disetujui')
                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($pathPejabat)) }}" class="ttd-img">
                    @endif
                </div>
                <strong><u>{{ strtoupper($cuti->pejabat_berwenang ?? 'ERIK ASWANDI, S.H.I.') }}</u></strong><br>
                NIP. {{ $cuti->pejabat->nip ?? '-' }}
            </td>
        </tr>
    </table>
    <div style="font-size: 9pt; margin-top: 5px;">
        <span class="bold">Catatan:</span><br>
        * Coret yang tidak perlu / tidak digunakan<br>
        ** Pilih salah satu dengan memberi tanda centang (V)<br>
        *** Diisi oleh pejabat yang menangani bidang kepegawaian sebelum pejabat yang berwenang menetapkan keputusan<br>
        **** Tuliskan alasan penangguhan / perubahan / penolakan
    </div>                                               

</div>

</body>
</html>
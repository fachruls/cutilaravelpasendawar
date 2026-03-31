<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.7; color: #333; max-width: 640px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .container { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #107c41 0%, #0a5c30 100%); color: white; padding: 30px; text-align: center; }
        .header h2 { margin: 0; font-size: 20px; font-weight: 700; letter-spacing: 0.5px; }
        .header p { margin: 5px 0 0; opacity: 0.85; font-size: 13px; }
        .content { padding: 30px 35px; }
        .footer { text-align: center; padding: 20px 30px; background: #f8f9fa; border-top: 1px solid #e9ecef; }
        .footer p { margin: 3px 0; color: #888; font-size: 11px; }
        
        .greeting { font-size: 15px; color: #555; margin-bottom: 5px; }
        .title-box { display: flex; align-items: center; margin-bottom: 15px; }
        .title-icon { font-size: 28px; margin-right: 10px; }
        .title-text { font-size: 20px; font-weight: 700; margin: 0; }
        .desc { font-size: 14px; color: #555; margin-bottom: 20px; line-height: 1.6; }

        .flow-tracker { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 18px 20px; margin-bottom: 22px; }
        .flow-tracker h4 { margin: 0 0 12px; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; color: #15803d; font-weight: 700; }
        .flow-steps { display: flex; align-items: center; justify-content: center; gap: 0; flex-wrap: wrap; }
        .flow-step { text-align: center; font-size: 11px; font-weight: 600; padding: 6px 10px; border-radius: 6px; white-space: nowrap; }
        .flow-step.done { background: #16a34a; color: white; }
        .flow-step.active { background: #f59e0b; color: white; animation: pulse 1.5s infinite; }
        .flow-step.pending { background: #e5e7eb; color: #9ca3af; }
        .flow-arrow { color: #d1d5db; font-size: 14px; margin: 0 3px; }
        .flow-arrow.done { color: #16a34a; }

        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.7; } }

        .data-card { background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0; padding: 0; margin: 20px 0; overflow: hidden; }
        .data-card-header { background: #107c41; color: white; padding: 10px 18px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; }
        .data-card table { width: 100%; border-collapse: collapse; }
        .data-card td { padding: 10px 18px; font-size: 13px; border-bottom: 1px solid #f1f5f9; }
        .data-card td:first-child { width: 120px; color: #64748b; font-weight: 600; }
        .data-card td:last-child { color: #1e293b; }
        .data-card tr:last-child td { border-bottom: none; }

        .status-badge { display: inline-block; padding: 4px 14px; border-radius: 6px; font-size: 12px; font-weight: 700; }
        .badge-green { background: #dcfce7; color: #15803d; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-blue { background: #dbeafe; color: #1e40af; }

        .cta-box { text-align: center; margin: 25px 0 10px; }
        .btn { display: inline-block; background: linear-gradient(135deg, #107c41, #0a5c30); color: white !important; text-decoration: none; padding: 14px 32px; border-radius: 8px; font-weight: 700; font-size: 14px; letter-spacing: 0.3px; }

        .note-box { background: #fffbeb; border: 1px solid #fde68a; border-radius: 8px; padding: 14px 18px; margin-top: 18px; font-size: 13px; color: #92400e; }
        .note-box strong { display: block; margin-bottom: 4px; }

        .rejection-box { background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0; }
        .rejection-box .icon { font-size: 48px; margin-bottom: 10px; }
        .rejection-box .reason { background: white; border: 1px solid #fecaca; border-radius: 8px; padding: 12px 16px; margin-top: 12px; font-style: italic; color: #991b1b; text-align: left; }

        .approved-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 20px; text-align: center; margin: 20px 0; }
        .approved-box .icon { font-size: 48px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        {{-- ==================== HEADER ==================== --}}
        <div class="header">
            <h2>Pengadilan Agama Sendawar</h2>
            <p>Sistem Informasi Cuti Pegawai (E-Cuti PAS)</p>
        </div>

        <div class="content">

            {{-- ==================== KASUBAG: Verifikasi Awal ==================== --}}
            @if($tipe_notif == 'kasubag')
                <p class="greeting">Yth. Bapak/Ibu Kasubag Kepegawaian,</p>
                <h2 class="title-text" style="color: #107c41;">📋 Pengajuan Cuti Baru — Perlu Verifikasi</h2>
                <p class="desc">Seorang pegawai telah mengajukan permohonan cuti melalui sistem E-Cuti. Pengajuan ini memerlukan <strong>verifikasi administrasi</strong> dari Kasubag Kepegawaian sebelum diteruskan ke Atasan Langsung.</p>

                {{-- Flow Tracker --}}
                <div class="flow-tracker">
                    <h4>📍 Alur Disposisi Pengajuan</h4>
                    <div class="flow-steps">
                        <span class="flow-step done">✓ Pegawai</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step active">⏳ Kasubag</span>
                        <span class="flow-arrow">→</span>
                        <span class="flow-step pending">Atasan</span>
                        <span class="flow-arrow">→</span>
                        <span class="flow-step pending">Ketua</span>
                    </div>
                </div>

                <div class="note-box">
                    <strong>⚠️ Perhatian:</strong>
                    Mohon segera melakukan verifikasi kelengkapan administrasi (saldo cuti, lampiran, dll.). Setelah diverifikasi, pengajuan akan otomatis diteruskan ke Atasan Langsung pegawai.
                </div>

            {{-- ==================== ATASAN: Persetujuan Atasan ==================== --}}
            @elseif($tipe_notif == 'atasan')
                <p class="greeting">Yth. Bapak/Ibu Atasan Langsung,</p>
                <h2 class="title-text" style="color: #107c41;">📝 Permohonan Cuti Bawahan — Perlu Persetujuan</h2>
                <p class="desc">Pengajuan cuti pegawai di bawah ini telah <strong>lolos verifikasi Kasubag Kepegawaian</strong> dan kini memerlukan persetujuan Anda selaku Atasan Langsung.</p>

                <div class="flow-tracker">
                    <h4>📍 Alur Disposisi Pengajuan</h4>
                    <div class="flow-steps">
                        <span class="flow-step done">✓ Pegawai</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step done">✓ Kasubag</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step active">⏳ Atasan</span>
                        <span class="flow-arrow">→</span>
                        <span class="flow-step pending">Ketua</span>
                    </div>
                </div>

                <div class="note-box">
                    <strong>⚠️ Perhatian:</strong>
                    Silakan periksa pengajuan ini dan berikan persetujuan atau penolakan. Jika disetujui, pengajuan akan otomatis diteruskan ke Ketua untuk penetapan akhir.
                </div>

            {{-- ==================== KETUA: Penetapan Final ==================== --}}
            @elseif($tipe_notif == 'ketua')
                <p class="greeting">Yth. Bapak/Ibu Ketua Pengadilan,</p>
                <h2 class="title-text" style="color: #107c41;">⚖️ Penetapan Akhir Cuti — Menunggu Tanda Tangan</h2>
                <p class="desc">Pengajuan cuti pegawai berikut telah <strong>diverifikasi Kasubag</strong> dan <strong>disetujui Atasan Langsung</strong>. Pengajuan ini memerlukan penetapan akhir dan tanda tangan digital dari Bapak/Ibu Ketua.</p>

                <div class="flow-tracker">
                    <h4>📍 Alur Disposisi Pengajuan</h4>
                    <div class="flow-steps">
                        <span class="flow-step done">✓ Pegawai</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step done">✓ Kasubag</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step done">✓ Atasan</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step active">⏳ Ketua</span>
                    </div>
                </div>

                <div class="note-box">
                    <strong>⚠️ Perhatian:</strong>
                    Ini adalah tahap akhir dari proses persetujuan. Setelah Bapak/Ibu menyetujui, Surat Cuti akan secara otomatis diterbitkan dan pegawai akan dinotifikasi.
                </div>

            {{-- ==================== DISETUJUI: Notif ke Pegawai ==================== --}}
            @elseif($tipe_notif == 'disetujui')
                <p class="greeting">Yth. Sdr/i {{ $cuti->user->name ?? 'Pegawai' }},</p>
                <h2 class="title-text" style="color: #16a34a;">✅ Pengajuan Cuti Anda DISETUJUI</h2>
                <p class="desc">Dengan ini diberitahukan bahwa pengajuan cuti Anda telah <strong>disetujui sepenuhnya</strong> oleh seluruh pejabat berwenang. Surat Cuti telah diterbitkan secara otomatis oleh sistem.</p>

                <div class="approved-box">
                    <div class="icon">🎉</div>
                    <h3 style="color: #16a34a; margin: 0;">Selamat!</h3>
                    <p style="color: #555; margin: 5px 0 0; font-size: 13px;">Seluruh proses persetujuan telah selesai.</p>
                </div>

                <div class="flow-tracker">
                    <h4>📍 Alur Disposisi — Selesai</h4>
                    <div class="flow-steps">
                        <span class="flow-step done">✓ Pegawai</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step done">✓ Kasubag</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step done">✓ Atasan</span>
                        <span class="flow-arrow done">→</span>
                        <span class="flow-step done">✓ Ketua</span>
                    </div>
                </div>

            {{-- ==================== DITOLAK: Notif ke Pegawai ==================== --}}
            @elseif($tipe_notif == 'ditolak')
                <p class="greeting">Yth. Sdr/i {{ $cuti->user->name ?? 'Pegawai' }},</p>
                <h2 class="title-text" style="color: #dc2626;">❌ Pengajuan Cuti Ditolak</h2>
                <p class="desc">Dengan ini diberitahukan bahwa pengajuan cuti Anda <strong>tidak dapat disetujui</strong> pada saat ini. Berikut adalah rincian dan alasan penolakan.</p>

                <div class="rejection-box">
                    <div class="icon">📋</div>
                    <h3 style="color: #dc2626; margin: 0;">Mohon Maaf</h3>
                    <p style="color: #555; margin: 5px 0 0; font-size: 13px;">Pengajuan Anda belum dapat diproses lebih lanjut.</p>
                    @if($cuti->catatan_atasan && $cuti->status == 'Ditolak')
                        <div class="reason">
                            <strong style="font-style: normal; color: #991b1b;">Alasan Penolakan:</strong><br>
                            {{ $cuti->catatan_atasan ?? $cuti->catatan_pejabat ?? '-' }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- ==================== DATA CARD (semua tipe) ==================== --}}
            <div class="data-card">
                <div class="data-card-header">📄 Data Pengajuan Cuti</div>
                <table>
                    <tr>
                        <td>Nama Pegawai</td>
                        <td>: <strong>{{ $cuti->user->name ?? 'Pegawai' }}</strong></td>
                    </tr>
                    <tr>
                        <td>NIP</td>
                        <td>: {{ $cuti->user->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jabatan</td>
                        <td>: {{ $cuti->user->jabatan ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Jenis Cuti</td>
                        <td>: <span class="status-badge badge-blue">{{ $cuti->jenis_cuti }}</span></td>
                    </tr>
                    <tr>
                        <td>Tanggal</td>
                        <td>: {{ date('d F Y', strtotime($cuti->tanggal_mulai)) }} s.d {{ date('d F Y', strtotime($cuti->tanggal_selesai)) }}</td>
                    </tr>
                    <tr>
                        <td>Lama</td>
                        <td>: <strong>{{ $cuti->lama }} Hari Kerja</strong></td>
                    </tr>
                    <tr>
                        <td>Alasan</td>
                        <td>: {{ $cuti->alasan }}</td>
                    </tr>
                    <tr>
                        <td>Alamat Cuti</td>
                        <td>: {{ $cuti->alamat_selama_cuti ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>No. HP</td>
                        <td>: {{ $cuti->no_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td>Status</td>
                        <td>: 
                            @if($tipe_notif == 'disetujui')
                                <span class="status-badge badge-green">✓ DISETUJUI</span>
                            @elseif($tipe_notif == 'ditolak')
                                <span class="status-badge badge-red">✕ DITOLAK</span>
                            @else
                                <span class="status-badge badge-yellow">⏳ {{ strtoupper($cuti->status) }}</span>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>

            {{-- ==================== CTA BUTTONS ==================== --}}
            @if(in_array($tipe_notif, ['kasubag', 'atasan', 'ketua']))
                <div class="cta-box">
                    <p style="font-size: 13px; color: #555;">Silakan login ke aplikasi untuk memproses pengajuan ini.</p>
                    <a href="{{ route('login') }}" class="btn">🔐 Login & Proses Cuti</a>
                </div>
            @endif

            @if($tipe_notif == 'disetujui')
                <div class="cta-box">
                    <p style="font-size: 13px; color: #555;">Anda dapat mengunduh Surat Cuti resmi melalui aplikasi.</p>
                    <a href="{{ route('login') }}" class="btn">📄 Login & Unduh Surat Cuti</a>
                </div>
            @endif

            @if($tipe_notif == 'ditolak')
                <div class="cta-box">
                    <p style="font-size: 13px; color: #555;">Anda dapat mengajukan ulang cuti melalui aplikasi setelah memperbaiki pengajuan.</p>
                    <a href="{{ route('login') }}" class="btn" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">🔄 Login & Ajukan Ulang</a>
                </div>
            @endif

        </div>
    </div>

    {{-- ==================== FOOTER ==================== --}}
    <div class="footer">
        <p style="font-size: 11px; color: #999;">Email ini dikirim otomatis oleh Sistem E-Cuti PA Sendawar.</p>
        <p style="font-size: 11px; color: #999;">Mohon <strong>tidak membalas</strong> email ini.</p>
        <p style="font-size: 11px; color: #bbb; margin-top: 8px;">&copy; {{ date('Y') }} Pengadilan Agama Sendawar — Semua Hak Dilindungi</p>
    </div>
</body>
</html>
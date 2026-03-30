<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f4f4f4; }
        .container { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: #0f6b3d; color: white; padding: 25px; text-align: center; }
        .content { padding: 30px; }
        .footer { text-align: center; margin-top: 20px; color: #888; font-size: 12px; padding-bottom: 20px; }
        .btn { display: inline-block; background: #0f6b3d; color: white !important; text-decoration: none; padding: 12px 25px; border-radius: 5px; margin-top: 20px; font-weight: bold; }
        .btn-green { background: #0f6b3d; }
        .status-box { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 5px solid #0f6b3d; margin: 20px 0; }
        h1, h2, h3 { margin: 0 0 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Pengadilan Agama Sendawar</h2>
            <p style="margin:0; opacity: 0.9;">Sistem Informasi Cuti Pegawai</p>
        </div>
        
        <div class="content">
            
            @if(in_array($tipe_notif, ['kasubag', 'atasan']))
                <h3>Halo, Bapak/Ibu</h3>
                <h2 style="color: #0f6b3d;">📝 Pengajuan Cuti Baru</h2>
                <p>Pegawai berikut telah mengajukan permohonan cuti dan menunggu tindak lanjut / persetujuan Anda.</p>

            @elseif($tipe_notif == 'ketua')
                <h3>Halo, Bapak/Ibu Ketua</h3>
                <h2 style="color: #0f6b3d;">⚖️ Verifikasi Lanjutan</h2>
                <p>Pengajuan cuti ini telah disetujui dan menunggu persetujuan/tanda tangan akhir dari Anda.</p>

            @elseif($tipe_notif == 'disetujui')
                <h3>Halo, {{ $cuti->user->name ?? 'Pegawai' }}</h3>
                <h2 style="color: #198754;">✅ Cuti Disetujui!</h2>
                <p>Selamat! Pengajuan cuti Anda telah disetujui sepenuhnya dan Surat Cuti telah diterbitkan.</p>

            @elseif($tipe_notif == 'ditolak')
                <h3>Halo, {{ $cuti->user->name ?? 'Pegawai' }}</h3>
                <h2 style="color: #dc3545;">❌ Pengajuan Ditolak</h2>
                <p>Mohon maaf, pengajuan cuti Anda belum dapat disetujui saat ini.</p>
            @endif
            
            <div class="status-box">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 5px 0; width: 120px; color: #666;"><strong>Nama Pegawai</strong></td>
                        <td style="padding: 5px 0;">: {{ $cuti->user->name ?? 'Pegawai' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #666;"><strong>Jenis Cuti</strong></td>
                        <td style="padding: 5px 0;">: {{ $cuti->jenis_cuti }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #666;"><strong>Tanggal</strong></td>
                        <td style="padding: 5px 0;">: {{ date('d M Y', strtotime($cuti->tanggal_mulai)) }} s.d {{ date('d M Y', strtotime($cuti->tanggal_selesai)) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #666;"><strong>Lama</strong></td>
                        <td style="padding: 5px 0;">: {{ $cuti->lama }} Hari Kerja</td>
                    </tr>
                    <tr>
                        <td style="padding: 5px 0; color: #666; vertical-align: top;"><strong>Alasan</strong></td>
                        <td style="padding: 5px 0;">: {{ $cuti->alasan }}</td>
                    </tr>
                </table>
            </div>
            
            @if(in_array($tipe_notif, ['kasubag', 'atasan', 'ketua']))
                <div style="text-align: center;">
                    <p>Silakan login ke aplikasi untuk melihat detail dan melakukan tanda tangan digital.</p>
                    <a href="{{ route('login') }}" class="btn">Login & Proses Cuti</a>
                </div>
            @endif

            @if($tipe_notif == 'disetujui')
                <div style="text-align: center;">
                    <a href="{{ route('login') }}" class="btn">Login & Unduh Surat</a>
                </div>
            @endif

        </div>
    </div>
    
    <div class="footer">
        <p>Email ini dikirim otomatis oleh Sistem e-Cuti PA Sendawar.<br>Mohon tidak membalas email ini.</p>
        <p>&copy; {{ date('Y') }} Pengadilan Agama Sendawar</p>
    </div>
</body>
</html>
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiCuti extends Mailable
{
    use Queueable, SerializesModels;

    public $cuti;
    public $tipe_notif; // 'kasubag', 'atasan', 'ketua', 'disetujui', 'ditolak'

    public function __construct($cuti, $tipe_notif)
    {
        $this->cuti = $cuti;
        $this->tipe_notif = $tipe_notif;
    }

    public function build()
    {
        $subject = '[E-CUTI] Notifikasi Baru';

        switch ($this->tipe_notif) {
            case 'kasubag':
                $subject = '[E-CUTI] 📝 Pengajuan Cuti Baru Menunggu Verifikasi';
                break;
            case 'atasan':
                $subject = '[E-CUTI] ⏳ Permohonan Cuti Bawahan Menunggu Persetujuan';
                break;
            case 'ketua':
                $subject = '[E-CUTI] ⚖️ Permohonan Cuti Menunggu Penetapan Ketua';
                break;
            case 'disetujui':
                $subject = '[E-CUTI] ✅ Hore! Pengajuan Cuti Anda DISETUJUI';
                break;
            case 'ditolak':
                $subject = '[E-CUTI] ❌ Mohon Maaf, Pengajuan Cuti DITOLAK';
                break;
        }

        // Pastikan view 'emails.notifikasi_cuti' sudah ada (dari kode sebelumnya)
        return $this->subject($subject)->view('emails.notifikasi_cuti');
    }
}
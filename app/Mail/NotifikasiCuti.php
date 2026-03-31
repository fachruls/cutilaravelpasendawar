<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotifikasiCuti extends Mailable implements ShouldQueue
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
        $nama = $this->cuti->user->name ?? 'Pegawai';

        $subject = match ($this->tipe_notif) {
            'kasubag'   => "[E-CUTI PA Sendawar] Pengajuan Cuti Baru — $nama — Menunggu Verifikasi Kepegawaian",
            'atasan'    => "[E-CUTI PA Sendawar] Permohonan Cuti $nama — Menunggu Persetujuan Atasan",
            'ketua'     => "[E-CUTI PA Sendawar] Penetapan Akhir Cuti $nama — Menunggu Tanda Tangan Ketua",
            'disetujui' => "[E-CUTI PA Sendawar] Cuti Anda Telah DISETUJUI — Surat Cuti Diterbitkan",
            'ditolak'   => "[E-CUTI PA Sendawar] Pengajuan Cuti Anda DITOLAK",
            default     => "[E-CUTI PA Sendawar] Notifikasi Cuti Pegawai",
        };

        return $this->subject($subject)->view('emails.notifikasi_cuti');
    }
}
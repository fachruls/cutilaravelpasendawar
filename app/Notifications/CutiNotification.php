<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CutiNotification extends Notification
{
    use Queueable;

    public $cuti;
    public $tipe_notif;
    public $pesan;

    public function __construct($cuti, $tipe_notif, $pesan)
    {
        $this->cuti = $cuti;
        $this->tipe_notif = $tipe_notif;
        $this->pesan = $pesan;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'cuti_id' => $this->cuti->id,
            'jenis_cuti' => $this->cuti->jenis_cuti,
            'nama_pegawai' => $this->cuti->user->name ?? 'Unknown',
            'pesan' => $this->pesan,
        ];
    }
}

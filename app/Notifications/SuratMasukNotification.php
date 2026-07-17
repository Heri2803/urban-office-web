<?php

namespace App\Notifications;

use App\Models\Surat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SuratMasukNotification extends Notification
{
    use Queueable;

    protected $surat;

    public function __construct(Surat $surat)
    {
        $this->surat = $surat;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toDatabase($notifiable)
    {
        return [
            'surat_id'     => $this->surat->id,
            'nomor_surat'  => $this->surat->nomor_surat,
            'perihal'      => $this->surat->perihal,
            'pengirim'     => $this->surat->pengirim,
            'tanggal_surat'=> $this->surat->tanggal_surat->format('d/m/Y'),
            'message'      => "Anda menerima surat baru: {$this->surat->perihal}",
            'action_url'   => route('dashboard.surats.show', $this->surat->id),
            'created_by'   => $this->surat->creator?->name ?? 'Admin',
            'created_at'   => now()->toDateTimeString(),
        ];
    }

    /**
     * Get the array representation of the notification (for array/JSON).
     */
    public function toArray($notifiable)
    {
        return $this->toDatabase($notifiable);
    }
}

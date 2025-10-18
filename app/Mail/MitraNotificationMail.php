<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Mitra;

class MitraNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $mitra;
    public $isForUser; // true = email ke user, false = email ke admin

    /**
     * Create a new message instance.
     */
    public function __construct(Mitra $mitra, bool $isForUser = true)
    {
        $this->mitra = $mitra;
        $this->isForUser = $isForUser;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        if ($this->isForUser) {
            // Email untuk user yang mengisi form
            return $this->subject('Terima Kasih - Pengajuan Kemitraan UrbanOffice')
                        ->view('layouts.emails.user-confirmation')
                        ->with([
                            'nama' => $this->mitra->nama_lengkap_ktp,
                            'email' => $this->mitra->alamat_email,
                        ]);
        } else {
            // Email untuk admin
            return $this->subject('Pengajuan Kemitraan Baru - ' . $this->mitra->nama_lengkap_ktp)
                        ->view('layouts.emails.admin-notification')
                        ->with([
                            'mitra' => $this->mitra,
                        ]);
        }
    }
}
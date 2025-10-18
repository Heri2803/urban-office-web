<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;

    public function __construct($otp)
    {
        $this->otp = $otp;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
                    ->replyTo(config('mail.from.address'))
                    ->subject('Kode OTP Reset Password')
                    ->view('layouts.emails.reset-otp')  // Kita buat view selanjutnya
                    ->with(['otp' => $this->otp]);
    }
}

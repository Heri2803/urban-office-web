<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Transaction;

class TransactionSettledMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transaction;

    /**
     * Create a new message instance.
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject("Transaksi Anda Telah Terselesaikan")
                    ->view('emails.transaction_settled')
                    ->with([
                        'transaction' => $this->transaction
                    ])
                    ->attach(public_path("invoices/{$this->transaction->order_id}.pdf")); // pastikan PDF sudah generate
    }
}

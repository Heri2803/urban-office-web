<?php

namespace App\Observers;

use App\Models\Transaction;
use App\Mail\TransactionSettledMail;
use Illuminate\Support\Facades\Mail;

class TransactionObserver
{
    public function updated(Transaction $transaction)
    {
        // cek jika status berubah menjadi settlement
        if ($transaction->isDirty('status') && $transaction->status === 'settlement') {
            Mail::to($transaction->user->email)->send(new TransactionSettledMail($transaction));
        }
    }
}

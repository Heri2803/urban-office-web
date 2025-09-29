<?php

namespace App\Http\Controllers\Mails;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;

class MailController extends Controller
{
    public function mailContent()
{
    $transactions = Transaction::where('user_id', Auth::id())
                               ->orderBy('created_at', 'desc')
                               ->get();

    return view('layouts.dashboard.mails', compact('transactions'));
}

}

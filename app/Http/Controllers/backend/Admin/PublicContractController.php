<?php

namespace App\Http\Controllers\Backend\Admin;

use App\Models\Contract;
use App\Models\Addendum;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PublicContractController extends Controller
{
    // PublicContractController.php
    public function show(string $token)
    {
        $contract = Contract::with([
                'transaction.location.city',
                'transaction.user',
                'invoice',
            ])
            ->where('public_token', $token)
            ->where(function ($q) {
                $q->whereNull('token_expires_at')
                ->orWhere('token_expires_at', '>', now());
            })
            ->firstOrFail();

        $transaction = $contract->transaction;

        // Sesuaikan path view dengan lokasi file
        return view('layouts.publicpage.contract-verify', compact('contract', 'transaction'));
    }

    public function showAddendum(string $token)
    {
        $addendum = Addendum::with([
                'contract.transaction.location.city',
                'contract.transaction.user',
                'invoice',
                'parentAddendum'
            ])
            ->where('public_token', $token)
            ->where(function ($q) {
                $q->whereNull('token_expires_at')
                ->orWhere('token_expires_at', '>', now());
            })
            ->firstOrFail();

        $transaction = $addendum->transaction ?? $addendum->contract->transaction;

        return view('layouts.publicpage.addendum-verify', compact('addendum', 'transaction'));
    }
}
<?php

namespace App\Http\Controllers\Backend\MitraPanel;

use App\Http\Controllers\Controller;

class HistoryPromoController extends Controller
{
    public function index()
    {
        // Menampilkan view history promo
        return view('layouts.mitrapanel.history-promo');
    }
}

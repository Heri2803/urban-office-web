<?php

namespace App\Http\Controllers\Backend\MitraPanel;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('layouts.mitrapanel.dashboard');
    }
}

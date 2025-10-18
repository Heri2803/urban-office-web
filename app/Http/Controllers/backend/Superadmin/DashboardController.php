<?php

namespace App\Http\Controllers\Backend\Superadmin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('layouts.superadmin.dashboard');
    }
}

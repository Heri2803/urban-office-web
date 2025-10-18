<?php

namespace App\Http\Controllers\Backend\MitraPanel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('layouts.mitrapanel.settings');
    }

    public function update(Request $request)
    {
        // contoh dummy untuk testing
        return "Settings updated successfully!";
    }
}

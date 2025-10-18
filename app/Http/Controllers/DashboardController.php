<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;

class DashboardController extends Controller
{
    public function __construct()
    {
        // Pastikan semua method di controller ini memerlukan auth
        $this->middleware('auth');
    }

    public function home()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.home', [
            'user' => $user
        ]);
    }

    public function calls()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.calls', [
            'user' => $user
        ]);
    }

    public function mails()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.mails', [
            'user' => $user
        ]);
    }

    public function invoice()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.invoice', [
            'user' => $user
        ]);
    }

    public function reward()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.reward', [
            'user' => $user
        ]);
    }

    public function bookingForm()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.bookingform', [
            'user' => $user
        ]);
    }

    public function bookingInvoice()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.bookinginvoice', [
            'user' => $user
        ]);
    }

    public function mitra()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.mitra', [
            'user' => $user
        ]);
    }

    public function prosesMitra()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.prosesmitra', [
            'user' => $user
        ]);
    }

    public function mitraForm()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.mitraform', [
            'user' => $user
        ]);
    }

    public function profile()
    {
        $user = AuthController::getUser();
        return view('layouts.dashboard.profile', [
            'user' => $user
        ]);
    }
}
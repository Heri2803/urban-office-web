<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name'      => $googleUser->getName(),
                    'email'     => $googleUser->getEmail(),
                    'password'  => bcrypt(uniqid()),
                    'google_id' => $googleUser->getId(),
                ]);
            }

            Auth::login($user);

            return redirect('/dashboard/home');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Google login gagal, coba lagi.');
        }
    }
}

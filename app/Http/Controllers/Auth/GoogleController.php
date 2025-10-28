<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Hash;
use Exception;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah user sudah pernah login sebelumnya
            $user = User::where('google_id', $googleUser->id)->first();

            if (!$user) {
                // Jika belum, buat akun baru
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(uniqid()), // password acak
                ]);
            }

            // Login dengan guard web (user)
            Auth::guard('web')->login($user);

            return redirect()->route('home');
        } catch (Exception $e) {
            return redirect('/login')->with('error', 'Login Google gagal. ' . $e->getMessage());
        }
    }
}

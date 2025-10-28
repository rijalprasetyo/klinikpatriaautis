<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

class LoginController extends Controller
{
    /** LOGIN BIASA */
    public function showLoginForm() {
        return view('auth.login');
    }

    public function login(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();
            return redirect()->intended('home');
        }

        return back()->withErrors(['email' => 'Email atau password salah.']);
    }

    /** REGISTER BIASA */
    public function showRegisterForm() {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'alamat'     => 'required|string|max:255',
            'tgl_lahir'  => 'required|date',
            'email'      => 'required|string|email|max:255|unique:users',
            'no_hp'      => 'required|string|min:10|max:15',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'       => $request->name,
            'alamat'     => $request->alamat,
            'tgl_lahir'  => $request->tgl_lahir,
            'email'      => $request->email,
            'no_hp'      => $request->no_hp,
            'password'   => Hash::make($request->password),
        ]);

        // Setelah registrasi, arahkan ke halaman login user
        return redirect()
            ->route('user.login.submit')
            ->with('success', 'Registrasi berhasil! Silakan login untuk melanjutkan.');
    }


    /** GOOGLE LOGIN */
    public function redirectToGoogle() {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback() {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'password' => Hash::make(uniqid()), // password random
                    'google_id' => $googleUser->getId(),
                ]);
            }

            Auth::login($user);
            return redirect('home');
        } catch (\Exception $e) {
            return redirect('/login')->withErrors(['google' => 'Login Google gagal!']);
        }
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}

<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name'       => 'required|string|max:255',
            'alamat'     => 'required|string|max:255',
            'tgl_lahir'  => 'required|date',
            'email'      => 'required|email|unique:users,email',
            'no_hp'      => 'required|string|min:10|max:15',
            'password'   => 'required|min:6|confirmed',
        ]);

        User::create([
            'name'       => $request->name,
            'alamat'     => $request->alamat,
            'tgl_lahir'  => $request->tgl_lahir,
            'email'      => $request->email,
            'no_hp'      => $request->no_hp,
            'password'   => Hash::make($request->password),
        ]);

        // Arahkan ke halaman login user, bukan langsung home
        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login untuk melanjutkan.');
    }
}


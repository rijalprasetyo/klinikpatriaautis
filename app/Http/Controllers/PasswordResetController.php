<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    // FORM LUPA PASSWORD
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // KIRIM OTP KE EMAIL
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->with('error', 'Email tidak ditemukan');
        }

        $otp = rand(100000, 999999);

        DB::table('password_otps')->updateOrInsert(
            ['email' => $request->email],
            ['otp' => $otp, 'expires_at' => Carbon::now()->addMinutes(10), 'created_at' => now(), 'updated_at' => now()]
        );

        // Kirim OTP ke email
        Mail::raw("
        Halo,

        Anda baru saja meminta untuk mengatur ulang kata sandi akun Anda di Klinik Patria.

        Kode OTP (One-Time Password) Anda adalah:

            $otp

        Kode ini hanya berlaku selama 10 menit.

        Jika Anda tidak merasa melakukan permintaan ini, silakan abaikan email ini.

        Terima kasih,
        Tim Layanan Klinik Patria
        ", function ($message) use ($request) {
            $message->to($request->email)
                    ->subject('Kode Verifikasi Reset Password | Klinik Patria');
        });


        return redirect()->route('password.verify.form')->with(['email' => $request->email, 'success' => 'OTP telah dikirim ke email Anda']);
    }

    // FORM INPUT OTP
    public function showVerifyForm(Request $request)
    {
        $email = session('email');
        if (!$email) return redirect()->route('password.forgot.form');
        return view('auth.verify-otp', compact('email'));
    }

    // CEK OTP
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6',
        ]);

        $otpData = DB::table('password_otps')->where('email', $request->email)->first();

        if (!$otpData || $otpData->otp !== $request->otp) {
            return back()->with('error', 'Kode OTP salah');
        }

        if (Carbon::now()->greaterThan($otpData->expires_at)) {
            return back()->with('error', 'Kode OTP sudah kedaluwarsa');
        }

        // Simpan email ke session agar bisa ubah password
        session(['reset_email' => $request->email]);

        return redirect()->route('password.reset.form');
    }

    // FORM UBAH PASSWORD BARU
    public function showResetForm()
    {
        $email = session('reset_email');
        if (!$email) return redirect()->route('password.forgot.form');
        return view('auth.reset-password', compact('email'));
    }

    // SIMPAN PASSWORD BARU
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) return back()->with('error', 'Email tidak ditemukan');

        $user->update(['password' => Hash::make($request->password)]);

        // Hapus OTP setelah digunakan
        DB::table('password_otps')->where('email', $request->email)->delete();

        session()->forget(['reset_email', 'email']);

        return redirect()->route('user.login')->with('success', 'Password berhasil diubah, silakan login.');
    }
}

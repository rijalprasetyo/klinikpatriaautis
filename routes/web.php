<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DokterController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DataBackupKontroler;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// Halaman landing
Route::get('/', [LandingController::class, 'index'])->name('index');

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::get('/register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [LoginController::class, 'register']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// LOGIN GOOGLE
Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('login.google');
Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback']);

// LUPA PASSWORD
Route::get('/forgot-password', [PasswordResetController::class, 'showForgotForm'])->name('password.forgot.form');
Route::post('/forgot-password', [PasswordResetController::class, 'sendOtp'])->name('password.send.otp');
Route::get('/verify-otp', [PasswordResetController::class, 'showVerifyForm'])->name('password.verify.form');
Route::post('/verify-otp', [PasswordResetController::class, 'verifyOtp'])->name('password.verify');
Route::get('/reset-password', [PasswordResetController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [PasswordResetController::class, 'updatePassword'])->name('password.reset');


// ADMIN
Route::get('/admin/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

Route::middleware('auth:admin')->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
    Route::get('/admin/pasien', [AdminController::class, 'dataPasienScheduled'])->name('admin.data-pasien');
    Route::get('/admin/pasien/{id}/detail', [AdminController::class, 'getPasienDetail'])->name('admin.pasien.detail');
    Route::post('/admin/pasien/{id}/update-berkas', [AdminController::class, 'updateStatusBerkas'])->name('admin.pasien.update-berkas');
    Route::get('/admin/verifikasi-berkas', [AdminController::class, 'verifikasiBerkas'])->name('admin.verifikasi-berkas');
    Route::get('/admin/master/pelayanan', [AdminController::class, 'dataMasterPelayanan'])->name('admin.pelayanan');
    Route::post('/admin/master/pelayanan', [AdminController::class, 'storePelayanan'])->name('admin.pelayanan.store');
    Route::delete('/admin/master/pelayanan/{id}', [AdminController::class, 'destroyPelayanan'])->name('admin.pelayanan.destroy');
    Route::get('/admin/master/jam-pelayanan', [AdminController::class, 'dataMasterJamPelayanan'])->name('admin.jam-pelayanan');
    Route::post('/admin/master/jam-pelayanan', [AdminController::class, 'storeJamPelayanan'])->name('admin.jam-pelayanan.store');
    Route::delete('/admin/master/jam-pelayanan/{id}', [AdminController::class, 'destroyJamPelayanan'])->name('admin.jam-pelayanan.destroy');
    Route::get('/admin/master/users/{type?}', [AdminController::class, 'dataMasterUser'])->name('admin.master.users')->where('type', 'user|dokter|admin'); 
    Route::post('/admin/dokter/store', [AdminController::class, 'storeDokter'])->name('admin.dokter.store');
    Route::post('/admin/admin/store', [AdminController::class, 'storeAdmin'])->name('admin.admin.store');
    Route::put('/admin/dokter/{id}/update-status', [AdminController::class, 'updateStatusDokter'])->name('admin.dokter.updateStatus');
    Route::delete('/admin/dokter/{id}', [AdminController::class, 'deleteDokter'])->name('admin.delete-dokter');
    Route::delete('/admin/admin/{id}', [AdminController::class, 'deleteAdmin'])->name('admin.delete-admin');
    Route::get('/admin/data-backup', [DataBackupKontroler::class, 'index'])->name('admin.data-backup');
    Route::post('/admin/data-backup', [DataBackupKontroler::class, 'backup'])->name('admin.data-backup.store');
    Route::get('/admin/riwayat-pasien', [AdminController::class, 'riwayatPasien'])->name('admin.riwayat-pasien');
    Route::get('/admin/pasien/{id}/detail', [AdminController::class, 'getDetailPasien'])->name('admin.pasien.detail');
    Route::get('/admin/pasien/{id}/catatan', [AdminController::class, 'getCatatan'])->name('admin.pasien.get-catatan');
    Route::post('/admin/data-backup', [DataBackupKontroler::class, 'backup'])->name('admin.data-backup.store');
});

// DOKTER
Route::get('/dokter/login', [DokterController::class, 'showLoginForm'])->name('dokter.login');
Route::post('/dokter/login', [DokterController::class, 'login'])->name('dokter.login.submit');
Route::middleware('auth:dokter')->group(function () {
    Route::get('/dokter/dashboard', [DokterController::class, 'dashboard'])->name('dokter.dashboard');
    Route::post('/dokter/logout', [DokterController::class, 'logout'])->name('dokter.logout');
    Route::get('/dokter/pasien', [DokterController::class, 'dataPasienScheduled'])->name('dokter.data-pasien');
    Route::get('/dokter/pasien/{id}/detail', [DokterController::class, 'getPasienDetail'])->name('dokter.pasien.detail');
    Route::put('/dokter/{id}/update-pemeriksaan', [DokterController::class, 'updateStatusPemeriksaan'])->name('dokter.pasien.update-pemeriksaan');
    Route::post('/dokter/{id}/upload-videos', [DokterController::class, 'uploadVideos'])->name('dokter.pasien.upload-videos');
    Route::post('/dokter/{id}/delete-video', [DokterController::class, 'deleteVideo'])->name('dokter.pasien.delete-video');
    Route::get('/dokter/{id}/catatan', [DokterController::class, 'getCatatan'])->name('dokter.pasien.get-catatan');
    Route::post('/dokter/{id}/catatan/update', [DokterController::class, 'updateCatatan'])->name('dokter.pasien.update-catatan');
    Route::post('/dokter/{id}/catatan/delete', [DokterController::class, 'deleteCatatan'])->name('dokter.pasien.delete-catatan');
    Route::get('/dokter/riwayat', [DokterController::class, 'riwayatPasien'])->name('dokter.riwayat-pasien');
});

// USER
Route::get('/user/login', [UserController::class, 'showLoginForm'])->name('user.login');
Route::post('/user/login', [UserController::class, 'login'])->name('user.login.submit');
Route::middleware('auth:web')->group(function () {
    Route::get('/login', [UserController::class, 'showLoginForm'])->name('user.login');
    Route::post('/login', [UserController::class, 'login'])->name('user.login.submit');
    Route::get('/home', [UserController::class, 'home'])->name('home')->middleware('auth');
    Route::post('/logout', [UserController::class, 'logout'])->name('logout');
    Route::get('/pendaftaran', [UserController::class, 'showForm'])->name('pendaftaran.form');
    Route::get('/pendaftaran', [UserController::class, 'create'])->name('pendaftaran.create');
    Route::post('/pendaftaran', [UserController::class, 'store'])->name('pendaftaran.store');
    Route::get('/pendaftaran/success/{antrian}', [UserController::class, 'success'])->name('pendaftaran.success');
    Route::get('/pendaftaran/download/{id}', [UserController::class, 'downloadPdf'])->name('pendaftaran.download');
    Route::get('/biodata', [UserController::class, 'biodata'])->name('biodata');
    Route::post('/biodata', [UserController::class, 'updateBiodata'])->name('biodata.update');
    Route::get('/riwayat-kunjungan', [UserController::class, 'riwayatPasien'])->name('riwayat-kunjungan');
    Route::get('/pasien/{id}/detail', [UserController::class, 'getDetailPasien'])->name('pasien.detail');
    Route::get('/pasien/{id}/catatan', [UserController::class, 'getCatatan'])->name('pasien.get-catatan');
    Route::post('/pasien/{id}/feedback', [UserController::class, 'submitFeedback'])->name('pasien.submit-feedback');
});


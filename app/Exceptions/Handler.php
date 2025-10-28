<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Session\TokenMismatchException;

class Handler extends ExceptionHandler
{
    /**
     * Daftar jenis exception yang tidak perlu dilaporkan.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [];

    /**
     * Daftar input yang tidak akan pernah disertakan dalam validasi.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Tangani exception yang terjadi.
     */
    public function register(): void
    {
        //
    }

    /**
     * Tangani error yang dikirimkan ke user.
     */
    public function render($request, Throwable $exception)
    {
        // Tangani jika session habis (TokenMismatchException)
        if ($exception instanceof TokenMismatchException) {
            return redirect()->route('user.login')->withErrors([
                'session' => 'Sesi Anda telah habis. Silakan login kembali.'
            ]);
        }

        return parent::render($request, $exception);
    }
}

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Klinik Patria</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-blue: #007bff;
            --primary-blue-dark: #0056b3;
            --white: #ffffff;
            --text-dark: #333;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--white);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1.5rem;
        }

        .login-container {
            display: flex;
            flex-direction: row;
            height: 80vh;
            width: 80%;
            max-width: 950px;
            background-color: var(--white);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            overflow: hidden;
        }

        /* BAGIAN KIRI (BIRU) */
        .blue-section {
            background: var(--primary-blue);
            color: var(--white);
            flex: 0 0 38%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
        }

        .blue-section h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .blue-section p {
            font-size: 0.95rem;
            opacity: 0.9;
        }

        /* BAGIAN KANAN (FORM) */
        .white-section {
            flex: 1;
            padding: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
        }

        .login-card {
            width: 100%;
            max-width: 420px;
        }

        .card-header {
            font-size: 1.6rem;
            font-weight: 600;
            text-align: center;
            margin-bottom: 1.5rem;
            color: var(--text-dark);
        }

        .form-label {
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.25rem rgba(0,123,255,0.25);
        }

        .password-input-group {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted);
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border: none;
            border-radius: 50px;
            font-weight: 600;
            padding: 0.75rem;
            transition: 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--primary-blue-dark);
            transform: translateY(-2px);
        }

        .btn-outline-dark, .btn-outline-danger {
            border-radius: 50px;
            font-weight: 600;
            padding: 0.7rem 1rem;
        }

        .divider {
            position: relative;
            text-align: center;
            margin: 1.5rem 0;
            color: var(--text-muted);
        }

        .divider::before {
            content: '';
            position: absolute;
            width: 100%;
            height: 1px;
            top: 50%;
            left: 0;
            background: var(--border-color);
        }

        .divider span {
            position: relative;
            background: var(--white);
            padding: 0 1rem;
        }

        .btn-link-back {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
        }

        .btn-link-back:hover {
            color: var(--primary-blue-dark);
        }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 991px) {
            .login-container {
                width: 95%;
                height: auto;
                flex-direction: column;
            }

            .blue-section {
                flex: none;
                height: auto;
                padding: 2rem 1.5rem;
            }

            .blue-section h2 {
                font-size: 1.7rem;
                margin-bottom: 0.5rem;
            }

            .blue-section p {
                font-size: 0.85rem;
                margin-bottom: 0;
            }

            .white-section {
                padding: 2rem 1.5rem;
                flex: none;
                overflow-y: visible;
            }

            .login-card {
                max-width: 100%;
            }

            .card-header {
                font-size: 1.4rem;
            }
        }

        /* Ukuran kecil (HP 480px ke bawah) */
        @media (max-width: 480px) {
            body {
                padding: 0.5rem;
            }

            .login-container {
                border-radius: 12px;
                width: 100%;
            }

            .blue-section {
                padding: 1.5rem 1rem;
            }

            .white-section {
                padding: 1.2rem 1rem;
            }

            .btn-primary,
            .btn-outline-dark,
            .btn-outline-danger {
                font-size: 0.9rem;
                padding: 0.6rem;
            }

            .toggle-password {
                right: 10px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <!-- BAGIAN KIRI -->
        <div class="blue-section">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" fill="none" stroke="currentColor" stroke-width="2" class="mb-3 text-white" viewBox="0 0 24 24">
                <path d="M12 2v6M12 16v6M19 9h3M2 9h3M19 15h3M2 15h3M9 2v3M15 2v3M9 19v3M15 19v3"/>
                <circle cx="12" cy="12" r="4"/>
            </svg>
            <h2>Selamat Datang</h2>
            <p>Masuk untuk mengakses dashboard <strong>Klinik Patria</strong></p>
        </div>

        <!-- BAGIAN KANAN -->
        <div class="white-section">
            <div class="login-card">

                {{-- ALERT SUCCESS --}}
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- ALERT ERROR --}}
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="card-header">Login ke Akun Anda</div>

                <form method="POST" action="{{ route('user.login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" class="form-control" required autofocus value="{{ old('email') }}">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="password-input-group">
                            <input id="password" type="password" name="password" class="form-control" required>
                            <i class="fas fa-eye toggle-password"></i>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember">
                            <label class="form-check-label text-muted" for="remember">Ingat Saya</label>
                        </div>
                        <a href="{{ route('password.forgot.form') }}" class="text-decoration-none text-primary small">Lupa Password?</a>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </div>
                </form>

                <div class="divider"><span>atau</span></div>

                <div class="d-grid gap-3">
                    <a href="{{ route('register') }}" class="btn btn-outline-dark d-flex align-items-center justify-content-center">
                        <i class="fa-solid fa-user-plus me-2"></i> Daftar Akun
                    </a>

                    <a href="{{ route('login.google') }}" class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2">
                        <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20" height="20">
                        Masuk dengan Google
                    </a>
                </div>

                <div class="text-center mt-4">
                    <a href="{{ url('/') }}" class="btn-link-back">
                        <i class="fa-solid fa-arrow-left me-1"></i> Kembali ke Halaman Utama
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInput = document.getElementById('password');
            const togglePassword = document.querySelector('.toggle-password');

            togglePassword.addEventListener('click', function () {
                const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                passwordInput.setAttribute('type', type);
                this.classList.toggle('fa-eye-slash');
            });
        });
    </script>
</body>
</html>

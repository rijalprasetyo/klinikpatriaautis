<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Klinik Patria</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: auto; /* aktifkan scroll pada body */
            padding: 20px; /* beri sedikit ruang agar kontainer tidak menempel */
        }

        .register-container {
            display: flex;
            min-height: 80vh; /* ubah dari height ke min-height */
            width: 80%;
            max-width: 900px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
        }

        .blue-section {
            background: var(--primary-blue);
            color: var(--white);
            flex: 0 0 35%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 2rem;
            position: relative;
        }

        .blue-section h2 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        .blue-section p {
            font-weight: 400;
            opacity: 0.8;
        }
        
        .white-section {
            background: var(--white);
            flex: 0 0 65%;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: flex-start; /* ubah agar konten mulai dari atas */
            overflow-y: auto; /* scroll vertikal aktif */
            max-height: 80vh; /* batasi tinggi agar tidak melebihi viewport */
        }

        .register-card {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }

        .register-card .card-header {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: center;
            color: var(--text-dark);
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 500;
            color: var(--text-dark);
        }

        .form-control {
            border-radius: 10px;
            padding: 0.75rem 1rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-blue);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-blue-dark);
            transform: translateY(-2px);
        }

        .btn-outline {
            border-radius: 50px;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }
        
        .btn-outline-danger {
            border: 2px solid #dc3545;
            color: #dc3545;
            transition: all 0.3s ease;
        }
        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: var(--white);
        }
        .btn-outline-dark {
            border: 2px solid #343a40;
            color: #343a40;
            transition: all 0.3s ease;
        }
        .btn-outline-dark:hover {
            background-color: #343a40;
            color: var(--white);
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
            top: 50%;
            left: 0;
            width: 100%;
            height: 1px;
            background-color: var(--border-color);
            z-index: 1;
        }
        .divider span {
            position: relative;
            background-color: var(--white);
            padding: 0 1rem;
            z-index: 2;
        }
        
        .btn-link-back {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        .btn-link-back:hover {
            color: var(--primary-blue-dark);
        }
        
        .password-input-group {
            position: relative;
        }

        .password-input-group .form-control {
            padding-right: 3rem;
        }
        
        .password-input-group .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--text-muted);
        }
        
        .invalid-feedback {
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                width: 90%;
                min-height: auto;
                margin: 2rem 0;
            }

            .blue-section {
                flex: none;
                height: auto;
                padding: 2rem;
            }

            .white-section {
                flex: none;
                padding: 2rem;
                overflow-y: auto;
                max-height: none;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="blue-section">
            <svg xmlns="http://www.w3.org/2000/svg" width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mb-3 text-white">
                <path d="M12 2L12 8"></path><path d="M12 16L12 22"></path><path d="M19 9L22 9"></path><path d="M2 9L5 9"></path>
                <path d="M19 15L22 15"></path><path d="M2 15L5 15"></path><path d="M9 2L9 5"></path><path d="M15 2L15 5"></path>
                <path d="M9 19L9 22"></path><path d="M15 19L15 22"></path><circle cx="12" cy="12" r="4"></circle>
            </svg>
            <h2>Daftar Akun</h2>
            <p>Daftar sekarang untuk mengakses layanan fisioterapi terbaik kami.</p>
        </div>

        <div class="white-section">
            <div class="register-card">
                <div class="card-header">
                    Buat Akun Baru
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input id="name" type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat Lengkap</label>
                            <input id="alamat" type="text" 
                                   class="form-control @error('alamat') is-invalid @enderror" 
                                   name="alamat" value="{{ old('alamat') }}" required autocomplete="alamat">
                            @error('alamat')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                            <input id="tgl_lahir" type="date" 
                                   class="form-control @error('tgl_lahir') is-invalid @enderror" 
                                   name="tgl_lahir" value="{{ old('tgl_lahir') }}" required>
                            @error('tgl_lahir')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input id="email" type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   name="email" value="{{ old('email') }}" required autocomplete="email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="no_hp" class="form-label">Nomor HP / WA</label>
                            <input id="no_hp" type="text" 
                                   class="form-control @error('no_hp') is-invalid @enderror" 
                                   name="no_hp" value="{{ old('no_hp') }}" required placeholder="contoh: 081234567890">
                            @error('no_hp')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Kata Sandi</label>
                            <div class="password-input-group">
                                <input id="password" type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" required autocomplete="new-password">
                                <i class="fas fa-eye toggle-password"></i>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password-confirm" class="form-label">Konfirmasi Kata Sandi</label>
                            <div class="password-input-group">
                                <input id="password-confirm" type="password" 
                                       class="form-control" name="password_confirmation" required autocomplete="new-password">
                                <i class="fas fa-eye toggle-password"></i>
                            </div>
                        </div>

                        <div class="d-grid mb-3 mt-4">
                            <button type="submit" class="btn btn-primary">
                                Daftar Sekarang
                            </button>
                        </div>
                    </form>

                    <div class="divider"><span>atau</span></div>

                    <div class="d-grid gap-3">
                        <a href="{{ route('login.google') }}" class="btn btn-outline-danger d-flex align-items-center justify-content-center gap-2">
                            <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20" height="20">
                            Daftar dengan Google
                        </a>
                    </div>
                    
                    <div class="text-center mt-3">
                        Sudah punya akun? <a href="{{ route('user.login.submit') }}" class="btn-link-back">Masuk di sini</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const passwordInputs = document.querySelectorAll('.password-input-group input');
            const togglePasswords = document.querySelectorAll('.toggle-password');

            togglePasswords.forEach((toggle, index) => {
                toggle.addEventListener('click', function () {
                    const input = passwordInputs[index];
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    this.classList.toggle('fa-eye-slash');
                });
            });
        });
    </script>
</body>

</html>
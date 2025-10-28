<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Klinik Patria</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-blue: #007bff;
            --primary-blue-dark: #0056b3;
            --white: #fff;
            --text-dark: #333;
            --bg-color: #f8f9fc;
            --border-color: #dee2e6;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh; 
            padding: 2rem 1rem;
            margin: 0;
        }

        .login-container {
            display: flex;
            max-width: 900px;
            width: 100%;
            min-height: 500px;
            background: var(--white);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 20px;
            overflow: hidden;
        }

        .blue-section {
            background-color: var(--primary-blue);
            color: var(--white);
            flex: 0 0 40%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        .blue-section h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        .blue-section p {
            opacity: 0.9;
        }

        .form-section {
            flex: 0 0 60%;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow-y: auto; 
        }

        .login-card {
            max-width: 400px;
            width: 100%;
            margin: 0 auto;
        }

        .login-card h4 {
            font-weight: 600;
            color: var(--text-dark);
            text-align: center;
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
            padding: 0.75rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-blue-dark);
            transform: translateY(-2px);
        }

        .btn-outline-secondary {
            border-radius: 50px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: var(--white);
        }

        /* Password toggle button */
        .password-wrapper {
            position: relative;
        }
        .password-wrapper .toggle-password {
            position: absolute;
            top: 50%;
            right: 1.25rem; /* Menyesuaikan posisi ke kanan */
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            font-size: 1rem;
            transition: color 0.2s ease-in-out; /* Animasi warna */
        }
        .password-wrapper .toggle-password:hover {
            color: var(--primary-blue); /* Warna saat hover */
        }
        .password-wrapper .form-control {
            padding-right: 3rem; /* Memberi ruang untuk ikon mata */
        }

        /* Media Query untuk Mobile */
        @media (max-width: 768px) {
            body {
                padding: 1rem 0.5rem;
            }
            .login-container {
                flex-direction: column;
                min-height: auto;
                border-radius: 10px;
                box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            }

            .blue-section {
                flex: none;
                height: 20vh; /* Lebih kecil di HP */
                padding: 1.5rem 1rem;
                border-radius: 10px 10px 0 0;
            }

            .blue-section h2 {
                font-size: 1.5rem; /* teks lebih kecil */
            }

            .form-section {
                flex: none;
                padding: 1.5rem 1rem;
                overflow-y: visible;
            }

            .login-card {
                max-width: 300px; /* kotak lebih kecil di HP */
                width: 100%;
                margin: 0 auto;
            }

            /* Ikon mata lebih kecil dan posisinya pas */
            .password-wrapper .toggle-password {
                font-size: 0.85rem;
                right: 0.75rem;
            }
            .password-wrapper .form-control {
                padding-right: 2.5rem; /* Memberi ruang lebih kecil untuk ikon di HP */
            }
        }

        /* Media Query untuk Tablet (opsional, jika ingin ukuran spesifik) */
        @media (min-width: 769px) and (max-width: 991px) {
            .login-container {
                max-width: 700px; /* Ukuran kotak login disesuaikan */
            }
            .blue-section {
                flex: 0 0 35%; /* Proporsi blue section bisa disesuaikan */
            }
            .form-section {
                flex: 0 0 65%;
                padding: 2.5rem;
            }
            .login-card {
                max-width: 350px;
            }
             .password-wrapper .toggle-password {
                right: 1rem; 
            }
        }
    </style>
</head>
<body>
    
    <div class="login-container">
        <div class="blue-section">
            <i class="fas fa-user-shield fa-3x mb-3"></i>
            <h2>Login Admin</h2>
            <p>Masuk ke panel admin untuk mengelola sistem Klinik Patria.</p>
        </div>

        <div class="form-section">
            <div class="login-card">
                <h4>Selamat Datang Kembali</h4>

                <form method="POST" action="{{ route('admin.login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input id="username" type="text" name="username" class="form-control" required autofocus>
                    </div>

                    <div class="mb-4 password-wrapper">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" class="form-control" required>
                        <i class="fas fa-eye-slash toggle-password" id="togglePassword"></i> </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </div>

                    <div class="text-center">
                        <a href="/" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            // Mengubah ikon mata
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>
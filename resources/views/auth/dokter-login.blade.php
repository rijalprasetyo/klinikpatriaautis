<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Dokter - Klinik Patria</title>
    
    <!-- Link CSS Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Link Font Awesome (Icons) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <!-- Link Font Poppins -->
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
                height: 25vh;
                padding: 2rem 1rem;
                border-radius: 10px 10px 0 0;
            }

            .form-section {
                flex: none;
                padding: 2rem 1.5rem;
                overflow-y: visible;
            }
            
            .login-card {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    
    <div class="login-container">
        <!-- Bagian kiri -->
        <div class="blue-section">
            <i class="fas fa-user-md fa-3x mb-3"></i>
            <h2>Login Dokter</h2>
            <p>Masuk ke sistem untuk mengelola jadwal dan pasien Klinik Patria.</p>
        </div>

        <!-- Bagian kanan -->
        <div class="form-section">
            <div class="login-card">
                <h4>Selamat Datang Dokter</h4>

                @if(session('error'))
                    <div class="alert alert-danger text-center">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('dokter.login.submit') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input id="username" type="text" name="username" class="form-control" required autofocus>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password" class="form-control" required>
                    </div>

                    <div class="d-grid mb-3">
                        <button type="submit" class="btn btn-primary">Masuk</button>
                    </div>

                    <div class="text-center">
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Kembali ke Halaman Utama
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

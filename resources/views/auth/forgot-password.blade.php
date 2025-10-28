<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Klinik Patria</title>
    
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
            background-color: #f8f9fa; /* Lebih terang dari 'bg-light' */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .password-reset-card {
            max-width: 450px;
            width: 90%;
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            background-color: var(--white);
        }

        .card-header-custom {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary-blue);
            text-align: center;
            margin-bottom: 0.5rem;
        }

        .card-subtitle-custom {
            text-align: center;
            color: var(--text-muted);
            margin-bottom: 2rem;
            font-weight: 400;
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

        .btn-link-back {
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-link-back:hover {
            color: var(--primary-blue-dark);
        }
    </style>
</head>
<body>
    
    <div class="password-reset-card">
        
        <div class="text-center mb-3">
             <i class="fas fa-lock fa-3x text-primary mb-3"></i>
        </div>

        <div class="card-header-custom">
            Lupa Password
        </div>
        <p class="card-subtitle-custom">
            Masukkan email Anda untuk menerima kode OTP dan mengatur ulang password.
        </p>

        @if(session('error')) 
            <div class="alert alert-danger text-center">{{ session('error') }}</div> 
        @endif
        @if(session('success')) 
            <div class="alert alert-success text-center">{{ session('success') }}</div> 
        @endif

        <form method="POST" action="{{ route('password.send.otp') }}">
            @csrf
            
            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input id="email" type="email" name="email" class="form-control" required autocomplete="email" autofocus>
            </div>
            
            <div class="d-grid mb-4">
                <button type="submit" class="btn btn-primary">
                    Kirim Kode OTP
                </button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('user.login') }}" class="btn-link-back">
                <i class="fas fa-arrow-left"></i> Kembali ke Halaman Login
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
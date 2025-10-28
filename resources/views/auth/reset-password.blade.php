<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Klinik Patria</title>
    
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
            background-color: #f8f9fa;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .reset-card {
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

        .btn-primary-custom {
            background-color: var(--primary-blue);
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary-custom:hover {
            background-color: var(--primary-blue-dark);
            transform: translateY(-2px);
        }

        /* Password Toggle Styling */
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
    </style>
</head>
<body>
    
    <div class="reset-card">
        
        <div class="text-center mb-3">
             <i class="fas fa-unlock-alt fa-3x text-primary mb-3"></i>
        </div>

        <div class="card-header-custom">
            Ubah Password Baru
        </div>
        <p class="card-subtitle-custom">
            Silakan masukkan password baru Anda. Pastikan password kuat dan mudah diingat.
        </p>

        @if(session('error')) 
            <div class="alert alert-danger text-center">{{ session('error') }}</div> 
        @endif
        @if(session('status')) 
            <div class="alert alert-success text-center">{{ session('status') }}</div> 
        @endif

        <form method="POST" action="{{ route('password.reset') }}">
            @csrf
            
            <input type="hidden" name="email" value="{{ $email ?? '' }}">

            <div class="mb-3">
                <label for="password" class="form-label">Password Baru</label>
                <div class="password-input-group">
                    <input id="password" type="password" name="password" class="form-control @error('password') is-invalid @enderror" required autocomplete="new-password">
                    <i class="fas fa-eye toggle-password" data-target="password"></i>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="mb-4">
                <label for="password-confirm" class="form-label">Konfirmasi Password</label>
                <div class="password-input-group">
                    <input id="password-confirm" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                    <i class="fas fa-eye toggle-password" data-target="password-confirm"></i>
                </div>
            </div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="fas fa-save me-2"></i> Simpan Password
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toggle-password').forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    
                    if (passwordInput) {
                        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        passwordInput.setAttribute('type', type);
                        this.classList.toggle('fa-eye-slash');
                    }
                });
            });
        });
    </script>
</body>
</html>
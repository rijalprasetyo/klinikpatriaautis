<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna - Klinik Patria</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/logoklinik.png') }}">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
    :root {
        --primary-blue: #007bff;
        --primary-dark: #0056b3;
        --bg-light: #f8f9fc;
        --white: #ffffff; 
        --sidebar-width: 250px; 
    }

    body {
        font-family: 'Poppins', sans-serif;
        background-color: var(--bg-light);
        margin: 0;
        min-height: 100vh;
        overflow-x: hidden;
    }

    /* ====================== SIDEBAR ====================== */
    .sidebar {
        width: var(--sidebar-width);
        background: var(--primary-blue);
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 5px 0 15px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
        z-index: 1050;
    }

    .sidebar-header {
        text-align: center;
        padding: 1.5rem 1rem 1rem 1rem;
        background-color: var(--primary-dark);
    }
    .sidebar-logo {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 10px auto;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .sidebar-logo img { width: 100%; height: 100%; object-fit: contain; }
    .sidebar-header h4 { font-size: 1rem; font-weight: 700; margin: 0; }

    .nav-links a {
        display: block;
        color: white;
        text-decoration: none;
        padding: 1rem 1.5rem;
        transition: all 0.3s ease;
        font-weight: 500;
        border-left: 5px solid transparent;
    }
    .nav-links a:hover,
    .nav-links a.active {
        background: rgba(255, 255, 255, 0.2);
        border-left: 5px solid var(--white);
    }
    .nav-links i { margin-right: 10px; }

    .user-info-section {
        background-color: rgba(0,0,0,0.1);
        padding: 0.75rem 1rem;
        margin: 1rem;
        border-radius: 8px;
        text-align: center;
    }
    .user-info-section .user-name {
        font-weight: 600;
        font-size: 0.95rem;
        margin-bottom: 5px;
        color: var(--white);
    }
    .user-info-section .btn {
        font-size: 0.8rem;
        padding: 4px 12px;
    }

    /* ====================== MAIN CONTAINER (Desktop) ====================== */
    .main-container {
        margin-left: var(--sidebar-width);
        transition: margin-left 0.3s ease;
        padding: 0;
    }
    .content {
        background: white;
        min-height: 100vh;
        padding: 2rem;
        border-top-left-radius: 30px;
        box-shadow: -10px 0 25px rgba(0, 0, 0, 0.05);
    }

    /* ====================== TOPBAR (MOBILE) ====================== */
    .topbar {
        display: none;
        background: var(--primary-blue);
        color: white;
        padding: 0.75rem 1rem;
        align-items: center;
        justify-content: space-between;
    }
    .topbar h5 {
        margin: 0;
        font-weight: 600;
    }
    .toggle-btn {
        background: none;
        border: none;
        color: white;
        font-size: 1.4rem;
    }

    /* ====================== RESPONSIVE ====================== */
    @media (max-width: 992px) {
        .sidebar {
            left: -250px;
        }
        .sidebar.active {
            left: 0;
        }
        .main-container {
            margin-left: 0;
        }
        .topbar {
            display: flex;
        }
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.4);
            z-index: 1049;
        }
        .overlay.active {
            display: block;
        }
        
        /* PENYESUAIAN KHUSUS MOBILE */
        .content {
            /* Menghilangkan gaya desktop agar konten mobile terlihat penuh dan bersih */
            border-top-left-radius: 0;
            box-shadow: none;
            padding: 1rem; /* Padding lebih kecil di mobile */
        }
    }
    </style>
</head>
<body>

    <div class="overlay" id="overlay"></div>

    <div class="sidebar" id="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    {{-- Ganti dengan path logo Anda --}}
                    <img src="{{ asset('assets/logoklinik.png') }}" alt="Logo Klinik Patria"> 
                </div>
                <h4>Klinik Patria</h4>
            </div>
            
            <div class="nav-links">
                <a href="{{ route('home') }}" class="{{ Request::routeIs('home') ? 'active' : '' }}">
                    <i class="fa-solid fa-house"></i> Beranda
                </a>
                <a href="{{ route('riwayat-kunjungan') }}" class="{{ Request::routeIs('riwayat-kunjungan') ? 'active' : '' }}">
                    <i class="fa-solid fa-file-medical"></i> Riwayat Pemeriksaan
                </a>
                <a href="{{ route('biodata') }}" class="{{ Request::routeIs('biodata') ? 'active' : '' }}">
                    <i class="fa-solid fa-user"></i> Biodata
                </a>
            </div>
        </div>

        <div class="user-info-section">
            <span class="user-name">{{ Auth::user()->name ?? 'Pengguna' }}</span>
            {{-- Asumsi rute logout adalah 'logout' --}}
            <form action="{{ route('logout') }}" method="POST" class="w-100">
                @csrf
                <button type="submit" class="btn btn-light btn-sm w-100">
                    <i class="fa-solid fa-right-from-bracket"></i> Logout
                </button>
            </form>
        </div>
    </div>

    <div class="topbar">
        <button class="toggle-btn" id="toggle-btn"><i class="fa-solid fa-bars"></i></button>
        <h5>Klinik Patria</h5>
    </div>

    <div class="main-container">
        <div class="content">
            @yield('content')
        </div>
    </div>

    <script>
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('toggle-btn');
    const overlay = document.getElementById('overlay');

    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
    });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Klinik Patria</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/logoklinik1.png') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    
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
            --admin-color: #007bff;
            --admin-dark: #0056b3;
            --text-dark: #343a40;
            --border-color: #e9ecef;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-light);
            display: flex;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* ==================== SIDEBAR ==================== */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--admin-color);
            color: #fff;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .sidebar-header {
            background-color: var(--admin-dark);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1.5rem 1rem;
        }

        /* --- PERUBAHAN DI SINI --- */
        .sidebar-logo {
            /* Hapus background, radius, dan overflow */
            
            padding: 0;
            display: flex; 
            justify-content: center;
            align-items: center;
        }
        .sidebar-logo img {
            /* Atur lebar gambar secara langsung */
            width: 100px; 
            height: 100px; 
            object-fit: contain; 
            background: transparent;
        }
        /* ----------------------- */

        .sidebar-header h4 {
            font-weight: 700; font-size: 1rem; margin: 0;
        }

        .nav-links a {
            display: block;
            color: white;
            text-decoration: none;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-links a:hover, .nav-links a.active {
            background: rgba(255,255,255,0.2);
            border-left: 5px solid var(--white);
            padding-left: calc(1.5rem - 5px);
        }

        .sub-menu {
            background: rgba(0,0,0,0.1);
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }
        .sub-menu a {
            padding: 0.5rem 2.5rem;
            font-size: 0.9rem;
        }

        .logout-section {
            padding: 1rem;
        }
        .user-info-section {
            background-color: rgba(0,0,0,0.1);
            border-radius: 8px;
            padding: 0.75rem;
            text-align: center;
        }
        .user-info-section .btn {
            font-size: 0.8rem;
        }

        /* ==================== MAIN AREA ==================== */
        .main-container {
            margin-left: var(--sidebar-width);
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .top-header {
            background-color: var(--white);
            padding: 0.75rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content {
            flex: 1;
            background: var(--white);
            border-top-left-radius: 30px;
            box-shadow: -10px 0 25px rgba(0,0,0,0.05);
            padding: 2rem;
            min-height: 100vh;
        }

        /* ==================== RESPONSIVE ==================== */
        @media (max-width: 992px) {
            .sidebar {
                position: fixed;
                left: -250px;
                height: 100%;
                z-index: 2000;
            }
            .sidebar.active {
                left: 0;
            }
            .main-container {
                margin-left: 0;
            }
            .menu-toggle {
                display: inline-block;
                cursor: pointer;
                color: var(--admin-color);
            }
        }

        @media (max-width: 576px) {
            .content {
                padding: 1rem;
            }
            .top-header .admin-title {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div>
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <img src="{{ asset('assets/logoklinik1.png') }}" alt="Logo Klinik Patria">
                </div>
                <h4>Admin Panel</h4>
            </div>
            <div class="nav-links">
                <a href="{{ route('admin.dashboard') }}" class="{{ Request::routeIs('admin.dashboard') ? 'active' : '' }}"><i class="fa-solid fa-house"></i> Beranda</a>
                <a href="{{ route('admin.verifikasi-umum') }}" class="{{ Request::routeIs('admin.verifikasi-umum') ? 'active' : '' }}"><i class="fas fa-user-check me-2"></i> Listing Hold</a>
                <a href="{{ route('admin.data-pasien') }}" class="{{ Request::routeIs('admin.data-pasien') ? 'active' : '' }}"><i class="fa-solid fa-users"></i> Data Pasien</a>
                <a href="{{ route('admin.verifikasi-berkas') }}"class="{{ Request::routeIs('admin.verifikasi-berkas') ? 'active' : '' }}"> <i class="fa-solid fa-file-alt me-2"></i> Verifikasi</a>
                <a href="{{ route('admin.riwayat-pasien') }}"class="{{ Request::routeIs('admin.riwayat-pasien') ? 'active' : '' }}"> <i class="fa-solid fa-history"></i> Riwayat</a>
                <a href="#" id="master-toggle"><i class="fa-solid fa-database"></i> Data Master <i class="fa-solid fa-chevron-down float-end"></i></a>
                <div class="sub-menu" id="sub-master">
                    <a href="{{ route('admin.pelayanan') }}">Pelayanan</a>
                    <a href="{{ route('admin.jam-pelayanan')}}">Jam Buka</a>
                    <a href="{{ route('admin.master.users', ['type' => 'user']) }}">User</a>
                    <a href="{{ route('admin.data-backup') }}">Data Backup</a>
                </div>
            </div>
        </div>
        <div class="logout-section">
            <div class="user-info-section">
                <span class="user-name">{{ Auth::guard('admin')->user()->nama ?? 'Admin' }}</span>
                <form action="{{ route('admin.logout') }}" method="POST" class="w-100">
                    @csrf
                    <button type="submit" class="btn btn-light btn-sm w-100">
                        <i class="fa-solid fa-right-from-bracket"></i> Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="main-container">
        <div class="top-header">
            <span class="admin-title">
                <i class="fa-solid fa-bars menu-toggle me-3" id="menuToggle"></i>
                <i class="fa-solid fa-tachometer-alt me-2"></i> Dashboard Administrasi
            </span>
        </div>
        <div class="content">
            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('menuToggle');
        const masterToggle = document.getElementById('master-toggle');
        const subMaster = document.getElementById('sub-master');

        toggle.addEventListener('click', () => sidebar.classList.toggle('active'));

        masterToggle.addEventListener('click', e => {
            e.preventDefault();
            if (subMaster.style.maxHeight) {
                subMaster.style.maxHeight = null;
            } else {
                subMaster.style.maxHeight = subMaster.scrollHeight + "px";
            }
        });
    </script>
</body>
</html>
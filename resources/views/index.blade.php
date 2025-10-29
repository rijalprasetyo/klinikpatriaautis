<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klinik Fisioterapi Klinik Patria</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/logoklinik.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        /* Variabel Warna Terinspirasi MCC */
        :root {
            --primary: #0570b6;
            --primary-dark: #044d87;
            --secondary: #6c757d;
            --dark: #092c4e;
            --light: #f8f9fa;
            --success: #28a745;
            --info: #17a2b8;
            --white: #ffffff;
            --accent: #ffa500;
            --bs-font-sans-serif: 'Plus Jakarta Sans', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* General Body Styling */
        body {
            font-family: var(--bs-font-sans-serif);
            line-height: 1.7;
            color: var(--dark);
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, .section-title {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-weight: 800;
            color: var(--dark);
        }

        /* Navbar Styling - MCC Style */
        .navbar {
            padding: 20px 0;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
        }

        .navbar.scrolled {
            padding: 12px 0;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.12);
        }

        .navbar-brand img {
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: scale(1.05);
        }

        .nav-link {
            color: var(--dark) !important;
            font-weight: 600;
            margin: 0 15px;
            padding: 8px 0 !important;
            transition: all 0.3s ease;
            position: relative;
            text-transform: capitalize;
            font-size: 0.95rem;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border-radius: 2px;
        }

        .nav-link:hover::before,
        .nav-link.active::before {
            width: 100%;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--primary) !important;
        }
        
        /* Button Styling - MCC Style */
        .btn-login, .btn-hero, .btn-register {
            border-radius: 50px;
            padding: 12px 35px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            font-size: 0.9rem;
        }

        .btn-login {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: var(--white);
            border: none;
        }

        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, var(--primary-dark), var(--primary));
            transition: left 0.4s ease;
            z-index: -1;
        }

        .btn-login:hover::before {
            left: 0;
        }

        .btn-login:hover {
            color: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(5, 112, 182, 0.4);
        }

        .btn-hero, .btn-register {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: var(--white);
            border: none;
        }

        .btn-hero:hover, .btn-register:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 12px 35px rgba(5, 112, 182, 0.4);
            color: var(--white);
        }

        /* Hero Section - MCC Inspired */
        #hero {
            padding: 180px 0 120px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            position: relative;
            min-height: 90vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            background-color: var(--light); /* Tambahkan fallback warna */
        }

        #hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 50%, rgba(5, 112, 182, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255, 165, 0, 0.1) 0%, transparent 50%);
            pointer-events: none;
        }

        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 0;
            opacity: 0.6;
        }

        #hero > .container {
            position: relative;
            z-index: 1;
        }

        #hero h1 {
            font-size: 4rem;
            font-weight: 900;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 25px;
            line-height: 1.2;
        }

        #hero .lead {
            font-size: 1.3rem;
            color: var(--secondary);
            margin-bottom: 40px;
            font-weight: 500;
            line-height: 1.8;
        }

        .hero-carousel {
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            transform: perspective(1000px) rotateY(-5deg);
            transition: transform 0.6s ease;
        }

        .hero-carousel:hover {
            transform: perspective(1000px) rotateY(0deg);
        }

        .carousel-item img {
            max-height: 500px;
            object-fit: cover;
            border-radius: 25px;
        }

        .carousel-control-prev-icon, 
        .carousel-control-next-icon {
            background-color: rgba(5, 112, 182, 0.7);
            border-radius: 50%;
            padding: 20px;
        }

        /* Sections */
        section {
            padding: 100px 0;
            position: relative;
        }

        .section-title {
            font-size: 3rem;
            position: relative;
            display: inline-block;
            margin-bottom: 20px;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .section-title::after {
            content: '';
            display: block;
            width: 80px;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            margin: 15px auto 0;
            border-radius: 3px;
        }

        /* About Section */
        #about {
            background-color: var(--white);
            position: relative;
        }

        #about::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="1" fill="%230570b6" opacity="0.1"/></svg>');
            background-size: 50px 50px;
            pointer-events: none;
        }

        .card-glass {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            border: 2px solid rgba(5, 112, 182, 0.1);
            margin-bottom: 30px;
            backdrop-filter: blur(10px);
            transition: all 0.4s ease;
            height: 100%;
        }

        .card-glass:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(5, 112, 182, 0.15);
            border-color: var(--primary);
        }

        .about-clinic-card {
            padding: 50px;
            text-align: center;
        }

        /* Jam Operasional */
        .jam-item {
            padding: 20px 15px;
            background: linear-gradient(135deg, #f8f9fa, #ffffff);
            border-radius: 15px;
            transition: all 0.4s ease;
            border: 2px solid transparent;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .jam-item:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 25px rgba(5, 112, 182, 0.15);
            transform: translateY(-5px) scale(1.02);
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
        }
        
        /* Services Section */
        #services {
            background: linear-gradient(180deg, #f5f7fa 0%, #ffffff 100%);
        }

        .service-card {
            background: var(--white);
            border-radius: 20px;
            padding: 40px 25px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            flex-direction: column;
            align-items: center;
            height: 100%;
            border: 2px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 5px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            transform: scaleX(0);
            transition: transform 0.4s ease;
        }

        .service-card:hover::before {
            transform: scaleX(1);
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 50px rgba(5, 112, 182, 0.2);
            border-color: var(--primary);
        }

        .service-icon {
            width: 100px;
            height: 100px;
            margin-bottom: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(5, 112, 182, 0.1), rgba(255, 165, 0, 0.1));
            transition: all 0.4s ease;
        }

        .service-card:hover .service-icon {
            transform: scale(1.1) rotate(5deg);
            background: linear-gradient(135deg, rgba(5, 112, 182, 0.2), rgba(255, 165, 0, 0.2));
        }

        /* PERUBAHAN: Menyesuaikan gaya ikon layanan agar konsisten */
        .service-icon img {
            width: 60px;  /* Ukuran lebih besar agar lebih jelas */
            height: 60px;
            object-fit: cover;  /* Ganti dari 'contain' ke 'cover' agar gambar memenuhi area bulat */
            border-radius: 50%;  /* Membuat gambar menjadi bulat sempurna */
            border: 3px solid rgba(5, 112, 182, 0.2);  /* Tambahan: border untuk estetika */
        }

        .service-icon i {
             /* Mengatur ulang warna ikon FontAwesome di dalam div agar sesuai dengan gradient background */
            color: var(--primary) !important;
            transition: color 0.4s ease;
        }
        
        /* Pendaftaran Section */
        #pendaftaran {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: var(--white);
            position: relative;
            overflow: hidden;
        }

        #pendaftaran::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            border-radius: 50%;
        }

        #pendaftaran .section-title {
            color: var(--white);
            -webkit-text-fill-color: var(--white);
        }

        #pendaftaran .section-title::after {
            background: linear-gradient(90deg, var(--white), var(--accent));
        }

        .category-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 35px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-align: center;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            border: 3px solid transparent;
            position: relative;
            overflow: hidden;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s ease;
        }

        .category-card:hover::before {
            left: 100%;
        }

        .category-card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
            border-color: var(--accent);
        }

        .category-icon {
            height: 200px;
            width: 100%;
            overflow: hidden;
            border-radius: 15px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
            position: relative;
        }

        .category-icon::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(180deg, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
            z-index: 1;
            transition: opacity 0.4s ease;
        }

        .category-card:hover .category-icon::before {
            opacity: 0;
        }

        .category-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .category-card:hover .category-icon img {
            transform: scale(1.15);
        }

        .category-card .btn-register {
            width: 100%;
            margin-top: auto;
        }

        /* Footer Styling - MCC Inspired */
        .footer-modern {
            background: linear-gradient(135deg, var(--dark) 0%, #051b35 100%);
            color: #dcdcdc;
            padding: 60px 0 30px;
            font-size: 0.95rem;
            position: relative;
        }

        .footer-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><circle cx="50" cy="50" r="1" fill="%23ffffff" opacity="0.05"/></svg>');
            background-size: 30px 30px;
            pointer-events: none;
        }

        .footer-title {
            color: var(--white);
            font-weight: 800;
            margin-bottom: 25px;
            font-size: 1.3rem;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 2px;
        }

        .footer-modern a {
            color: #dcdcdc;
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }

        .footer-modern a:hover {
            color: var(--accent);
            padding-left: 5px;
        }

        .social-icons a {
            display: inline-flex;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            color: var(--white);
            border-radius: 50%;
            justify-content: center;
            align-items: center;
            margin-right: 12px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            border: 2px solid transparent;
        }

        .social-icons a:hover {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            transform: translateY(-5px) rotate(360deg);
            border-color: var(--white);
        }

        /* Animation */
        .reveal {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s cubic-bezier(0.4, 0, 0.2, 1), 
                        transform 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            #hero {
                padding-top: 140px;
                text-align: center;
            }
            
            #hero h1 {
                font-size: 2.8rem;
            }
            
            .hero-carousel {
                transform: none;
            }
            
            .section-title {
                font-size: 2.2rem;
            }
            
            .navbar-collapse {
                background-color: rgba(255, 255, 255, 0.98);
                border-radius: 0 0 15px 15px;
                padding: 20px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
                margin-top: 10px;
            }
            
            .nav-link {
                margin: 10px 0;
            }
            
            .btn-login {
                margin-top: 15px;
                width: 100%;
            }
        }

        @media (max-width: 576px) {
            #hero h1 {
                font-size: 2.2rem;
            }
            
            .section-title {
                font-size: 1.8rem;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('assets/logoklinik.png') }}" alt="Logo" width="90" height="42" class="me-2"> 
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link active" href="#hero">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pendaftaran">Pendaftaran</a></li>
                </ul>
                <a href="{{ route('user.login') }}" class="btn btn-login">Login</a> 
            </div>
        </div>
    </nav>

    <section id="hero">
        <div id="particles-js"></div>
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-lg-6 order-lg-1 order-2">
                    <h1 class="reveal">Solusi Fisioterapi Modern & Inklusif</h1>
                    <p class="lead reveal" style="transition-delay: 0.1s;">
                        Kami hadir untuk memberikan layanan rehabilitasi berkualitas tinggi yang dapat diakses oleh semua kalangan, memulihkan gerak dan harapan Anda.
                    </p>
                    <a href="#pendaftaran" class="btn btn-hero reveal" style="transition-delay: 0.2s;">
                        Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                </div>
                <div class="col-lg-6 order-lg-2 order-1 reveal" style="transition-delay: 0.15s;">
                    <div class="hero-carousel">
                        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
                            <div class="carousel-inner">
                                <div class="carousel-item active">
                                    <img src="{{ asset('assets/foto1.jpg') }}" class="d-block w-100" alt="Fisioterapi">
                                </div>
                                <div class="carousel-item">
                                    <img src="{{ asset('assets/foto2.jpg') }}" class="d-block w-100" alt="Rehabilitasi">
                                </div>
                                <div class="carousel-item">
                                    <img src="{{ asset('assets/foto3.jpg') }}" class="d-block w-100" alt="Klinik">
                                </div>
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="about">
        <div class="container">
            <div class="row g-4">
                <div class="col-12">
                    <div class="card-glass reveal about-clinic-card">
                        <h2 class="section-title">Mengenai Klinik Kami</h2>
                        <p class="text-muted mt-4 mb-0 mx-auto" style="font-size: 1.15rem; line-height: 1.9; max-width: 850px;">
                            Kami menyediakan layanan rehabilitasi fisioterapi yang <strong>inklusif, berkualitas, dan terjangkau</strong>. Program ini diprioritaskan bagi kalangan disabilitas dari keluarga prasejahtera. Masyarakat umum tetap dapat mendaftar dan memperoleh layanan sesuai dengan ketersediaan jadwal dan kuota.
                        </p>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card-glass reveal" style="transition-delay: 0.1s;">
                        <h2 class="section-title text-center mx-auto">Jam Operasional</h2>
                        
                        @if(isset($jam_operasional) && $jam_operasional->count() > 0)
                        <div class="row g-3 mt-4 justify-content-center"> 
                            @foreach($jam_operasional as $jam)
                            <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                                <div class="jam-item text-center">
                                    <div class="text-uppercase fw-semibold text-muted small mb-2">{{ $jam->hari }}</div>
                                    <div class="fw-bold" style="font-size: 1.1rem; color: var(--primary);">
                                        {{ $jam->jam_mulai }} - {{ $jam->jam_selesai }}
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            
                        </div>
                        <p class="text-muted text-center mt-4 mb-0 pt-3 border-top">
                            <i class="fas fa-calendar-check me-2"></i>Buka Setiap Hari (Hari Jumat Libur)
                        </p>
                        @else
                        <div class="text-center py-5">
                            <p class="text-muted fst-italic mb-0"><i class="fas fa-clock me-2"></i> Belum ada data jam operasional yang ditetapkan.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="services">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title reveal">Layanan Unggulan Kami</h2>
                <p class="text-muted mt-3 reveal" style="transition-delay: 0.1s;">Berbagai layanan fisioterapi profesional untuk pemulihan optimal</p>
            </div>
            <div class="row g-4 justify-content-center">
                
                @if(isset($jenis_pelayanan) && $jenis_pelayanan->count() > 0)
                    @foreach ($jenis_pelayanan as $index => $layanan)
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="service-card reveal" style="transition-delay: {{ 0.1 * ($index + 1) }}s;">
                            <div class="service-icon">
                                <img src="{{ asset('assets/' . $layanan->icon_pelayanan) }}" 
                                    alt="{{ $layanan->pelayanan }}"
                                    title="{{ $layanan->pelayanan }}"
                                    loading="lazy">
                            </div>
                            <h3 class="h5 text-center mb-0">{{ $layanan->pelayanan }}</h3>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="service-card reveal" style="transition-delay: 0.1s;">
                            <div class="service-icon">
                                <i class="fas fa-hands-helping fa-2x" style="color: var(--primary);"></i>
                            </div>
                            <h3 class="h5 text-center mb-0">Terapi Manual</h3>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-4 col-sm-6">
                        <div class="service-card reveal" style="transition-delay: 0.2s;">
                            <div class="service-icon">
                                <i class="fas fa-bolt fa-2x" style="color: var(--primary);"></i>
                            </div>
                            <h3 class="h5 text-center mb-0">Elektroterapi</h3>
                        </div>
                    </div>
                    <div class="col-12 text-center py-4">
                        <p class="text-muted fst-italic mb-0"><i class="fas fa-box-open me-2"></i> Belum ada data layanan yang tersedia saat ini.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section id="pendaftaran">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title reveal">Pilih Kategori Pendaftaran</h2>
                <p class="text-white mt-3 reveal" style="max-width: 700px; margin: 0 auto; transition-delay: 0.1s;">
                    Pilih kategori pendaftaran Anda. Layanan <strong>Prioritas</strong> diberikan kepada disabilitas dari keluarga prasejahtera (dengan SKTM).
                </p>
            </div>
            <div class="row g-4 justify-content-center">
                <div class="col-lg-4 col-md-6">
                    <div class="category-card reveal" style="transition-delay: 0.1s;">
                        <div class="category-icon">
                            <img src="https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?w=400" alt="Prioritas">
                        </div>
                        <h3 class="h5 text-center fw-bold mb-2" style="color: var(--dark);">Disabilitas Prioritas<br>(Dengan Surat Keterangan Tidak Mampu)</h3>
                        <p class="text-muted text-center small mb-4">Layanan rehabilitasi <strong>Gratis</strong> bagi keluarga prasejahtera.</p>
                        <a href="{{ route('user.login.submit') }}" class="btn btn-register">
                            Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="category-card reveal" style="transition-delay: 0.2s;">
                        <div class="category-icon">
                            <img src="https://images.unsplash.com/photo-1599058917212-d750089bc07e?w=400" alt="Disabilitas">
                        </div>
                        <h3 class="h5 text-center fw-bold mb-2" style="color: var(--dark);">Disabilitas<br>(Tanpa Surat Keterangan Tidak Mampu)</h3>
                        <p class="text-muted text-center small mb-4">Layanan dengan biaya terjangkau untuk kategori non-prasejahtera.</p>
                        <a href="{{ route('user.login.submit') }}" class="btn btn-register">
                            Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="category-card reveal" style="transition-delay: 0.3s;">
                        <div class="category-icon">
                            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=400" alt="Umum">
                        </div>
                        <h3 class="h5 text-center fw-bold mb-2" style="color: var(--dark);">Masyarakat<br>Umum</h3>
                        <p class="text-muted text-center small mb-4">Layanan fisioterapi umum sesuai ketersediaan kuota.</p>
                        <a href="{{ route('user.login.submit') }}" class="btn btn-register">
                            Daftar Sekarang <i class="fas fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-modern">
        <div class="container">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-12">
                    <h5 class="footer-title">Klinik Patria</h5>
                    <p>Solusi Fisioterapi Modern & Inklusif untuk memulihkan gerak dan harapan Anda.</p>
                </div>
                <div class="col-lg-2 col-md-4">
                    <h5 class="footer-title">Navigasi</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#hero">Beranda</a></li>
                        <li class="mb-2"><a href="#about">Tentang</a></li>
                        <li class="mb-2"><a href="#services">Layanan</a></li>
                        <li class="mb-2"><a href="#pendaftaran">Pendaftaran</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-title">Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Jl. Manggar No.08, Sengkaling, Mulyoagung, Kec. Dau, Kabupaten Malang, Jawa Timur</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i>0896-8187-0970</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i>macdatabase25@gmail.com</li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-4">
                    <h5 class="footer-title">Ikuti Kami</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://www.instagram.com/malangautismcenter.ofc?utm_source=ig_web_button_share_sheet&igsh=Y2poeW5pcjFmMXI4"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5 pt-4 border-top" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                <p class="mb-0">&copy; 2025 Klinik Fisioterapi Klinik Patria. All Rights Reserved. SYN Project</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <script>
        // Navbar Scroll Effect
        const navbar = document.querySelector('.navbar');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // Active Nav Link
        const sections = document.querySelectorAll('section[id]');
        const navLinks = document.querySelectorAll('.nav-link');

        window.addEventListener('scroll', () => {
            let current = '';
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (scrollY >= (sectionTop - 100)) {
                    current = section.getAttribute('id');
                }
            });

            navLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href').substring(1) === current) {
                    link.classList.add('active');
                }
            });
        });

        // Particles.js Configuration
        particlesJS('particles-js', {
            particles: {
                number: { value: 80, density: { enable: true, value_area: 800 } },
                color: { value: '#0570b6' },
                shape: { type: 'circle' },
                opacity: { value: 0.4, random: true, anim: { enable: true, speed: 1, opacity_min: 0.1 } },
                size: { value: 4, random: true, anim: { enable: true, speed: 3, size_min: 0.1 } },
                line_linked: { 
                    enable: true, 
                    distance: 150, 
                    color: '#0570b6', 
                    opacity: 0.3, 
                    width: 1 
                },
                move: { 
                    enable: true, 
                    speed: 3, 
                    direction: 'none', 
                    random: true, 
                    straight: false, 
                    out_mode: 'out', 
                    bounce: false 
                }
            },
            interactivity: {
                detect_on: 'canvas',
                events: { 
                    onhover: { enable: true, mode: 'grab' }, 
                    onclick: { enable: true, mode: 'push' }, 
                    resize: true 
                },
                modes: { 
                    grab: { distance: 250, line_linked: { opacity: 0.6 } }, 
                    push: { particles_nb: 4 } 
                }
            },
            retina_detect: true
        });

        // Reveal on Scroll Animation
        const revealElements = document.querySelectorAll('.reveal');
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { 
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        revealElements.forEach(el => revealObserver.observe(el));

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                    const navbarCollapse = document.querySelector('.navbar-collapse');
                    if (navbarCollapse.classList.contains('show')) {
                        navbarCollapse.classList.remove('show');
                    }
                }
            });
        });
    </script>
</body>
</html>

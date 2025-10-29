<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klinik Fisioterapi Klinik Patria</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/logoklinik.png') }}">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #007BFF;
            --primary-blue-dark: #0056b3;
            --text-dark: #212529;
            --text-muted: #6c757d;
            --light-gray-bg: #f8f9fa;
            --white: #ffffff;
            --border-color: #dee2e6;
            --dark-bg: #1a202c; /* Warna untuk footer */
            --success-green: #28a745;
            --success-green-dark: #218838;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; scroll-padding-top: 80px; }
        body { 
            font-family: 'Inter', sans-serif; 
            background: var(--white);
            color: var(--text-dark); 
            line-height: 1.7;
        }
        .navbar {
            background: var(--white);
            transition: all 0.3s ease;
            padding: 1rem 0;
        }
        .navbar.navbar-scrolled {
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
        }
        .navbar-brand { font-family: 'Poppins', sans-serif; font-weight: 700; color: var(--primary-blue); }
        .navbar-brand svg { color: var(--primary-blue); }
        .nav-link { font-weight: 600; color: var(--text-muted) !important; transition: color 0.3s ease, transform 0.2s ease; padding: 0.5rem 1rem !important; }
        .nav-link:hover, .nav-link.active { color: var(--primary-blue) !important; transform: translateY(-2px); }
        .navbar .btn-primary { border-radius: 50px; font-weight: 600; padding: 10px 24px; transition: all 0.3s ease; background-color: var(--primary-blue); border-color: var(--primary-blue); }
        .navbar .btn-primary:hover { background-color: var(--primary-blue-dark); border-color: var(--primary-blue-dark); transform: scale(1.05); }
        
        section { padding: 100px 0; overflow-x: hidden; }

                /* ===== SERVICE SECTION ===== */
        #services {
            padding: 80px 0;
        }

        .service-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 30px 20px;
            transition: all 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
        }

        .service-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        /* Bulatkan dan rapikan icon layanan */
        .service-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 15px auto;
            border-radius: 50%;
            overflow: hidden;
            background: #f5f7fa;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .service-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            transition: transform 0.3s ease;
        }

        .service-card:hover .service-icon img {
            transform: scale(1.1);
        }

        /* Judul layanan */
        .service-card h3 {
            font-size: 1.05rem;
            font-weight: 600;
            color: #222;
            margin-top: 10px;
        }

        
        #hero { 
            padding: 120px 0; 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            background-color: var(--light-gray-bg);
            position: relative;
            overflow: hidden;
        }
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
        #hero .container {
            position: relative;
            z-index: 2;
        }
        #hero h1 { font-family: 'Poppins', sans-serif; font-size: 3.8rem; font-weight: 700; line-height: 1.2; color: var(--text-dark); }
        #hero p { font-size: 1.25rem; opacity: 0.9; max-width: 550px; color: var(--text-muted); }
        .carousel-container {
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 2;
        }
        .carousel-item img {
            width: 100%;
            height: 450px;
            object-fit: cover;
        }

        .bg-light-gray { background-color: var(--light-gray-bg); }
        .container-main { max-width: 900px; margin: 0 auto; }
        .container-auth { max-width: 500px; margin: 0 auto; }
        .card-modern { 
            background: var(--white); 
            border-radius: 16px; 
            padding: 40px; 
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05); 
            border: 1px solid var(--border-color);
            margin-bottom: 20px; 
            transition: all 0.3s ease; 
            height: 100%;
        }
        .card-modern:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0, 0, 0, 0.08); }
        h1, h2, h3, h4, h5 { font-family: 'Poppins', sans-serif; font-weight: 700; }
        h2 { font-size: 2.5rem; color: var(--text-dark); margin-bottom: 24px; }
        h3 { font-size: 1.3rem; font-weight: 600; color: var(--text-dark); margin-bottom: 16px; }
        .text-muted { color: var(--text-muted) !important; font-size: 1rem; }
        .btn-primary-modern { background-color: var(--primary-blue); border: none; color: var(--white); padding: 14px 32px; border-radius: 12px; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; cursor: pointer; box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2); }
        .btn-primary-modern:hover { transform: translateY(-3px) scale(1.03); box-shadow: 0 6px 25px rgba(0, 123, 255, 0.3); background-color: var(--primary-blue-dark); }
        .btn-secondary-modern { background: #e9ecef; border: none; color: #495057; padding: 14px 32px; border-radius: 12px; font-weight: 600; font-size: 1rem; transition: all 0.3s ease; cursor: pointer; }
        .btn-secondary-modern:hover { background: #ced4da; transform: translateY(-3px); }
        .category-card { border: 2px solid var(--border-color); border-radius: 16px; padding: 32px; cursor: pointer; transition: all 0.3s ease; background-color: var(--white); }
        .category-card:hover { border-color: var(--primary-blue); transform: translateY(-4px); box-shadow: 0 10px 30px rgba(0, 123, 255, 0.1); }
        
        .category-icon {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 24px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            border: 3px solid var(--white);
        }
        .category-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .service-card { text-align: center; transition: all 0.3s ease; }
        .form-label { font-weight: 600; color: #495057; margin-bottom: 8px; font-size: 0.95rem; }
        .form-control, .form-select { border: 2px solid var(--border-color); border-radius: 10px; padding: 12px 16px; font-size: 1rem; transition: all 0.3s ease; background-color: var(--white); }
        .form-control:focus, .form-select:focus { border-color: var(--primary-blue); box-shadow: 0 0 0 4px rgba(0, 123, 255, 0.15); outline: none; }
        .info-box { background: #e7f3ff; border-left: 4px solid var(--primary-blue); border-radius: 12px; padding: 20px; margin: 24px 0; }
        .info-box p strong { color: var(--primary-blue-dark); }
        
        .modal-content { border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.1); }
        .modal-header { background: var(--primary-blue); color: var(--white); border: none; padding: 24px; }
        .modal-title { font-weight: 600; }
        .modal-body { padding: 32px; }
        .list-group-item { border: none; border-bottom: 1px solid #e9ecef; padding: 16px 0; }
        .list-group-item strong.text-end { max-width: 60%; text-align: right; }
        
        #success-page {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: var(--light-gray-bg);
            align-items: center; justify-content: center;
            padding: 20px; z-index: 2000;
            display: none; 
        }
        .success-container { 
            background: var(--white);
            color: var(--text-dark);
            border-radius: 24px; 
            padding: 40px; 
            max-width: 550px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        }
        .success-icon { width: 70px; height: 70px; background: var(--success-green); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 24px; }
        .success-icon svg { width: 40px; height: 40px; color: var(--white); }
        .ticket-display { border: 2px dashed var(--primary-blue); border-radius: 16px; padding: 24px; margin: 32px 0; }
        .ticket-display p { margin-bottom: 4px; color: var(--text-muted); }
        .ticket-number { font-family: 'Poppins', sans-serif; font-size: 3rem; font-weight: 700; color: var(--primary-blue-dark); letter-spacing: 2px; }
        .ticket-schedule { font-weight: 600; font-size: 1.1rem; color: var(--text-dark); }
        
        .footer-modern { 
            background: var(--dark-bg); 
            color: #a0aec0; 
            padding: 60px 0 30px 0; 
            font-size: 0.95rem; 
        }
        .footer-modern h5 { color: var(--white); margin-bottom: 1rem; font-weight: 600; }
        .footer-modern ul { list-style: none; padding: 0; }
        .footer-modern a { color: #a0aec0; text-decoration: none; transition: color 0.3s ease; }
        .footer-modern a:hover { color: var(--white); }
        .footer-modern .list-unstyled li { margin-bottom: 0.75rem; }
        .footer-modern .social-icons a { color: #a0aec0; font-size: 1.25rem; margin-right: 1rem; transition: color 0.3s ease, transform 0.3s ease; }
        .footer-modern .social-icons a:hover { color: var(--white); transform: scale(1.1); }
        .footer-modern .border-top { border-color: rgba(255,255,255,0.1) !important; }

        .hidden { display: none !important; }
        
        .reveal {
            opacity: 0;
            transform: translateY(40px);
            transition: opacity 0.8s ease-out, transform 0.8s ease-out;
        }
        .reveal.visible {
            opacity: 1;
            transform: translateY(0);
        }
        
        @media (max-width: 991.98px) { 
            #hero { 
                text-align: center; 
                padding-top: 150px; 
                padding-bottom: 80px;
                min-height: auto;
            } 
            #hero h1 { 
                font-size: 2.8rem;
            }
            #hero p { 
                margin-left: auto; 
                margin-right: auto;
                font-size: 1.1rem;
            }
            .hero-carousel-column {
                margin-top: 40px;
            }
        }
        @media (max-width: 768px) {
            section { padding: 60px 15px; }
            .card-modern { padding: 24px; border-radius: 16px; }
            h2 { font-size: 2rem; }
            .success-container { padding: 30px; }
            .ticket-number { font-size: 2.5rem; }
        }
    </style>
</head>
<body>
    
    <nav class="navbar navbar-expand-lg navbar-light fixed-top bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="{{ asset('assets/logoklinik.png') }}" alt="Logo Klinik Patria" width="85" height="40" class="me-2">
                
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item"><a class="nav-link" href="#hero">Beranda</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">Tentang</a></li>
                    <li class="nav-item"><a class="nav-link" href="#services">Layanan</a></li>
                    <li class="nav-item"><a class="nav-link" href="#pendaftaran">Pendaftaran</a></li>
                </ul>

                <div id="auth-section-guest" class="d-lg-inline-block">
                    <a href="{{ route('user.login.submit') }}" id="btn-login-nav" class="btn btn-primary px-4">Login</a>
                </div>
            </div>
        </div>
    </nav>

    
    <div id="main-content">
        @yield('content')
    </div>

    <footer class="footer-modern">
        <div class="container text-center text-md-start">
            <div class="row gy-4">
                <div class="col-lg-4 col-md-12"><h5 class="fw-bold">Klinik Patria</h5><p>Solusi Fisioterapi Modern & Inklusif untuk memulihkan gerak dan harapan Anda.</p></div>
                <div class="col-lg-2 col-md-4"><h5 class="fw-bold">Navigasi</h5><ul class="list-unstyled"><li><a href="#hero">Beranda</a></li><li><a href="#about">Tentang</a></li><li><a href="#services">Layanan</a></li><li><a href="#pendaftaran">Daftar</a></li></ul></div>
                <div class="col-lg-3 col-md-4"><h5 class="fw-bold">Kontak</h5><ul class="list-unstyled"><li><i class="fas fa-map-marker-alt me-2"></i>Jl. Sehat Selalu No. 123</li><li><i class="fas fa-phone me-2"></i>(021) 123-4567</li><li><i class="fas fa-envelope me-2"></i>kontak@klinikpatria.com</li></ul></div>
                <div class="col-lg-3 col-md-4"><h5 class="fw-bold">Ikuti Kami</h5><div class="social-icons"><a href="#"><i class="fab fa-facebook"></i></a><a href="#"><i class="fab fa-instagram"></i></a><a href="#"><i class="fab fa-whatsapp"></i></a></div></div>
            </div>
            <div class="text-center mt-5 border-top pt-4"><p>&copy; 2025 Klinik Fisioterapi Klinik Patria. All Rights Reserved.</p></div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/particles.js/2.0.0/particles.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Navbar Scroll Effect
            const navbar = document.querySelector('.navbar');
            window.addEventListener('scroll', () => {
                if (window.scrollY > 50) {
                    navbar.classList.add('navbar-scrolled');
                } else {
                    navbar.classList.remove('navbar-scrolled');
                }
            });

            // Particles.js init
            particlesJS('particles-js', {
                "particles": {
                    "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
                    "color": { "value": "#007BFF" },
                    "shape": { "type": "circle" },
                    "opacity": { "value": 0.5, "random": false, "anim": { "enable": false } },
                    "size": { "value": 3, "random": true, "anim": { "enable": false } },
                    "line_linked": { "enable": true, "distance": 150, "color": "#007BFF", "opacity": 0.4, "width": 1 },
                    "move": { "enable": true, "speed": 6, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
                },
                "interactivity": {
                    "detect_on": "canvas",
                    "events": { "onhover": { "enable": true, "mode": "grab" }, "onclick": { "enable": true, "mode": "push" }, "onresize": { "enable": true, "density_auto": false } },
                    "modes": { "grab": { "distance": 400, "line_linked": { "opacity": 1 } }, "push": { "particles_nb": 4 } }
                },
                "retina_detect": true
            });

            // Reveal on Scroll
            const revealElements = document.querySelectorAll('.reveal');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, { threshold: 0.1 });

            revealElements.forEach(el => observer.observe(el));
        });
    </script>
</body>
</html>
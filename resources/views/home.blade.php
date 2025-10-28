@extends('layouts.user-sidebar')

@section('content')

<style>
    /* ======================================================= */
    /* 1. VARIABEL & RESET */
    /* ======================================================= */
    :root {
        --primary: #2563eb;
        --primary-dark: #1e40af;
        --primary-light: #dbeafe;
        --bg-main: #f8fafc;
        --bg-card: #ffffff;
        --text-primary: #0f172a;
        --text-secondary: #64748b;
        --border: #e2e8f0;
        --success: #16a34a;
        --success-light: #dcfce7;
        --warning: #d97706;
        --warning-light: #fef3c7;
        --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        --shadow-category: 0 8px 20px rgba(0, 0, 0, 0.05);
    }
    
    * {
        box-sizing: border-box;
    }
    
    /* ======================================================= */
    /* 2. CONTAINER & SPACING */
    /* ======================================================= */
    .container-fluid {
        padding-left: 1.5rem;
        padding-right: 1.5rem;
        padding-top: 1.5rem;
        padding-bottom: 1.5rem;
    }
    
    section {
        margin-bottom: 2rem;
    }
    
    /* ======================================================= */
    /* 3. PAGE HEADER */
    /* ======================================================= */
    .page-header { 
        margin-bottom: 2rem;
    }
    
    .page-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
        line-height: 1.3;
    }
    
    .page-header h1 i { 
        color: var(--primary); 
        font-size: 22px;
        flex-shrink: 0;
    }
    
    .page-header p { 
        color: var(--text-secondary); 
        font-size: 13px; 
        margin: 0;
        line-height: 1.5;
    }

    /* ======================================================= */
    /* 4. SECTION TITLE */
    /* ======================================================= */
    .section-title {
        font-size: 18px;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .section-title i { 
        color: var(--primary); 
        font-size: 16px;
        flex-shrink: 0;
    }

    /* ======================================================= */
    /* 5. CARD MODERN */
    /* ======================================================= */
    .card-modern {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    /* ======================================================= */
    /* 6. TABEL JADWAL */
    /* ======================================================= */
    .table-container { 
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .table-modern { 
        width: 100%; 
        border-collapse: separate; 
        border-spacing: 0; 
        margin: 0;
        min-width: 600px;
    }
    
    .table-modern thead th {
        background: var(--bg-main);
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 0.875rem 1rem;
        border-bottom: 1px solid var(--border);
        white-space: nowrap;
    }
    
    .table-modern tbody td {
        padding: 1rem;
        border-bottom: 1px solid var(--border);
        color: var(--text-primary);
        font-size: 13px;
        vertical-align: middle;
    }
    
    .table-modern tbody tr:last-child td { 
        border-bottom: none; 
    }
    
    .queue-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        background: var(--primary-light);
        color: var(--primary);
        font-weight: 700;
        font-size: 15px;
        border-radius: 10px;
    }
    
    .badge-custom {
        display: inline-flex;
        align-items: center;
        padding: 0.4rem 0.875rem;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        line-height: 1;
        white-space: nowrap;
    }
    
    .badge-custom.success { 
        background: var(--success-light); 
        color: var(--success); 
    }
    
    .badge-custom.warning { 
        background: var(--warning-light); 
        color: var(--warning); 
    }

    /* ======================================================= */
    /* 6B. CARD JADWAL UNTUK MOBILE */
    /* ======================================================= */
    .mobile-schedule-container {
        display: none;
    }

    .schedule-card {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid var(--primary);
    }

    .schedule-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
    }

    .schedule-queue {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .schedule-queue-number {
        width: 38px;
        height: 38px;
        background: var(--primary-light);
        color: var(--primary);
        font-weight: 700;
        font-size: 14px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .schedule-queue-label {
        font-size: 0.7rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .schedule-card-body {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .schedule-info-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .schedule-info-label {
        font-size: 0.7rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .schedule-info-value {
        font-size: 0.85rem;
        color: var(--text-primary);
        font-weight: 600;
    }

    .schedule-patient-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .schedule-service {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .schedule-card-footer {
        padding-top: 0.75rem;
        border-top: 1px solid var(--border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* ======================================================= */
    /* 7. KATEGORI PENDAFTARAN */
    /* ======================================================= */
    .bg-light-gray {
        background-color: var(--bg-main); 
        padding: 2rem 0;
        margin: 0 -1.5rem;
        border-top: 1px solid var(--border);
        border-bottom: 1px solid var(--border);
    }
    
    .card-category-wrapper {
        background: var(--bg-card); 
        border-radius: 12px; 
        padding: 1.5rem; 
        box-shadow: var(--shadow-sm); 
        border: 1px solid var(--border);
        margin: 0 1.5rem;
    }
    
    .card-category-wrapper h2 {
        font-size: 20px;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.5rem;
    }
    
    .card-category-wrapper > p {
        font-size: 13px;
        color: var(--text-secondary);
        margin-bottom: 1.5rem;
    }
    
    .category-card { 
        border: 2px solid var(--border); 
        border-radius: 12px; 
        padding: 1.5rem; 
        cursor: pointer; 
        transition: all 0.3s ease; 
        background-color: var(--bg-card);
        height: 100%;
    }
    
    .category-card:hover { 
        border-color: var(--primary); 
        transform: translateY(-2px); 
        box-shadow: 0 8px 16px rgba(37, 99, 235, 0.1); 
    }
    
    .category-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        margin: 0 auto 1rem;
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }
    
    .category-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .category-card h3 { 
        font-size: 16px; 
        font-weight: 600; 
        color: var(--text-primary);
        line-height: 1.4;
        margin: 0 0 1rem 0;
        min-height: auto;
    }
    
    .btn-primary-modern { 
        background-color: var(--primary); 
        border: none; 
        color: white; 
        padding: 0.75rem 1.25rem; 
        border-radius: 8px; 
        font-weight: 600; 
        font-size: 14px; 
        transition: all 0.3s ease; 
        cursor: pointer; 
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.2);
        width: 100%;
    }
    
    .btn-primary-modern:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 6px 15px rgba(37, 99, 235, 0.3); 
        background-color: var(--primary-dark); 
    }

    /* ======================================================= */
    /* 8. JAM OPERASIONAL */
    /* ======================================================= */
    .hours-header { 
        text-align: center; 
        margin-bottom: 1.5rem; 
    }
    
    .hours-header h4 { 
        font-size: 18px; 
        font-weight: 600; 
        color: var(--success); 
        margin-bottom: 0.5rem; 
    }
    
    .hours-header p { 
        color: var(--text-secondary); 
        font-size: 13px; 
        margin: 0; 
    }
    
    .time-slots-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.75rem;
        max-width: 800px;
        margin: 0 auto;
    }
    
    .time-slot {
        background: var(--bg-main);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 0.875rem 0.5rem;
        text-align: center;
        font-weight: 500;
        font-size: 12px;
        color: var(--text-primary);
        transition: all 0.2s ease;
    }
    
    .time-slot:hover {
        background: var(--primary-light);
        border-color: var(--primary);
        color: var(--primary);
    }
    
    .time-slot i { 
        color: var(--primary); 
        margin-right: 4px; 
        font-size: 12px; 
    }

    /* ======================================================= */
    /* 9. LAYANAN TERSEDIA */
    /* ======================================================= */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
    }
    
    .service-card {
        background: var(--bg-card);
        border: 1px solid var(--border);
        border-radius: 12px;
        padding: 1.25rem 1rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }
    
    .service-card:hover {
        border-color: var(--primary);
        box-shadow: 0 6px 12px rgba(37, 99, 235, 0.1);
        transform: translateY(-3px);
    }
    
    .service-icon {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        overflow: hidden;
        margin: 0 auto 0.875rem;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.08);
    }
    
    .service-icon img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .service-title {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-primary);
        line-height: 1.4;
        margin: 0;
    }

    /* ======================================================= */
    /* 10. RESPONSIVE - TABLET (max-width: 992px) */
    /* ======================================================= */
    @media (max-width: 992px) {
        .services-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 0.875rem;
        }
        
        .time-slots-grid {
            grid-template-columns: repeat(3, 1fr);
            max-width: 600px;
            margin: 0 auto;
        }

        .service-icon, .category-icon {
            width: 60px;
            height: 60px;
        }
        
        .service-title, .category-card h3 {
            font-size: 12px;
        }
        
        .time-slot {
            font-size: 11px;
        }
    }

    /* ======================================================= */
    /* 11. RESPONSIVE - MOBILE (max-width: 768px) */
    /* ======================================================= */
    @media (max-width: 768px) {
        /* Container */
        .container-fluid {
            padding: 1rem;
        }
        
        section {
            margin-bottom: 1.5rem;
        }
        
        /* Header */
        .page-header {
            margin-bottom: 1.5rem;
        }
        
        .page-header h1 {
            font-size: 18px;
        }
        
        .page-header h1 i {
            font-size: 16px;
        }
        
        .page-header p {
            font-size: 0.8rem;
        }
        
        /* Section Title */
        .section-title {
            font-size: 15px;
            margin-bottom: 0.875rem;
        }
        
        .section-title i {
            font-size: 14px;
        }
        
        /* Card Modern */
        .card-modern {
            border-radius: 10px;
        }
        
        .card-modern .card-body {
            padding: 1rem !important;
        }

        /* Jadwal: Hide Table, Show Cards */
        .table-container {
            display: none;
        }
        
        .mobile-schedule-container {
            display: block;
        }
        
        /* Background Section */
        .bg-light-gray {
            margin: 0 -1rem;
            padding: 1.5rem 0;
        }
        
        .card-category-wrapper {
            margin: 0 0.5rem;
            padding: 1rem;
        }
        
        .card-category-wrapper h2 {
            font-size: 16px;
            margin-bottom: 0.5rem;
        }
        
        .card-category-wrapper > p {
            font-size: 0.8rem;
            margin-bottom: 1rem;
        }
        
        /* Kategori: 2 Kolom */
        .row > .col-lg-4.col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .category-card {
            padding: 1rem 0.75rem;
        }
        
        .category-icon {
            width: 55px;
            height: 55px;
            margin-bottom: 0.75rem;
        }
        
        .category-card h3 {
            font-size: 12px;
            margin-bottom: 0.75rem;
        }
        
        .btn-primary-modern {
            padding: 0.6rem 1rem;
            font-size: 0.8rem;
        }
        
        /* Jam Operasional */
        .hours-header h4 {
            font-size: 15px;
        }
        
        .hours-header p {
            font-size: 0.75rem;
        }
        
        .time-slots-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
            max-width: none;
        }
        
        .time-slot {
            padding: 0.65rem 0.4rem;
            font-size: 0.7rem;
        }
        
        .time-slot i {
            font-size: 0.7rem;
        }
        
        /* Layanan: 3 Kolom Compact */
        .services-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 0.5rem;
        }
        
        .service-card {
            padding: 0.75rem 0.5rem;
            border-radius: 10px;
        }
        
        .service-icon {
            width: 45px;
            height: 45px;
            margin-bottom: 0.5rem;
        }
        
        .service-title {
            font-size: 0.7rem;
            line-height: 1.3;
        }
    }

    /* ======================================================= */
    /* 12. RESPONSIVE - SMALL MOBILE (max-width: 576px) */
    /* ======================================================= */
    @media (max-width: 576px) {
        .container-fluid {
            padding: 0.75rem;
        }
        
        section {
            margin-bottom: 1.25rem;
        }
        
        .page-header h1 {
            font-size: 16px;
        }
        
        .page-header p {
            font-size: 0.75rem;
        }
        
        .section-title {
            font-size: 14px;
        }
        
        /* Jadwal Cards - More Compact */
        .schedule-card {
            padding: 0.875rem;
            margin-bottom: 0.75rem;
        }
        
        .schedule-queue-number {
            width: 34px;
            height: 34px;
            font-size: 13px;
        }
        
        .schedule-card-body {
            gap: 0.5rem;
        }
        
        .schedule-info-label {
            font-size: 0.65rem;
        }
        
        .schedule-info-value {
            font-size: 0.8rem;
        }
        
        .schedule-patient-name {
            font-size: 0.9rem;
        }
        
        .schedule-service {
            font-size: 0.75rem;
        }
        
        /* Kategori */
        .card-category-wrapper {
            padding: 0.875rem;
        }
        
        .card-category-wrapper h2 {
            font-size: 15px;
        }
        
        .card-category-wrapper > p {
            font-size: 0.75rem;
        }
        
        .row > .col-lg-4.col-md-6 {
            flex: 0 0 50%;
            max-width: 50%;
        }
        
        .category-card {
            padding: 0.875rem 0.625rem;
        }
        
        .category-icon {
            width: 50px;
            height: 50px;
            margin-bottom: 0.625rem;
        }
        
        .category-card h3 {
            font-size: 11px;
            margin-bottom: 0.625rem;
        }
        
        .btn-primary-modern {
            padding: 0.5rem 0.875rem;
            font-size: 0.75rem;
        }
        
        /* Jam Operasional: 2 Kolom untuk Keterbacaan */
        .time-slots-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
        
        .time-slot {
            padding: 0.6rem 0.4rem;
            font-size: 0.65rem;
        }
        
        /* Layanan: 2 Kolom untuk Keterbacaan */
        .services-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
        
        .service-card {
            padding: 0.75rem 0.5rem;
        }
        
        .service-icon {
            width: 42px;
            height: 42px;
            margin-bottom: 0.5rem;
        }
        
        .service-title {
            font-size: 0.7rem;
        }
        
        /* Hours Header */
        .hours-header h4 {
            font-size: 14px;
        }
        
        .hours-header p {
            font-size: 0.7rem;
        }
    }

    /* Hide desktop table on mobile, show cards */
    @media (max-width: 768px) {
        .table-modern {
            display: none;
        }
        
        .mobile-schedule-container {
            display: block;
        }
    }

    @media (min-width: 769px) {
        .mobile-schedule-container {
            display: none;
        }
    }
</style>

<div class="container-fluid">

    {{-- Header Dashboard --}}
    <div class="page-header">
        <h1>
            <i class="fa-solid fa-notes-medical"></i>
            Pelayanan Klinik
        </h1>
        <p>Kelola jadwal dan lihat informasi layanan terbaru Klinik Patria.</p>
    </div>

    {{-- SECTION 1: Jadwal Pelayanan Mendatang --}}
    <section>
        <h3 class="section-title">
            <i class="fa-solid fa-calendar-alt"></i>
            Jadwal Pelayanan Anda
        </h3>
        
        <div class="card-modern">
            <div class="card-body p-4">
                {{-- Desktop: Table View --}}
                <div class="table-container">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th class="text-center">Antrian</th>
                                <th>Tanggal</th>
                                <th>Jam</th>
                                <th>Pasien</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadwal as $data)
                                <tr>
                                    <td class="text-center">
                                        <div class="queue-number">{{ $data->nomor_antrian }}</div>
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($data->tgl_kunjungan)->translatedFormat('d F Y') }}</td>
                                    <td class="fw-semibold">
                                        {{ $data->waktu ? $data->waktu->jam_mulai . ' - ' . $data->waktu->jam_selesai : 'N/A' }}
                                    </td>
                                    <td>
                                        {{ $data->nama_pasien }} <br>
                                        <small class="text-muted">{{ $data->layanan->pelayanan ?? '-' }}</small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge-custom warning">{{ $data->status_pemeriksaan }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Belum ada jadwal pelayanan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile: Card View --}}
                <div class="mobile-schedule-container">
                    @forelse ($jadwal as $data)
                        <div class="schedule-card">
                            <div class="schedule-card-header">
                                <div class="schedule-queue">
                                    <div class="schedule-queue-number">{{ $data->nomor_antrian }}</div>
                                    <span class="schedule-queue-label">Antrian</span>
                                </div>
                                <span class="badge-custom warning">{{ $data->status_pemeriksaan }}</span>
                            </div>
                            
                            <div class="schedule-card-body">
                                <div class="schedule-info-item">
                                    <span class="schedule-info-label">Tanggal</span>
                                    <span class="schedule-info-value">{{ \Carbon\Carbon::parse($data->tgl_kunjungan)->translatedFormat('d M Y') }}</span>
                                </div>
                                <div class="schedule-info-item">
                                    <span class="schedule-info-label">Jam</span>
                                    <span class="schedule-info-value">
                                        {{ $data->waktu ? $data->waktu->jam_mulai . ' - ' . $data->waktu->jam_selesai : 'N/A' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="schedule-card-footer">
                                <div>
                                    <div class="schedule-patient-name">{{ $data->nama_pasien }}</div>
                                    <div class="schedule-service">{{ $data->layanan->pelayanan ?? '-' }}</div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fa-solid fa-calendar-xmark mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                            <p class="mb-0">Belum ada jadwal pelayanan.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 2: Pilih Kategori Pendaftaran --}}
    <section id="pendaftaran" class="bg-light-gray">
        <div class="card-category-wrapper">
            <h2 class="text-center">Pilih Kategori Pendaftaran</h2>
            <p class="text-center">Pilih salah satu kategori di bawah ini untuk memulai proses pendaftaran Anda.</p>
            
            <div class="row g-3">
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="category-card d-flex flex-column text-center">
                        <div class="category-icon">
                            <img src="https://images.unsplash.com/photo-1582213782179-e0d53f98f2ca?q=80&w=400&auto=format&fit=crop" alt="Disabilitas SKTM">
                        </div>
                        <h3>Disabilitas<br>(Dengan SKTM)</h3>
                        <button 
                            class="btn-primary-modern mt-auto" 
                            onclick="window.location.href='/pendaftaran?kategori=Disabilitas (Dengan SKTM)'">
                            Daftar Kategori Ini
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="category-card d-flex flex-column text-center">
                        <div class="category-icon">
                            <img src="https://images.unsplash.com/photo-1599058917212-d750089bc07e?q=80&w=400&auto=format&fit=crop" alt="Disabilitas Non-SKTM">
                        </div>
                        <h3>Disabilitas<br>(Non-SKTM)</h3>
                        <button 
                            class="btn-primary-modern mt-auto"
                            onclick="window.location.href='/pendaftaran?kategori=Disabilitas (Non-SKTM)'">
                            Daftar Kategori Ini
                        </button>
                    </div>
                </div>
                <div class="col-lg-4 col-md-12 col-12">
                    <div class="category-card d-flex flex-column text-center">
                        <div class="category-icon">
                            <img src="https://images.unsplash.com/photo-1576091160550-2173dba999ef?q=80&w=400&auto=format&fit=crop" alt="Masyarakat Umum">
                        </div>
                        <h3>Masyarakat<br>Umum</h3>
                        <button 
                            class="btn-primary-modern mt-auto"
                            onclick="window.location.href='/pendaftaran?kategori=Masyarakat Umum'">
                            Daftar Kategori Ini
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 3: Jam Operasional Klinik --}}
    <section>
        <h3 class="section-title">
            <i class="fa-solid fa-business-time"></i>
            Jam Operasional Klinik
        </h3>
        
        <div class="card-modern">
            <div class="card-body p-4">
                <div class="hours-header">
                    <h4>Buka Setiap Hari</h4>
                    <p>Waktu pelayanan yang tersedia untuk pendaftaran</p>
                </div>
                
                <div class="time-slots-grid">
                    @forelse ($jam_operasional as $jam)
                        <div class="time-slot">
                            <i class="fa-regular fa-clock"></i>
                            {{ $jam->jam_mulai }} - {{ $jam->jam_selesai }}
                        </div>
                    @empty
                        <p class="text-muted text-center w-100">Belum ada data jam operasional.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    {{-- SECTION 4: Layanan Tersedia --}}
    <section>
        <h3 class="section-title">
            <i class="fa-solid fa-list-check"></i>
            Layanan Tersedia
        </h3>

        <div class="card-modern">
            <div class="card-body p-4">
                <div class="services-grid">
                    @foreach ($layanan as $item)
                        <div class="service-card">
                            <div class="service-icon">
                                <img src="{{ asset('assets/' . $item->icon_pelayanan) }}"
                                    alt="{{ $item->pelayanan }}">
                            </div>
                            <h3 class="service-title">{{ $item->pelayanan }}</h3>
                        </div>
                    @endforeach

                    @if($layanan->isEmpty())
                        <div class="text-center text-muted" style="grid-column: 1 / -1;">
                            Belum ada data layanan yang tersedia saat ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

</div>

@endsection
@extends('layouts.user-sidebar')

@section('content')

@php
    use Carbon\Carbon;
    $kategoriSktm = 'Disabilitas dengan Surat Keterangan Tidak Mampu';
    $kategoriNonSktm = 'Disabilitas tanpa Surat Keterangan Tidak Mampu';
    $kategoriUmum = 'Masyarakat Umum'; // Kategori yang memiliki logika status berkas
    $today = Carbon::today()->toDateString();
@endphp

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
        --warning: #d97706;
        --warning-light: #fef3c7;
        --error: #dc2626; /* MERAH UNTUK DITOLAK */
        --error-light: #fee2e2; /* MERAH MUDA */
        --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
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

    .badge-custom.pending-grey { 
    background: #f1f5f9; 
    color: #64748b; 
    }
    /* BARU: Gaya untuk status Ditolak */
    .badge-custom.rejected { 
        background: var(--error-light); 
        color: var(--error); 
    }
    /* BARU: Gaya untuk notifikasi Ditolak */
    .status-note.note-error { 
        color: var(--error);
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
    /* 6. TABEL JADWAL (DESKTOP) & STATUS STYLES */
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

    /* --- WARNA KHUSUS STATUS BERKAS --- */
    /* Abu-abu untuk Pending/Menunggu */
    .badge-custom.pending-grey { 
        background: #f1f5f9; /* Slate 100 */
        color: #64748b; /* Slate 500 */
    }

    /* Struktur Status untuk Desktop */
    .status-group {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 4px;
        text-align: center;
    }

    .status-note {
        font-size: 10px;
        color: var(--success);
        font-weight: 500;
        margin-top: 5px;
        display: block;
        max-width: 150px;
        line-height: 1.3;
        text-align: center;
    }
    .status-note i {
        margin-right: 3px;
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
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        border-left: 4px solid var(--primary);
        position: relative;
    }

    .schedule-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
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
        flex-shrink: 0;
    }

    .schedule-queue-info {
        display: flex;
        flex-direction: column;
    }

    .schedule-queue-label {
        font-size: 0.7rem;
        color: var(--text-secondary);
        font-weight: 500;
    }
    
    .schedule-patient-name {
        font-size: 0.95rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 0.25rem;
    }

    .schedule-card-body {
        display: grid;
        grid-template-columns: 1fr; 
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border);
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

    .schedule-service {
        font-size: 0.8rem;
        color: var(--text-secondary);
    }

    .schedule-card-footer {
        display: flex;
        justify-content: flex-start;
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
        
        /* Jadwal Card */
        .schedule-card-header {
            flex-direction: column;
            align-items: flex-start;
            border-bottom: none;
            padding-bottom: 0;
            margin-bottom: 0.5rem;
        }
        
        .status-group {
            align-items: flex-start;
            text-align: left;
            order: -1; 
            margin-bottom: 0.75rem;
        }

        .status-note {
            font-size: 9px;
            max-width: none;
            text-align: left;
            margin-top: 5px;
        }
        
        .schedule-card-body {
            grid-template-columns: 1fr 1fr;
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
            grid-template-columns: 1fr; /* Kembali ke 1 kolom untuk mobile kecil */
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
        
        /* Kategori: DIUBAH MENJADI 1 KOLOM */
        .row > .col-lg-4.col-md-6,
        .row > .col-lg-4.col-md-12.col-12 { /* Memastikan semua kolom menjadi 1 kolom */
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        .category-card {
            padding: 1rem; /* Sedikit lebih lega */
        }
        
        .category-icon {
            width: 60px; /* Diperbesar sedikit dari 50px */
            height: 60px;
            margin-bottom: 0.75rem;
        }
        
        .category-card h3 {
            font-size: 14px; /* Diperbesar agar mudah dibaca */
            margin-bottom: 0.75rem;
        }
        
        .btn-primary-modern {
            font-size: 0.85rem; /* Ukuran tombol lebih nyaman */
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
                                <th>Pasien & Layanan</th>
                                <th class="text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($jadwal as $data)
                                @php
                                    $isUmum = ($data->kategori_pendaftaran ?? '') == $kategoriUmum;
                                    $statusBerkas = $data->status_berkas ?? 'Menunggu';
                                    $statusPemeriksaan = $data->status_pemeriksaan ?? 'Belum Diperiksa';
                                    $tglKunjungan = \Carbon\Carbon::parse($data->tgl_kunjungan)->toDateString();
                                    
                                    // 1. Logika untuk menyembunyikan jika Ditolak & sudah Expired
                                    $isExpiredAndRejected = ($isUmum && $statusBerkas == 'Ditolak' && $tglKunjungan < $today);
                                    
                                    if ($isExpiredAndRejected) {
                                        continue; // Hentikan pemrosesan baris ini (Tidak ditampilkan)
                                    }
                                    
                                    // 2. Tentukan Status Tampil, Class, dan Note
                                    $statusTampil = '';
                                    $badgeClass = 'warning'; 
                                    $showNote = false;
                                    $noteClass = '';
                                    $noteText = '';

                                    if ($isUmum) {
                                        if ($statusBerkas == 'Sudah Diverifikasi') {
                                            $statusTampil = $statusPemeriksaan;
                                            $badgeClass = ($statusPemeriksaan == 'Sudah Diperiksa') ? 'success' : 'warning';
                                            $showNote = true; 
                                            $noteText = '<i class="fa-solid fa-check-circle"></i> Berkas anda sudah diverifikasi dan bisa melakukan pemeriksaan sesuai jadwal';

                                        } elseif ($statusBerkas == 'Ditolak') {
                                            $statusTampil = 'Ditolak';
                                            $badgeClass = 'rejected'; // MERAH DITOLAK
                                            $showNote = true;
                                            $noteClass = 'note-error';
                                            $noteText = '<i class="fa-solid fa-circle-xmark"></i> Pemeriksaan tidak bisa dilakukan, anda akan dihubungi oleh admin klinik.';

                                        } else { // Menunggu
                                            $statusTampil = $statusBerkas;
                                            $badgeClass = 'pending-grey';
                                        }
                                    } else {
                                        // LOGIKA KATEGORI PRIORITAS (Non-Umum)
                                        $statusTampil = $statusPemeriksaan;
                                        $badgeClass = ($statusPemeriksaan == 'Sudah Diperiksa') ? 'success' : 'warning';
                                    }
                                @endphp
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
                                        <small class="text-muted">{{ $data->layanan_id ?? '-' }} <br> {{ $data->kategori_pendaftaran ?? 'N/A' }}</small>                                         
                                    </td>
                                    <td class="text-center">
                                        <div class="status-group">
                                            <span class="badge-custom {{ $badgeClass }}">{{ $statusTampil }}</span> 
                                            
                                            @if ($showNote)
                                                <span class="status-note {{ $noteClass }}">
                                                    {!! $noteText !!}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        Belum ada jadwal pelayanan mendatang.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Mobile: Card View --}}
                <div class="mobile-schedule-container">
                    @forelse ($jadwal as $data)
                        @php
                            
                            $isUmum = ($data->kategori_pendaftaran ?? '') == $kategoriUmum;
                            $statusBerkas = $data->status_berkas ?? 'Menunggu';
                            $statusPemeriksaan = $data->status_pemeriksaan ?? 'Belum Diperiksa';
                            $tglKunjungan = Carbon::parse($data->tgl_kunjungan)->toDateString(); // Mengakses Carbon
                            
                            // Logika Skip harus diulang di sini untuk forelse mobile jika Anda ingin menggunakan array $jadwal utuh
                            $isExpiredAndRejected = ($isUmum && $statusBerkas == 'Ditolak' && $tglKunjungan < $today);
                            
                            if ($isExpiredAndRejected) {
                                continue;
                            }
                            
                            // Definisikan variabel Note di sini untuk menghindari error
                            $statusTampil = '';
                            $badgeClass = 'warning'; 
                            $showNote = false;
                            $noteClass = '';
                            $noteText = '';

                            if ($isUmum) {
                                if ($statusBerkas == 'Sudah Diverifikasi') {
                                    $statusTampil = $statusPemeriksaan;
                                    $badgeClass = ($statusPemeriksaan == 'Sudah Diperiksa') ? 'success' : 'warning';
                                    $showNote = true; 
                                    $noteText = '<i class="fa-solid fa-check-circle"></i> Berkas anda sudah diverifikasi dan bisa melakukan pemeriksaan sesuai jadwal';
                                } elseif ($statusBerkas == 'Ditolak') {
                                    $statusTampil = 'Ditolak';
                                    $badgeClass = 'rejected'; // MERAH DITOLAK
                                    $showNote = true;
                                    $noteClass = 'note-error';
                                    $noteText = '<i class="fa-solid fa-circle-xmark"></i> Pemeriksaan tidak bisa dilakukan, anda akan dihubungi oleh admin klinik.';
                                } else { // Menunggu (status berkas lainnya)
                                    $statusTampil = $statusBerkas;
                                    $badgeClass = 'pending-grey';
                                }
                            } else {
                                $statusTampil = $statusPemeriksaan;
                                $badgeClass = ($statusPemeriksaan == 'Sudah Diperiksa') ? 'success' : 'warning';
                            }
                        @endphp

                        <div class="schedule-card">
                            <div class="schedule-card-header">
                                
                                {{-- Status di Mobile Card --}}
                                <div class="status-group">
                                    <span class="badge-custom {{ $badgeClass }}">{{ $statusTampil }}</span> 
                                    
                                    @if ($showNote)
                                        <span class="status-note {{ $noteClass }}">
                                            {!! $noteText !!}
                                        </span>
                                    @endif
                                </div>

                                <div class="schedule-queue">
                                    <div class="schedule-queue-number">{{ $data->nomor_antrian }}</div>
                                    <div class="schedule-queue-info">
                                        <span class="schedule-queue-label">Antrian</span>
                                        <span class="schedule-patient-name m-0">{{ $data->nama_pasien }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="schedule-card-body">
                                <div class="schedule-info-item">
                                    <span class="schedule-info-label">Tanggal Kunjungan</span>
                                    <span class="schedule-info-value">{{ \Carbon\Carbon::parse($data->tgl_kunjungan)->translatedFormat('d M Y') }}</span>
                                </div>
                                <div class="schedule-info-item">
                                    <span class="schedule-info-label">Jam Pelayanan</span>
                                    <span class="schedule-info-value">
                                        {{ $data->waktu ? $data->waktu->jam_mulai . ' - ' . $data->waktu->jam_selesai : 'N/A' }}
                                    </span>
                                </div>
                                <div class="schedule-info-item">
                                    <span class="schedule-info-label">Layanan</span>
                                    <span class="schedule-info-value">{{ $data->layanan_id ?? '-' }}</span>
                                </div>
                                <div class="schedule-info-item">
                                    <span class="schedule-info-label">Kategori Pendaftar</span>
                                    <span class="schedule-info-value">{{ $data->kategori_pendaftaran ?? 'N/A' }}</span>
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
            <p class="text-center text-muted mb-5">
                Pilih salah satu kategori di bawah ini untuk memulai proses pendaftaran Anda. Layanan **Prioritas Utama** diberikan kepada disabilitas dari keluarga prasejahtera (dengan SKTM).
            </p>

            <div class="row g-4">
                {{-- KATEGORI 1: PRIORITAS UTAMA (DISABILITAS SKTM) --}}
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="category-card d-flex flex-column text-center h-100">
                        
                        <div class="text-center mb-3">
                            <span class="badge rounded-pill px-3 py-2 fw-bold text-white" style="background-color: #0570b6; font-size: 0.9em;">
                                PRIORITAS UTAMA
                            </span>
                        </div>

                        <div class="category-icon mb-3">
                            <img src="https://res.cloudinary.com/djhikwue3/image/upload/v1763283011/IMG-20251116-WA0016_u7rn8f.jpg" alt="Disabilitas SKTM">
                        </div>
                        
                        <h3>Disabilitas dengan<br>Surat Keterangan Tidak Mampu</h3>
                        <p class="text-muted small mb-4">Layanan rehabilitasi bagi keluarga prasejahtera</p>

                        <button 
                            class="btn-primary-modern mt-auto" 
                            onclick="window.location.href='/pendaftaran?kategori={{ $kategoriSktm }}'">
                            Daftar Kategori Ini
                        </button>
                    </div>
                </div>

                {{-- KATEGORI 2: PRIORITAS KEDUA (DISABILITAS NON-SKTM) --}}
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="category-card d-flex flex-column text-center h-100">
                        
                        <div class="text-center mb-3">
                            <span class="badge rounded-pill px-3 py-2 fw-bold text-white" style="background-color: #ffa500; font-size: 0.9em;">
                                PRIORITAS KEDUA
                            </span>
                        </div>

                        <div class="category-icon mb-3">
                            <img src="https://res.cloudinary.com/djhikwue3/image/upload/v1763283010/IMG-20251116-WA0018_idxrer.jpg" alt="Disabilitas Non-SKTM">
                        </div>
                        
                        <h3>Disabilitas tanpa<br>Surat Keterangan Tidak Mampu</h3>
                        <p class="text-muted small mb-4">Layanan dengan biaya terjangkau untuk kategori non-prasejahtera</p>

                        <button 
                            class="btn-primary-modern mt-auto"
                            onclick="window.location.href='/pendaftaran?kategori={{ $kategoriNonSktm }}'">
                            Daftar Kategori Ini
                        </button>
                    </div>
                </div>

                {{-- KATEGORI 3: NON PRIORITAS (MASYARAKAT UMUM) --}}
                <div class="col-lg-4 col-md-12 col-12">
                    <div class="category-card d-flex flex-column text-center h-100">

                        <div class="text-center mb-3">
                            <span class="badge rounded-pill px-3 py-2 fw-bold text-white" style="background-color: #6c757d; font-size: 0.9em;">
                                NON PRIORITAS
                            </span>
                        </div>

                        <div class="category-icon mb-3">
                            <img src="https://res.cloudinary.com/djhikwue3/image/upload/v1763283011/IMG-20251116-WA0020_v82mly.jpg" alt="Masyarakat Umum">
                        </div>
                        
                        <h3>Masyarakat<br>Umum</h3>
                        <p class="text-muted small mb-4">Layanan fisioterapi umum sesuai ketersediaan kuota</p>
                        
                        <button 
                            class="btn-primary-modern mt-auto"
                            onclick="window.location.href='/pendaftaran?kategori={{ $kategoriUmum }}'">
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
                    @foreach ($jenis_layanan as $item)
                        <div class="service-card">
                            <div class="service-icon">
                                <img src="{{ asset('assets/' . $item->icon_pelayanan) }}"
                                    alt="{{ $item->pelayanan }}">
                            </div>
                            <h3 class="service-title">{{ $item->pelayanan }}</h3>
                        </div>
                    @endforeach

                    @if(isset($jenis_layanan) && $jenis_layanan->isEmpty())
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
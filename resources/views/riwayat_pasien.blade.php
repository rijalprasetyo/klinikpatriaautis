@extends('layouts.user-sidebar')

@section('content')

<style>
    /* Variabel Warna Baru (Modern & Profesional) */
    :root {
        --primary-blue: #007bff;
        --secondary-blue: #0056b3;
        --text-dark: #343a40;
        --bg-light: #f8f9fa;
        --border-light: #dee2e6;
        --warning-yellow: #ffc107;
    }

    .container-fluid h2 {
        color: var(--secondary-blue);
        border-bottom: 2px solid var(--border-light);
        padding-bottom: 10px;
    }

    /* Tabel Utama */
    .table-responsive {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        overflow: hidden;
        margin-top: 20px;
        position: relative;
        min-height: 200px;
    }
    
    .table-primary thead tr {
        background-color: var(--primary-blue);
        color: white;
        font-weight: 600;
        border-bottom: none;
    }

    /* Tombol Aksi Tunggal (Riwayat Pasien) */
    .btn-riwayat { 
        background-color: var(--primary-blue) !important; 
        border-color: var(--primary-blue) !important; 
        padding: 0.5rem 1rem !important;
        font-size: 0.9rem;
    }
    .btn-riwayat:hover { 
        background-color: var(--secondary-blue) !important; 
    }
    .btn-action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Modal Utama (Elegan) */
    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .modal-header.bg-riwayat { 
        background-color: var(--primary-blue) !important;
        color: white;
    }

    /* Tabel Detail di Modal */
    .modal-body table th {
        width: 25%;
        font-weight: 600;
        color: var(--secondary-blue);
    }
    
    /* Navigasi Tab di Modal (Modern) */
    .nav-tabs .nav-link {
        color: var(--text-dark);
        border: none;
        border-bottom: 3px solid transparent;
        transition: all 0.3s;
        font-weight: 600;
    }
    .nav-tabs .nav-link.active {
        color: var(--primary-blue);
        border-color: var(--primary-blue);
        background-color: var(--bg-light);
    }
    .nav-tabs {
        border-bottom: 1px solid var(--border-light);
        margin-bottom: 10px;
    }
    
    .tab-pane {
        padding-top: 15px;
    }
    
    /* Kontainer Video */
    .video-container {
        position: relative;
        text-align: center;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        background-color: #e9ecef;
        border-radius: 0.5rem;
    }
    
    /* Konten Catatan */
    #catatan-content p {
        white-space: pre-wrap;
        min-height: 150px;
    }

    /* ========================================= */
    /* RESPONSIVENESS MOBILE (Max-width: 767px) */
    /* ========================================= */
    @media (max-width: 767px) {
        /* Container Padding */
        .container-fluid {
            padding-left: 10px;
            padding-right: 10px;
        }

        /* Judul Halaman - Lebih Compact */
        .container-fluid h2 {
            font-size: 1.25rem;
            padding-bottom: 8px;
            margin-bottom: 0.75rem;
        }
        
        .container-fluid h2 i {
            font-size: 1rem;
        }

        .container-fluid p.text-muted {
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        /* Tabel Utama - Card Style untuk Mobile */
        .table-responsive {
            box-shadow: none;
            border-radius: 0;
            margin-top: 10px;
        }

        .table-responsive table {
            display: none; /* Sembunyikan tabel tradisional di mobile */
        }

        /* Card Layout untuk Data Pasien */
        .mobile-card-container {
            display: block;
        }

        .patient-card {
            background: white;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border-left: 4px solid var(--primary-blue);
        }

        .patient-card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border-light);
        }

        .patient-card-header .patient-name {
            font-weight: 600;
            font-size: 1rem;
            color: var(--text-dark);
            flex: 1;
        }

        .patient-card-header .patient-number {
            background: var(--primary-blue);
            color: white;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 600;
            margin-left: 8px;
        }

        .patient-card-body {
            font-size: 0.85rem;
        }

        .patient-info-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
            gap: 10px;
        }

        .patient-info-label {
            color: #6c757d;
            font-weight: 500;
            min-width: 80px;
        }

        .patient-info-value {
            color: var(--text-dark);
            text-align: right;
            flex: 1;
        }

        .patient-card-footer {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .patient-status {
            flex: 1;
        }

        /* Tombol Aksi di Card */
        .btn-riwayat {
            width: auto;
            font-size: 0.8rem !important;
            padding: 0.4rem 0.8rem !important;
            white-space: nowrap;
        }

        /* Modal - Full Screen di Mobile */
        .modal-dialog.modal-xl {
            margin: 0;
            max-width: 100%;
            height: 100vh;
        }

        .modal-content {
            border-radius: 0;
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Modal Header - Compact */
        .modal-header {
            padding: 12px 15px;
            min-height: auto;
        }

        .modal-header h5 {
            font-size: 0.95rem;
            margin: 0;
            line-height: 1.3;
        }

        .modal-header .btn-close {
            padding: 0.5rem;
            margin: 0;
        }

        /* Modal Body - Scrollable */
        .modal-body {
            padding: 10px 12px;
            overflow-y: auto;
            flex: 1;
        }

        /* Navigasi Tab - Compact & Scrollable */
        .nav-tabs {
            flex-wrap: nowrap;
            overflow-x: auto;
            overflow-y: hidden;
            padding-bottom: 0;
            margin-bottom: 12px;
            border-bottom: 2px solid var(--border-light);
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none; /* Firefox */
        }

        .nav-tabs::-webkit-scrollbar {
            display: none; /* Chrome, Safari */
        }

        .nav-tabs .nav-link {
            font-size: 0.75rem;
            white-space: nowrap;
            padding: 8px 12px;
            border-bottom-width: 2px;
        }

        .nav-tabs .nav-link i {
            font-size: 0.7rem;
        }

        .tab-content {
            padding-top: 8px;
        }

        /* Tab Detail - Compact Cards */
        #detail-tab-pane .table {
            display: none;
        }

        .detail-card-item {
            background: var(--bg-light);
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 8px;
            border-left: 3px solid var(--primary-blue);
        }

        .detail-card-label {
            font-weight: 600;
            color: var(--secondary-blue);
            font-size: 0.8rem;
            margin-bottom: 4px;
        }

        .detail-card-value {
            color: var(--text-dark);
            font-size: 0.85rem;
            word-wrap: break-word;
        }

        /* Tab Catatan - Compact */
        #catatan-content .col-md-6 {
            width: 100%;
            margin-bottom: 12px !important;
        }

        #catatan-content .form-label {
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        #catatan-content p {
            font-size: 0.8rem;
            padding: 10px;
            min-height: 100px;
            max-height: 200px;
            overflow-y: auto;
        }

        /* Tab Video - Compact */
        #video-tab-pane .col-md-6 {
            width: 100%;
            margin-bottom: 12px !important;
        }

        #video-tab-pane .card {
            margin-bottom: 0;
        }

        #video-tab-pane .card-header {
            padding: 8px;
            font-size: 0.85rem;
        }

        #video-tab-pane .card-body {
            padding: 10px;
        }

        .video-container {
            min-height: 180px;
        }

        #video-tab-pane video {
            max-height: 200px !important;
        }

        #video-tab-pane .alert {
            font-size: 0.8rem;
            padding: 10px;
            margin: 0;
        }

        /* Tab Feedback - Compact */
        #feedback-tab-pane p.text-muted {
            font-size: 0.8rem;
            margin-bottom: 10px;
        }

        #feedback-tab-pane .form-label {
            font-size: 0.85rem;
            margin-bottom: 6px;
        }

        #feedback-tab-pane textarea {
            font-size: 0.85rem;
            min-height: 120px;
        }

        #feedback-tab-pane button[type="submit"] {
            width: 100%;
            font-size: 0.9rem;
            padding: 10px;
        }

        /* Modal Footer - Compact */
        .modal-footer {
            padding: 10px 12px;
            border-top: 2px solid var(--border-light);
        }

        .modal-footer .btn {
            width: 100%;
            font-size: 0.9rem;
        }

        /* Alert - Compact */
        .alert {
            font-size: 0.85rem;
            padding: 10px 12px;
            margin-bottom: 10px;
        }

        /* Loading Spinner */
        #loading-spinner-detail,
        #loading-spinner-catatan {
            padding: 30px 0 !important;
            font-size: 0.85rem;
        }

        .spinner-border {
            width: 2rem !important;
            height: 2rem !important;
        }
    }

    /* Untuk Desktop - Hide Mobile Cards */
    @media (min-width: 768px) {
        .mobile-card-container {
            display: none;
        }
    }
</style>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Pemeriksaan Selesai</h2>
    <p class="text-muted">Daftar riwayat pemeriksaan yang telah diselesaikan oleh dokter.</p>

    <hr>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show">
            {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- TABEL DATA --}}
    <div class="table-responsive">
        {{-- Loading Overlay --}}
        <div class="loading-overlay" id="loading">
            <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
        
        @if($dataPasien->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fa-solid fa-circle-info me-2"></i>
                Tidak ada riwayat pemeriksaan yang berstatus **Selesai Diperiksa**.
            </div>
        @else
            {{-- Tabel Desktop --}}
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Pasien</th>
                        <th>Tgl Kunjungan</th>
                        <th>Layanan</th>
                        <th>Dokter</th>
                        <th>Status</th>
                        <th style="width: 15%;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataPasien as $index => $pasien)
                        <tr>
                            <td class="fw-bold">{{ $index + 1 }}</td>
                            <td>{{ $pasien->nama_pasien }}</td>
                            <td>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $pasien->layanan->pelayanan ?? '-' }}</td>
                            <td>{{ $pasien->dokter->nama_dokter ?? '-' }}</td>
                            <td>
                                <span class="badge bg-success">
                                    {{ $pasien->status_pemeriksaan }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-riwayat text-white btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        data-video-before="{{ $pasien->video_before ? asset('storage/' . $pasien->video_before) : '' }}" 
                                        data-video-after="{{ $pasien->video_after ? asset('storage/' . $pasien->video_after) : '' }}" 
                                        data-current-feedback="{{ $pasien->feedback }}"
                                        title="Riwayat Pasien Lengkap">
                                    <i class="fa-solid fa-folder-open me-1"></i> Riwayat Pasien
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- Card Layout Mobile --}}
            <div class="mobile-card-container">
                @foreach ($dataPasien as $index => $pasien)
                    <div class="patient-card">
                        <div class="patient-card-header">
                            <div class="patient-name">{{ $pasien->nama_pasien }}</div>
                            <div class="patient-number">#{{ $index + 1 }}</div>
                        </div>
                        <div class="patient-card-body">
                            <div class="patient-info-row">
                                <span class="patient-info-label">Tanggal:</span>
                                <span class="patient-info-value">{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</span>
                            </div>
                            <div class="patient-info-row">
                                <span class="patient-info-label">Layanan:</span>
                                <span class="patient-info-value">{{ $pasien->layanan->pelayanan ?? '-' }}</span>
                            </div>
                            <div class="patient-info-row">
                                <span class="patient-info-label">Dokter:</span>
                                <span class="patient-info-value">{{ $pasien->dokter->nama_dokter ?? '-' }}</span>
                            </div>
                        </div>
                        <div class="patient-card-footer">
                            <div class="patient-status">
                                <span class="badge bg-success">{{ $pasien->status_pemeriksaan }}</span>
                            </div>
                            <button class="btn btn-sm btn-riwayat text-white btn-action-icon" 
                                    data-id="{{ $pasien->id }}" 
                                    data-video-before="{{ $pasien->video_before ? asset('storage/' . $pasien->video_before) : '' }}" 
                                    data-video-after="{{ $pasien->video_after ? asset('storage/' . $pasien->video_after) : '' }}" 
                                    data-current-feedback="{{ $pasien->feedback }}"
                                    title="Riwayat Pasien Lengkap">
                                <i class="fa-solid fa-folder-open me-1"></i> Detail
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Modal UTAMA: Riwayat Pasien Lengkap --}}
<div class="modal fade" id="riwayatPasienModal" tabindex="-1" aria-labelledby="riwayatPasienModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered"> 
        <div class="modal-content">
            <div class="modal-header bg-riwayat text-white">
                <h5 class="modal-title" id="riwayatPasienModalLabel">
                    <i class="fa-solid fa-folder-open me-2"></i> Riwayat Pasien - <span id="riwayat-antrian"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                
                {{-- Navigasi Tab --}}
                <ul class="nav nav-tabs" id="riwayatTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-file-invoice me-1"></i> Detail
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="catatan-tab" data-bs-toggle="tab" data-bs-target="#catatan-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-notes-medical me-1"></i> Catatan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="video-tab" data-bs-toggle="tab" data-bs-target="#video-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-video me-1"></i> Video
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="feedback-tab" data-bs-toggle="tab" data-bs-target="#feedback-tab-pane" type="button" role="tab">
                            <i class="fa-solid fa-comment-dots me-1"></i> Feedback
                        </button>
                    </li>
                </ul>

                {{-- Konten Tab --}}
                <div class="tab-content" id="riwayatTabsContent">
                    
                    {{-- TAB 1: Detail Kunjungan --}}
                    <div class="tab-pane fade show active" id="detail-tab-pane" role="tabpanel" aria-labelledby="detail-tab" tabindex="0">
                        <div id="loading-spinner-detail" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-info" role="status"></div> Loading Detail...
                        </div>
                        
                        {{-- Tabel Desktop --}}
                        <table class="table table-striped table-bordered" id="detail-table">
                            <tbody>
                                <tr><th>Nama Pasien</th><td id="detail-nama"></td><th>Nomor HP</th><td id="detail-hp"></td></tr>
                                <tr><th>Tgl Lahir / JK</th><td id="detail-tgl-jk"></td><th>Pendamping</th><td id="detail-pendamping"></td></tr>
                                <tr><th>Tgl Kunjungan</th><td id="detail-tgl-kunjungan"></td><th>Waktu Kunjungan</th><td id="detail-waktu"></td></tr>
                                <tr><th>Layanan</th><td id="detail-layanan"></td><th>Kategori</th><td id="detail-kategori"></td></tr>
                                <tr><th>Dokter Penanggung Jawab</th><td id="detail-dokter"></td><th>Status Pemeriksaan</th><td id="detail-status-pemeriksaan"></td></tr>
                                <tr><th>Alamat</th><td colspan="3" id="detail-alamat"></td></tr>
                                <tr><th>Keluhan</th><td colspan="3" id="detail-keluhan"></td></tr>
                                <tr><th>Status Berkas</th><td colspan="3" id="detail-status-berkas"></td></tr>
                            </tbody>
                        </table>

                        {{-- Cards Mobile --}}
                        <div id="detail-cards-mobile" style="display: none;">
                            <div class="detail-card-item">
                                <div class="detail-card-label">Nama Pasien</div>
                                <div class="detail-card-value" id="detail-nama-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Nomor HP</div>
                                <div class="detail-card-value" id="detail-hp-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Tanggal Lahir / Jenis Kelamin</div>
                                <div class="detail-card-value" id="detail-tgl-jk-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Pendamping</div>
                                <div class="detail-card-value" id="detail-pendamping-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Tanggal Kunjungan</div>
                                <div class="detail-card-value" id="detail-tgl-kunjungan-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Waktu Kunjungan</div>
                                <div class="detail-card-value" id="detail-waktu-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Layanan</div>
                                <div class="detail-card-value" id="detail-layanan-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Kategori</div>
                                <div class="detail-card-value" id="detail-kategori-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Dokter Penanggung Jawab</div>
                                <div class="detail-card-value" id="detail-dokter-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Status Pemeriksaan</div>
                                <div class="detail-card-value" id="detail-status-pemeriksaan-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Alamat</div>
                                <div class="detail-card-value" id="detail-alamat-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Keluhan</div>
                                <div class="detail-card-value" id="detail-keluhan-mobile"></div>
                            </div>
                            <div class="detail-card-item">
                                <div class="detail-card-label">Status Berkas</div>
                                <div class="detail-card-value" id="detail-status-berkas-mobile"></div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 2: Catatan Dokter --}}
                    <div class="tab-pane fade" id="catatan-tab-pane" role="tabpanel" aria-labelledby="catatan-tab" tabindex="0">
                        <div id="loading-spinner-catatan" class="text-center py-5" style="display: block;">
                            <div class="spinner-border text-success" role="status"></div> Loading Catatan...
                        </div>
                        <div class="row" id="catatan-content" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold"><i class="fa-solid fa-clipboard-check me-1"></i> Catatan Pemeriksaan</label>
                                <p id="view_catatan_pemeriksaan" class="border p-3 rounded bg-light"></p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold"><i class="fa-solid fa-capsules me-1"></i> Catatan Obat</label>
                                <p id="view_catatan_obat" class="border p-3 rounded bg-light"></p>
                            </div>
                        </div>
                    </div>
                    
                    {{-- TAB 3: Video --}}
                    <div class="tab-pane fade" id="video-tab-pane" role="tabpanel" aria-labelledby="video-tab" tabindex="0">
                        <p class="text-muted mb-4">Video sebelum dan sesudah pemeriksaan.</p>
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light fw-bold text-center">Video Sebelum</div>
                                    <div class="card-body">
                                        <div class="video-container" id="player-before-tab">
                                            <video id="video-before-player-tab" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                            <div id="video-before-not-found-tab" class="alert alert-info text-center" style="display: block;">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada video
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-4">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light fw-bold text-center">Video Sesudah</div>
                                    <div class="card-body">
                                        <div class="video-container" id="player-after-tab">
                                            <video id="video-after-player-tab" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                            <div id="video-after-not-found-tab" class="alert alert-info text-center" style="display: block;">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada video
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- TAB 4: Feedback --}}
                    <div class="tab-pane fade" id="feedback-tab-pane" role="tabpanel" aria-labelledby="feedback-tab" tabindex="0">
                        <form id="form-feedback-tab" method="POST" action="">
                            @csrf
                            <p class="text-muted">Bagaimana pengalaman Anda dengan pelayanan kami? Feedback Anda sangat berharga! Anda dapat mengubahnya kapan saja.</p>
                            <div class="mb-3">
                                <label for="feedback_text_tab" class="form-label fw-bold">Feedback Anda</label>
                                <textarea name="feedback" id="feedback_text_tab" class="form-control" rows="5" placeholder="Tulis feedback Anda di sini..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-warning text-dark mt-2"><i class="fa-solid fa-save me-1"></i> Simpan Feedback</button>
                        </form>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
    // Fungsi untuk menampilkan overlay loading pada tabel utama
    function showLoading() {
        document.getElementById('loading').style.display = 'flex';
        const table = document.querySelector('.table-responsive table');
        if (table) table.style.opacity = '0.5';
    }
    
    // Fungsi untuk menghentikan video
    function stopVideos() {
        const playerBefore = document.getElementById('video-before-player-tab');
        const playerAfter = document.getElementById('video-after-player-tab');
        if (playerBefore) playerBefore.pause();
        if (playerAfter) playerAfter.pause();
    }

    // Fungsi untuk update tampilan detail (desktop/mobile)
    function updateDetailDisplay(data) {
        const d = data.data;
        
        // Update Desktop Table
        document.getElementById('detail-nama').textContent = d.nama_pasien;
        document.getElementById('detail-hp').textContent = d.nomor_hp;
        document.getElementById('detail-tgl-jk').textContent = `${d.tgl_lahir} / ${d.jenis_kelamin}`;
        document.getElementById('detail-pendamping').textContent = d.pendamping || '-';
        document.getElementById('detail-dokter').textContent = d.dokter_nama || '-';
        document.getElementById('detail-tgl-kunjungan').textContent = d.tgl_kunjungan;
        document.getElementById('detail-waktu').textContent = d.waktu_kunjungan;
        document.getElementById('detail-layanan').textContent = d.layanan;
        document.getElementById('detail-kategori').textContent = d.kategori_pendaftaran;
        document.getElementById('detail-alamat').textContent = d.alamat;
        document.getElementById('detail-keluhan').textContent = d.keluhan;
        document.getElementById('detail-status-pemeriksaan').textContent = d.status_pemeriksaan;
        document.getElementById('detail-status-berkas').textContent = d.status_berkas;

        // Update Mobile Cards
        document.getElementById('detail-nama-mobile').textContent = d.nama_pasien;
        document.getElementById('detail-hp-mobile').textContent = d.nomor_hp;
        document.getElementById('detail-tgl-jk-mobile').textContent = `${d.tgl_lahir} / ${d.jenis_kelamin}`;
        document.getElementById('detail-pendamping-mobile').textContent = d.pendamping || '-';
        document.getElementById('detail-dokter-mobile').textContent = d.dokter_nama || '-';
        document.getElementById('detail-tgl-kunjungan-mobile').textContent = d.tgl_kunjungan;
        document.getElementById('detail-waktu-mobile').textContent = d.waktu_kunjungan;
        document.getElementById('detail-layanan-mobile').textContent = d.layanan;
        document.getElementById('detail-kategori-mobile').textContent = d.kategori_pendaftaran;
        document.getElementById('detail-alamat-mobile').textContent = d.alamat;
        document.getElementById('detail-keluhan-mobile').textContent = d.keluhan;
        document.getElementById('detail-status-pemeriksaan-mobile').textContent = d.status_pemeriksaan;
        document.getElementById('detail-status-berkas-mobile').textContent = d.status_berkas;
    }

    // Fungsi untuk toggle tampilan detail berdasarkan ukuran layar
    function toggleDetailView() {
        const isMobile = window.innerWidth < 768;
        const detailTable = document.getElementById('detail-table');
        const detailCards = document.getElementById('detail-cards-mobile');
        
        if (isMobile) {
            detailTable.style.display = 'none';
            detailCards.style.display = 'block';
        } else {
            detailTable.style.display = 'table';
            detailCards.style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const riwayatModal = new bootstrap.Modal(document.getElementById('riwayatPasienModal'));
        
        const formFeedback = document.getElementById('form-feedback-tab');
        const riwayatTabsElement = document.getElementById('riwayatTabs');

        // Templat URL API
        const detailUrlTemplate = `{{ route('pasien.detail', ['id' => 'PASIEN_ID']) }}`;
        const getCatatanUrlTemplate = `{{ route('pasien.get-catatan', ['id' => 'PASIEN_ID']) }}`;
        const submitFeedbackUrlTemplate = `{{ route('pasien.submit-feedback', ['id' => 'PASIEN_ID']) }}`;
        
        let currentPasienId = null;
        
        // Sembunyikan loading overlay di tabel utama saat DOM selesai dimuat
        document.getElementById('loading').style.display = 'none';

        // Toggle detail view pada resize
        window.addEventListener('resize', toggleDetailView);

        // Event listener untuk Tombol Riwayat Pasien
        document.querySelectorAll('.btn-riwayat').forEach(button => {
            button.addEventListener('click', function() {
                currentPasienId = this.dataset.id;
                const videoBeforePath = this.dataset.videoBefore;
                const videoAfterPath = this.dataset.videoAfter;
                const currentFeedback = this.dataset.currentFeedback;
                
                // 1. Tampilkan Modal
                riwayatModal.show();
                
                // 2. Reset dan Tampilkan Tab Detail sebagai default
                const detailTab = document.getElementById('detail-tab');
                const tabInstance = bootstrap.Tab.getInstance(detailTab);
                if (tabInstance) {
                    tabInstance.show();
                } else {
                    new bootstrap.Tab(detailTab).show();
                }

                // 3. Muat Data Detail
                loadDetailPasien(currentPasienId);
                
                // 4. Atur Video
                setupVideoPlayers(videoBeforePath, videoAfterPath);
                
                // 5. Atur Form Feedback
                setupFeedbackForm(currentPasienId, currentFeedback);
            });
        });

        // Event listener saat tab Catatan dibuka (Lazy Load)
        riwayatTabsElement.addEventListener('shown.bs.tab', event => {
            const activeTabId = event.target.id;
            
            // Hentikan video saat pindah tab
            stopVideos(); 

            if (activeTabId === 'catatan-tab' && currentPasienId) {
                loadCatatanDokter(currentPasienId);
            }
        });
        
        // Event listener untuk menghentikan video saat modal ditutup
        document.getElementById('riwayatPasienModal').addEventListener('hidden.bs.modal', function () {
            stopVideos();
        });

        // =========================================================
        // FUNGSI UTAMA UNTUK MENGISI KONTEN TAB
        // =========================================================

        function loadDetailPasien(pasienId) {
            const loading = document.getElementById('loading-spinner-detail');
            const detailTable = document.getElementById('detail-table');
            const detailCards = document.getElementById('detail-cards-mobile');
            
            loading.style.display = 'block';
            detailTable.style.display = 'none';
            detailCards.style.display = 'none';

            fetch(detailUrlTemplate.replace('PASIEN_ID', pasienId))
                .then(response => response.json())
                .then(data => {
                    document.getElementById('riwayat-antrian').textContent = `Antrian: ${data.data.nomor_antrian}`;
                    
                    // Update semua field
                    updateDetailDisplay(data);
                    
                    // Toggle tampilan sesuai ukuran layar
                    toggleDetailView();
                    
                    loading.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengambil detail kunjungan.');
                    loading.style.display = 'none';
                });
        }
        
        function loadCatatanDokter(pasienId) {
            const loading = document.getElementById('loading-spinner-catatan');
            const content = document.getElementById('catatan-content');
            
            loading.style.display = 'block';
            content.style.display = 'none';
            
            // Teks sementara saat memuat
            document.getElementById('view_catatan_pemeriksaan').textContent = 'Memuat...';
            document.getElementById('view_catatan_obat').textContent = 'Memuat...';

            fetch(getCatatanUrlTemplate.replace('PASIEN_ID', pasienId))
                .then(response => response.json())
                .then(data => {
                    const pemeriksaan = data.data.catatan_pemeriksaan || 'Tidak ada catatan pemeriksaan.';
                    const obat = data.data.catatan_obat || 'Tidak ada catatan obat/resep.';

                    document.getElementById('view_catatan_pemeriksaan').textContent = pemeriksaan;
                    document.getElementById('view_catatan_obat').textContent = obat;
                    
                    loading.style.display = 'none';
                    content.style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('view_catatan_pemeriksaan').textContent = 'Gagal memuat.';
                    document.getElementById('view_catatan_obat').textContent = 'Gagal memuat.';
                    loading.style.display = 'none';
                    content.style.display = 'flex';
                });
        }
        
        function setupVideoPlayers(videoBeforePath, videoAfterPath) {
            const playerBefore = document.getElementById('video-before-player-tab');
            const notFoundBefore = document.getElementById('video-before-not-found-tab');
            const playerAfter = document.getElementById('video-after-player-tab');
            const notFoundAfter = document.getElementById('video-after-not-found-tab');

            // Video Before
            if (videoBeforePath) {
                playerBefore.src = videoBeforePath;
                playerBefore.load();
                playerBefore.style.display = 'block';
                notFoundBefore.style.display = 'none';
            } else {
                playerBefore.removeAttribute('src');
                playerBefore.style.display = 'none';
                notFoundBefore.style.display = 'block';
            }

            // Video After
            if (videoAfterPath) {
                playerAfter.src = videoAfterPath;
                playerAfter.load();
                playerAfter.style.display = 'block';
                notFoundAfter.style.display = 'none';
            } else {
                playerAfter.removeAttribute('src');
                playerAfter.style.display = 'none';
                notFoundAfter.style.display = 'block';
            }
        }
        
        function setupFeedbackForm(pasienId, currentFeedback) {
            formFeedback.action = submitFeedbackUrlTemplate.replace('PASIEN_ID', pasienId);
            document.getElementById('feedback_text_tab').value = currentFeedback || '';
        }

        // Event handler submit form Feedback
        formFeedback.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(formFeedback.action, {
                method: 'POST',
                body: new FormData(formFeedback),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Update data-current-feedback di tombol tabel
                    const feedbackText = document.getElementById('feedback_text_tab').value;
                    const buttons = document.querySelectorAll(`.btn-riwayat[data-id="${currentPasienId}"]`);
                    buttons.forEach(button => {
                        button.dataset.currentFeedback = feedbackText;
                    });
                    
                    alert(data.message);
                    riwayatModal.hide();
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan feedback: ' + (data.message || 'Terjadi kesalahan.'));
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan server saat menyimpan feedback.');
                console.error('Error:', error);
            });
        });
    });
</script>

@endsection
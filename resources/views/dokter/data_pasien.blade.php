@extends('layouts.dokter-sidebar')

@section('content')

{{-- Tambahkan CSS Plyr --}}
<link rel="stylesheet" href="https://cdn.plyr.io/3.7.8/plyr.css" />

{{-- Hidden Input untuk Nama Dokter yang Login --}}
<input type="hidden" id="dokter-login-nama" value="{{ Auth::user()->nama_dokter ?? 'Dokter ID: ' . Auth::id() }}">

<style>
    /* Variabel Warna */
    :root {
        --primary-blue: #007bff;
        --secondary-blue: #0056b3;
        --text-dark: #343a40;
        --bg-light: #f8f9fa;
        --border-light: #dee2e6;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Header & Container */
    .container-fluid h2 {
        color: var(--secondary-blue);
        border-bottom: 2px solid var(--border-light);
        padding-bottom: 10px;
        font-size: 1.5rem;
    }
    
    /* Tombol Toggle Hari Ini/Esok */
    .btn-group .btn {
        border-radius: 0.5rem;
        margin-right: 5px;
        transition: all 0.2s ease;
        font-weight: 500;
        border: 1px solid var(--primary-blue);
    }

    .btn-group .btn.btn-primary.active {
        background-color: var(--primary-blue) !important;
        border-color: var(--primary-blue) !important;
        color: white !important;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }
    
    .btn-group .btn-secondary {
        background-color: var(--bg-light) !important;
        border-color: var(--border-light) !important;
        color: var(--text-dark) !important;
    }
    
    .btn-group .btn-secondary:hover {
        background-color: #e9ecef !important;
    }

    /* Tampilan Tabel Desktop */
    .table-responsive {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        overflow: hidden;
        margin-top: 20px;
        position: relative;
        min-height: 200px;
    }
    
    .table-primary thead tr, .table-secondary thead tr {
        background-color: var(--primary-blue);
        color: white;
        font-weight: 600;
        border-bottom: none;
    }

    /* Tombol Aksi */
    .btn-sm {
        padding: 0.3rem 0.6rem;
        border-radius: 0.3rem;
        font-size: 0.8rem;
    }
    
    .btn-info { background-color: var(--primary-blue) !important; border-color: var(--primary-blue) !important; }
    .btn-info:hover { background-color: var(--secondary-blue) !important; }
    
    .btn-warning { background-color: #ffc107 !important; border-color: #ffc107 !important; color: var(--text-dark) !important; }
    .btn-warning:hover { background-color: #e0a800 !important; }

    .btn-secondary { background-color: #6c757d !important; border-color: #6c757d !important; }
    .btn-secondary:hover { background-color: #5a6268 !important; }
    
    .btn-action-icon {
        padding: 0.3rem !important;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Modal Styling */
    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .modal-header.bg-info { background-color: var(--primary-blue) !important; }
    .modal-header.bg-success { background-color: #28a745 !important; }

    .modal-body table th {
        width: 25%;
        font-weight: 600;
        color: var(--secondary-blue);
    }
    
    .video-container {
        position: relative;
        text-align: center;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        padding: 10px; 
    }

    /* Atur agar Plyr player tidak terlalu besar di modal */
    .plyr--video {
        width: 100%;
        max-height: 300px;
    }

    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 10;
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: opacity 0.3s ease-in-out;
    }

    /* ============================================ */
    /* RESPONSIVE MOBILE CARD STYLES */
    /* ============================================ */
    
    /* Container yang hanya muncul di mobile */
    .mobile-cards-container {
        display: none;
        margin-top: 1rem;
    }
    
    .mobile-card {
        background: white;
        border: 1px solid var(--border-light);
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 1rem;
        box-shadow: var(--shadow-sm);
    }

    .mobile-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid var(--border-light);
    }

    .mobile-card-antrian {
        font-size: 1.25rem;
        font-weight: bold;
        color: var(--primary-blue);
    }
    
    .mobile-card-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
    }

    .mobile-card-label {
        font-weight: 600;
        color: var(--text-dark);
        min-width: 120px; /* Lebar minimum untuk label agar rapi */
    }
    
    .mobile-card-actions {
        display: flex;
        flex-wrap: wrap; /* Biar tombol bisa pindah baris jika layar sempit */
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid var(--border-light);
    }
    
    /* Atur lebar tombol aksi di mobile card */
    .mobile-card-actions .btn {
        flex: 1 1 auto; /* Memungkinkan tombol mengisi ruang dan mengatur ulang */
        font-size: 0.85rem;
        padding: 0.4rem 0.6rem;
    }
    
    /* MEDIA QUERIES */
    /* Tampilkan Mobile Card dan sembunyikan Table di layar sempit (Tablet/Mobile) */
    @media (max-width: 767.98px) {
        .table-responsive table {
            display: none;
        }
        .mobile-cards-container {
            display: block;
        }
        
        .filter-group .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        
        /* Penyesuaian Modal untuk layar sangat kecil */
        .modal-body table th,
        .modal-body table td {
            padding: 0.4rem;
            display: block;
            width: 100% !important;
        }

        .video-container {
            min-height: 200px;
        }

        .video-container video,
        .video-container img {
            max-height: 200px !important;
        }
        
        .mobile-card-actions {
            justify-content: space-between;
        }
        .mobile-card-actions .btn {
            flex: 1 1 45%; /* Dua tombol per baris */
        }
    }
    
    /* Sembunyikan Mobile Card di Desktop/Layar Besar */
    @media (min-width: 768px) {
        .mobile-cards-container {
            display: none;
        }
    }
</style>

<div class="container-fluid">
    <h2 class="mb-3"><i class="fa-solid fa-users me-2"></i> Data Pasien</h2>
    <p class="text-muted">Daftar pasien yang dijadwalkan untuk kunjungan.</p>

    <hr>

    <div class="row mb-3">
        <div class="col-12">
            
            {{-- Tombol Navigasi Toggle --}}
            <div class="btn-group mb-3" role="group" aria-label="Jadwal Pasien">
                <button type="button" class="btn btn-primary active" id="btn-today">
                    <i class="fa-solid fa-calendar-day me-1"></i> Hari Ini
                </button>
                <button type="button" class="btn btn-secondary" id="btn-upcoming">
                    <i class="fa-solid fa-calendar-alt me-1"></i> Mendatang
                </button>
            </div>

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
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    Mohon periksa unggahan file Anda.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

        </div>
    </div>
    
    {{-- Data Tabs --}}
    @php
        $dataTabs = [
            'today' => ['label' => 'Jadwal Hari Ini', 'data' => $pasienHariIni, 'color' => 'primary'],
            'upcoming' => ['label' => 'Jadwal Mendatang', 'data' => $pasienMendatang, 'color' => 'secondary']
        ];
    @endphp

    @foreach ($dataTabs as $key => $tab)
        <div id="pasien-{{ $key }}" style="{{ $key !== 'today' ? 'display: none;' : '' }}">
            <h4 class="mb-3 mt-3 text-{{ $tab['color'] }}">{{ $tab['label'] }}</h4>
            
            {{-- FILTER --}}
            <form method="GET" action="{{ route('dokter.data-pasien') }}" id="filter-form-{{ $key }}" class="filter-group row mb-3 align-items-end" style="{{ $key === 'today' ? 'display: flex;' : 'display: none;' }}">
                <input type="hidden" name="tab" value="{{ $key }}">
                
                @if ($key == 'upcoming')
                    <div class="col-12 col-md-4 mb-2">
                        <label for="filter_date_{{ $key }}" class="form-label">Tanggal Kunjungan</label>
                        <select name="date" id="filter_date_{{ $key }}" class="form-select">
                            <option value="">-- Semua Tanggal --</option>
                            @foreach ($availableDates as $date)
                                <option value="{{ $date }}" {{ $currentFilterDate == $date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->isoFormat('D MMM YYYY') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                <div class="col-12 col-md-4 mb-2">
                    <label for="filter_status_pemeriksaan_{{ $key }}" class="form-label">Status Pemeriksaan</label>
                    <select name="status_pemeriksaan" id="filter_status_pemeriksaan_{{ $key }}" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="Belum Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Belum Diperiksa' ? 'selected' : '' }}>Belum Diperiksa</option>
                        <option value="Sedang Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Sedang Diperiksa' ? 'selected' : '' }}>Sedang Diperiksa</option>
                        <option value="Selesai Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Selesai Diperiksa' ? 'selected' : '' }}>Selesai Diperiksa</option>
                    </select>
                </div>
                
                <div class="col-12 col-md-4 mb-2">
                    <label for="filter_nama_pasien_{{ $key }}" class="form-label">Cari Nama Pasien</label>
                    <input type="text" name="nama_pasien" id="filter_nama_pasien_{{ $key }}" class="form-control" placeholder="Ketik nama..." value="{{ $currentFilterNamaPasien }}">
                </div>
                
                <div class="col-12 col-md-4 mb-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fa-solid fa-filter"></i> Filter
                    </button>
                    <a href="{{ route('dokter.data-pasien', ['tab' => $key]) }}" class="btn btn-outline-secondary flex-fill">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <div class="loading-overlay" id="loading-{{ $key }}">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                @if($tab['data']->isEmpty())
                    <div class="alert alert-info text-center">Tidak ada pasien ditemukan.</div>
                @else
                    {{-- Desktop Table View --}}
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-{{ $tab['color'] }}">
                            <tr>
                                @if ($key == 'upcoming')
                                    <th>Tanggal</th>
                                @endif
                                <th>Antrian</th>
                                <th>Nama Pasien</th>
                                <th>Kategori</th>
                                <th>Layanan</th>
                                <th>Waktu</th>
                                <th>Status</th>
                                <th>Dokter</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tab['data'] as $pasien)
                                <tr>
                                    @if ($key == 'upcoming')
                                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM') }}</td>
                                    @endif
                                    <td class="fw-bold">{{ $pasien->nomor_antrian }}</td>
                                    <td>{{ $pasien->nama_pasien }}</td>
                                    <td>{{ $pasien->kategori_pendaftaran }}</td>
                                    <td>{{ $pasien->layanan_id ?? '-' }}</td>
                                    <td>{{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}</td>
                                    <td>
                                        @php
                                            $badgeClass = 'bg-danger';
                                            if ($pasien->status_pemeriksaan == 'Sedang Diperiksa') {
                                                $badgeClass = 'bg-primary';
                                            } elseif ($pasien->status_pemeriksaan == 'Selesai Diperiksa') {
                                                $badgeClass = 'bg-success';
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ $pasien->status_pemeriksaan }}</span>
                                    </td>
                                    <td>{{ $pasien->dokter->nama_dokter ?? '-' }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn btn-sm btn-info text-white me-1 btn-detail btn-action-icon" data-id="{{ $pasien->id }}" title="Detail">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </button>
                                            <button class="btn btn-sm btn-warning me-1 btn-status-pemeriksaan btn-action-icon" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_pemeriksaan }}" title="Status">
                                                <i class="fa-solid fa-stethoscope"></i>
                                            </button>
                                            <button class="btn btn-sm btn-primary me-1 btn-video btn-action-icon" 
                                                data-id="{{ $pasien->id }}" 
                                                data-video-before="{{ $pasien->video_before ? asset('public/storage/' . $pasien->video_before) : '' }}" 
                                                data-video-after="{{ $pasien->video_after ? asset('public/storage/' . $pasien->video_after) : '' }}" 
                                                title="File">
                                                <i class="fa-solid fa-video"></i>
                                            </button>
                                            <button class="btn btn-sm btn-success btn-catatan btn-action-icon" data-id="{{ $pasien->id }}" title="Catatan">
                                                <i class="fa-solid fa-notes-medical"></i>
                                            </button>
                                            {{-- Tombol Feedback dihilangkan dari tabel desktop --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    {{-- Mobile Card View (BARU) --}}
                    <div class="mobile-cards-container">
                        @foreach ($tab['data'] as $pasien)
                            <div class="mobile-card">
                                <div class="mobile-card-header">
                                    <div class="mobile-card-antrian">Antrian: {{ $pasien->nomor_antrian }}</div>
                                    @php
                                        $badgeClass = 'bg-danger';
                                        if ($pasien->status_pemeriksaan == 'Sedang Diperiksa') {
                                            $badgeClass = 'bg-primary';
                                        } elseif ($pasien->status_pemeriksaan == 'Selesai Diperiksa') {
                                            $badgeClass = 'bg-success';
                                        }
                                    @endphp
                                    <span class="badge {{ $badgeClass }}">{{ $pasien->status_pemeriksaan }}</span>
                                </div>
                                <div class="mobile-card-body">
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-user me-1"></i> Nama Pasien:</span>
                                        <span class="mobile-card-value text-end fw-bold">{{ $pasien->nama_pasien }}</span>
                                    </div>
                                    @if ($key == 'upcoming')
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-calendar-alt me-1"></i> Tanggal Kunjungan:</span>
                                        <span class="mobile-card-value text-end">{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</span>
                                    </div>
                                    @endif
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-clock me-1"></i> Waktu:</span>
                                        <span class="mobile-card-value text-end">{{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-hand-holding-medical me-1"></i> Layanan:</span>
                                        <span class="mobile-card-value text-end">{{ $pasien->layanan_id ?? '-' }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-user-md me-1"></i> Dokter PJ:</span>
                                        <span class="mobile-card-value text-end">{{ $pasien->dokter->nama_dokter ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="mobile-card-actions">
                                    <button class="btn btn-info text-white btn-detail" data-id="{{ $pasien->id }}" title="Detail">
                                        <i class="fa-solid fa-file-invoice"></i> Detail
                                    </button>
                                    <button class="btn btn-warning btn-status-pemeriksaan" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_pemeriksaan }}" title="Ubah Status">
                                        <i class="fa-solid fa-stethoscope"></i> Status
                                    </button>
                                    <button class="btn btn-primary btn-video" 
                                        data-id="{{ $pasien->id }}" 
                                        data-video-before="{{ $pasien->video_before ? asset('public/storage/' . $pasien->video_before) : '' }}" 
                                        data-video-after="{{ $pasien->video_after ? asset('public/storage/' . $pasien->video_after) : '' }}"
                                        title="File Pemeriksaan">
                                        <i class="fa-solid fa-video"></i> File
                                    </button>
                                    <button class="btn btn-success btn-catatan" data-id="{{ $pasien->id }}" title="Catatan">
                                        <i class="fa-solid fa-notes-medical"></i> Catatan
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @endforeach

</div>

{{-- MODALS (Tidak Berubah Fungsionalitas) --}}
{{-- Modal 1: Detail Pasien --}}
<div class="modal fade" id="detailPasienModal" tabindex="-1" aria-labelledby="detailPasienModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailPasienModalLabel">Detail Pasien - <span id="detail-antrian"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr><th>Nama Pasien</th><td id="detail-nama"></td><th>Nomor HP</th><td id="detail-hp"></td></tr>
                        <tr><th>Tgl Lahir / JK</th><td id="detail-tgl-jk"></td><th>Pendamping</th><td id="detail-pendamping"></td></tr>
                        <tr><th>Tgl Kunjungan</th><td id="detail-tgl-kunjungan"></td><th>Waktu Kunjungan</th><td id="detail-waktu"></td></tr>
                        <tr><th>Layanan</th><td id="detail-layanan"></td><th>Kategori</th><td id="detail-kategori"></td></tr>
                        <tr><th>Dokter Penanggung Jawab</th><td colspan="3" id="detail-dokter"></td></tr>
                        <tr><th>Alamat</th><td colspan="3" id="detail-alamat"></td></tr>
                        <tr><th>Keluhan</th><td colspan="3" id="detail-keluhan"></td></tr>
                        <tr><th>Status Pemeriksaan</th><td id="detail-status-pemeriksaan"></td><th>Status Berkas</th><td id="detail-status-berkas"></td></tr>
                    </tbody>
                </table>
                <div id="loading-spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-info" role="status"></div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal 2: Ubah Status Pemeriksaan --}}
<div class="modal fade" id="statusPemeriksaanModal" tabindex="-1" aria-labelledby="statusPemeriksaanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="statusPemeriksaanModalLabel">
                    <i class="fa-solid fa-stethoscope me-2"></i>Ubah Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-status-pemeriksaan" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div id="status-loading-spinner" class="text-center mb-3" style="display: none;">
                        <div class="spinner-border text-warning" role="status"></div> Loading...
                    </div>

                    <div id="status-content" style="display: none;">
                        <p class="mb-1">
                            Dokter Penanggung Jawab: 
                            <strong id="current-dokter-text">N/A</strong>
                        </p>
                        <p class="mb-2 text-primary">
                            <i class="fa-solid fa-user-tie me-1"></i> Anda Login Sebagai: 
                            <strong id="dokter-yg-login-nama"></strong>
                        </p>
                        <p>
                            Status saat ini: 
                            <strong id="current-status-text"></strong>
                        </p>
    
                        <div class="mb-3">
                            <label for="status_pemeriksaan_select" class="form-label">Status Pemeriksaan Baru</label>
                            <select name="status_pemeriksaan" id="status_pemeriksaan_select" class="form-select" required>
                                <option value="Belum Diperiksa">Belum Diperiksa</option>
                                <option value="Sedang Diperiksa">Sedang Diperiksa</option>
                                <option value="Selesai Diperiksa">Selesai Diperiksa</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer" id="status-modal-footer" style="display: none;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 3: Unggah dan Lihat Video/Foto --}}
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="videoModalLabel">
                    <i class="fa-solid fa-video me-2"></i>File Pemeriksaan (Video/Foto)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="form-upload-video" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-3">Unggah file baru atau lihat yang sudah ada. <span class="fw-bold text-danger">Maks. Video 25mb, Foto 2.5mb.</span></p>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light fw-bold">Sebelum Pemeriksaan</div>
                                <div class="card-body">
                                    
                                    <div class="video-container mb-3" id="player-before">
                                        
                                        <div class="loading-overlay" id="upload-loading-before" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Uploading...</span>
                                            </div>
                                            <p class="mt-2 text-primary">Mengunggah...</p>
                                        </div>

                                        <img id="image-before-player" style="width: 100%; max-height: 300px; display: none;" class="rounded" alt="Foto Sebelum">
                                        
                                        <video id="video-before-player" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                        
                                        <div id="file-before-not-found" class="alert alert-info text-center" style="display: block;">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada file.
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="video_before_file" class="form-label">Unggah File Baru</label>
                                        <input class="form-control @error('video_before') is-invalid @enderror" type="file" id="video_before_file" name="video_before" accept="image/*,video/mp4,video/quicktime">
                                        <small class="text-muted">MP4/MOV, JPEG/PNG/HEIC</small>
                                        @error('video_before')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-2 btn-delete-video" data-type="video_before" data-bs-target="#confirmDeleteModal" data-bs-toggle="modal" id="btn-delete-before" style="display: none;">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus File
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light fw-bold">Sesudah Pemeriksaan</div>
                                <div class="card-body">
                                    
                                    <div class="video-container mb-3" id="player-after">
                                        
                                        <div class="loading-overlay" id="upload-loading-after" style="display: none;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Uploading...</span>
                                            </div>
                                            <p class="mt-2 text-primary">Mengunggah...</p>
                                        </div>

                                        <img id="image-after-player" style="width: 100%; max-height: 300px; display: none;" class="rounded" alt="Foto Sesudah">

                                        <video id="video-after-player" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                        
                                        <div id="file-after-not-found" class="alert alert-info text-center" style="display: block;">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada file.
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="video_after_file" class="form-label">Unggah File Baru</label>
                                        <input class="form-control @error('video_after') is-invalid @enderror" type="file" id="video_after_file" name="video_after" accept="image/*,video/mp4,video/quicktime">
                                        <small class="text-muted">MP4/MOV, JPEG/PNG/HEIC</small>
                                        @error('video_after')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-2 btn-delete-video" data-type="video_after" data-bs-target="#confirmDeleteModal" data-bs-toggle="modal" id="btn-delete-after" style="display: none;">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus File
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-video">Unggah & Simpan</button>
                </div>
            </form>
            
            <form id="form-delete-video" method="POST" action="" style="display: none;">
                @csrf
                <input type="hidden" name="video_type" id="delete-video-type">
            </form>
        </div>
    </div>
</div>

{{-- Modal 4: Catatan Pemeriksaan & Obat --}}
<div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="catatanModalLabel">
                    <i class="fa-solid fa-file-waveform me-2"></i>Catatan: <span id="catatan-pasien-id" class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-update-catatan" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div id="catatan-alert-success" class="alert alert-success d-none"></div>
                    <div id="catatan-alert-error" class="alert alert-danger d-none"></div>
                    <div id="catatan-loading" class="text-center" style="display: none;"><div class="spinner-border text-success" role="status"></div> Loading...</div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="catatan_pemeriksaan" class="form-label fw-bold"><i class="fa-solid fa-clipboard-check me-1"></i> Catatan Pemeriksaan</label>
                            <textarea name="catatan_pemeriksaan" id="catatan_pemeriksaan" class="form-control" rows="8" placeholder="Tulis hasil pemeriksaan..."></textarea>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2 btn-delete-catatan" data-field="catatan_pemeriksaan" id="btn-delete-pemeriksaan"><i class="fa-solid fa-trash-can me-1"></i> Hapus</button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="catatan_obat" class="form-label fw-bold"><i class="fa-solid fa-capsules me-1"></i> Catatan Obat</label>
                            <textarea name="catatan_obat" id="catatan_obat" class="form-control" rows="8" placeholder="Tulis resep obat..."></textarea>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2 btn-delete-catatan" data-field="catatan_obat" id="btn-delete-obat"><i class="fa-solid fa-trash-can me-1"></i> Hapus</button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success" id="btn-save-catatan"><i class="fa-solid fa-save me-1"></i> Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i> Konfirmasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>Yakin hapus <span id="video-name-confirm" class="fw-bold"></span>?</p>
                <p class="text-danger small">Tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete"><i class="fa-solid fa-trash-can me-1"></i> Hapus</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 5: Lihat Feedback (Tetap Disertakan) --}}
<div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="feedbackModalLabel">
                    <i class="fa-solid fa-comment-dots me-2"></i> Feedback Pasien: <span id="feedback-pasien-nama"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Feedback yang diberikan pasien setelah pemeriksaan selesai.</p>
                <div class="p-3 border rounded bg-light" id="feedback-content">
                    <p id="feedback-text-view" class="mb-0 text-dark">Memuat...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>


{{-- JAVASCRIPT --}}
<script src="https://cdn.plyr.io/3.7.8/plyr.js"></script> 

<script>
    // Variable untuk menyimpan instance Plyr
    let plyrBefore = null;
    let plyrAfter = null;

    function showLoading(key) {
        document.getElementById(`loading-${key}`).style.display = 'flex';
        const table = document.querySelector(`#pasien-${key} .table-responsive table`);
        if (table) table.style.opacity = '0.5';
        const alert = document.querySelector(`#pasien-${key} .alert-info`);
        if (alert) alert.style.opacity = '0.5';
    }

    function isImageFile(path) {
        if (!path) return false;
        const extension = path.split('.').pop().toLowerCase();
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'].includes(extension);
    }
    
    function isVideoFile(path) {
        if (!path) return false;
        const extension = path.split('.').pop().toLowerCase();
        return ['mp4', 'mov', 'flv', 'avi', 'wmv'].includes(extension);
    }

    // Fungsi untuk menghancurkan (destroy) instance Plyr
    function destroyPlyrInstances() {
        if (plyrBefore) {
            plyrBefore.destroy();
            plyrBefore = null;
        }
        if (plyrAfter) {
            plyrAfter.destroy();
            plyrAfter = null;
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const detailModal = new bootstrap.Modal(document.getElementById('detailPasienModal'));
        const statusPemeriksaanModal = new bootstrap.Modal(document.getElementById('statusPemeriksaanModal'));
        const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
        const catatanModal = new bootstrap.Modal(document.getElementById('catatanModal'));
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        
        const formUpdateCatatan = document.getElementById('form-update-catatan');
        const formDeleteVideo = document.getElementById('form-delete-video');
        const formUploadVideo = document.getElementById('form-upload-video');

        const detailUrlTemplate = `{{ route('dokter.pasien.detail', ['id' => 'PASIEN_ID']) }}`;
        const updatePemeriksaanUrlTemplate = `{{ route('dokter.pasien.update-pemeriksaan', ['id' => 'PASIEN_ID']) }}`;
        const uploadVideoUrlTemplate = `{{ route('dokter.pasien.upload-videos', ['id' => 'PASIEN_ID']) }}`;
        const deleteVideoUrlTemplate = `{{ route('dokter.pasien.delete-video', ['id' => 'PASIEN_ID']) }}`;
        const getCatatanUrlTemplate = `{{ route('dokter.pasien.get-catatan', ['id' => 'PASIEN_ID']) }}`;
        const updateCatatanUrlTemplate = `{{ route('dokter.pasien.update-catatan', ['id' => 'PASIEN_ID']) }}`;
        const deleteCatatanUrlTemplate = `{{ route('dokter.pasien.delete-catatan', ['id' => 'PASIEN_ID']) }}`;
        
        let currentPasienId = null; 
        let videoTypeToDelete = null;

        const dokterLoginNama = document.getElementById('dokter-login-nama').value; 
        
        // Hide initial loadings
        document.getElementById('loading-today').style.display = 'none';
        document.getElementById('loading-upcoming').style.display = 'none';
        
        // --- LOGIC TAB & FILTER ---
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab') || 'today';

        function applyTabVisibility(key, isActive) {
            document.getElementById(`pasien-${key}`).style.display = isActive ? 'block' : 'none';
            document.getElementById(`btn-${key}`).classList.toggle('active', isActive);
            document.getElementById(`btn-${key}`).classList.toggle('btn-primary', isActive);
            document.getElementById(`btn-${key}`).classList.toggle('btn-secondary', !isActive);
            const filterForm = document.getElementById(`filter-form-${key}`);
            if (filterForm) {
                filterForm.style.display = isActive ? 'flex' : 'none';
            }
        }

        applyTabVisibility('today', activeTab === 'today');
        applyTabVisibility('upcoming', activeTab === 'upcoming');

        function updateTabs(activeKey) {
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('tab', activeKey);
            // Hapus filter yang tidak relevan (kecuali nama pasien dan status) saat pindah tab
            if (activeKey === 'today') {
                 currentParams.delete('date');
            }
            const queryString = currentParams.toString();
            const newUrl = `{{ route('dokter.data-pasien') }}?${queryString}`;
            
            showLoading(activeKey);
            window.location.href = newUrl;
        }

        document.getElementById('btn-today').addEventListener('click', () => updateTabs('today'));
        document.getElementById('btn-upcoming').addEventListener('click', () => updateTabs('upcoming'));

        document.querySelectorAll('.filter-group button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const form = this.closest('form');
                const activeKey = form.querySelector('input[name="tab"]').value;
                showLoading(activeKey);
                form.submit();
            });
        });
        // --- END LOGIC TAB & FILTER ---

        // Modal Detail Pasien (Tidak Berubah)
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const loading = document.getElementById('loading-spinner');
                const detailTable = document.querySelector('#detailPasienModal table');
                
                loading.style.display = 'block';
                detailTable.style.display = 'none';
                detailModal.show();

                fetch(detailUrlTemplate.replace('PASIEN_ID', pasienId))
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.json();
                    })
                    .then(data => {
                        const d = data.data;
                        const tglLahirFormatted = d.tgl_lahir ? new Date(d.tgl_lahir).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';
                        const tglKunjunganFormatted = d.tgl_kunjungan ? new Date(d.tgl_kunjungan).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';

                        document.getElementById('detail-antrian').textContent = d.nomor_antrian || '-';
                        document.getElementById('detail-nama').textContent = d.nama_pasien;
                        document.getElementById('detail-hp').textContent = d.nomor_hp;
                        document.getElementById('detail-tgl-jk').textContent = `${tglLahirFormatted} / ${d.jenis_kelamin}`;
                        document.getElementById('detail-pendamping').textContent = d.pendamping;
                        document.getElementById('detail-tgl-kunjungan').textContent = tglKunjunganFormatted;
                        document.getElementById('detail-waktu').textContent = d.waktu_kunjungan;
                        document.getElementById('detail-layanan').textContent = d.layanan; 
                        document.getElementById('detail-kategori').textContent = d.kategori_pendaftaran;
                        document.getElementById('detail-dokter').textContent = d.dokter_nama || 'Belum Ditentukan'; 
                        document.getElementById('detail-alamat').textContent = d.alamat;
                        document.getElementById('detail-keluhan').textContent = d.keluhan;
                        document.getElementById('detail-status-pemeriksaan').textContent = d.status_pemeriksaan;
                        document.getElementById('detail-status-berkas').textContent = d.status_berkas;

                        loading.style.display = 'none';
                        detailTable.style.display = 'table';
                    })
                    .catch(error => {
                        console.error('Error fetching detail:', error);
                        alert('Gagal mengambil detail pasien.');
                        detailModal.hide();
                    });
            });
        });

        // Modal Status Pemeriksaan (Tidak Berubah)
        document.querySelectorAll('.btn-status-pemeriksaan').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const loading = document.getElementById('status-loading-spinner');
                const content = document.getElementById('status-content');
                const footer = document.getElementById('status-modal-footer');
                
                loading.style.display = 'block';
                content.style.display = 'none';
                footer.style.display = 'none';
                statusPemeriksaanModal.show();
                
                document.getElementById('dokter-yg-login-nama').textContent = dokterLoginNama;
                
                fetch(detailUrlTemplate.replace('PASIEN_ID', pasienId))
                    .then(response => response.json())
                    .then(data => {
                        const d = data.data;
                        
                        document.getElementById('form-status-pemeriksaan').action = updatePemeriksaanUrlTemplate.replace('PASIEN_ID', pasienId);
                        
                        document.getElementById('current-dokter-text').textContent = d.dokter_nama || 'Belum Ditentukan';
                        document.getElementById('current-status-text').textContent = d.status_pemeriksaan;
                        document.getElementById('status_pemeriksaan_select').value = d.status_pemeriksaan;
                        
                        loading.style.display = 'none';
                        content.style.display = 'block';
                        footer.style.display = 'flex';
                    })
                    .catch(error => {
                        console.error('Error fetching status detail:', error);
                        loading.style.display = 'none';
                        content.style.display = 'block';
                        alert('Gagal memuat detail.');
                    });
            });
        });

        // Modal Video/Foto (Hanya fungsionalitas Plyr yang ditambahkan, logika tampilan file tetap sama)
        document.querySelectorAll('.btn-video').forEach(button => {
            button.addEventListener('click', function() {
                destroyPlyrInstances();
                
                currentPasienId = this.dataset.id;
                const pasienId = currentPasienId;
                const filePathBefore = this.dataset.videoBefore;
                const filePathAfter = this.dataset.videoAfter;

                const playerBefore = document.getElementById('video-before-player');
                const imageBefore = document.getElementById('image-before-player');
                const notFoundBefore = document.getElementById('file-before-not-found');
                const btnDeleteBefore = document.getElementById('btn-delete-before');
                
                const playerAfter = document.getElementById('video-after-player');
                const imageAfter = document.getElementById('image-after-player');
                const notFoundAfter = document.getElementById('file-after-not-found');
                const btnDeleteAfter = document.getElementById('btn-delete-after');
                
                // Reset input files & loading
                document.getElementById('video_before_file').value = '';
                document.getElementById('video_after_file').value = '';
                document.getElementById('upload-loading-before').style.display = 'none';
                document.getElementById('upload-loading-after').style.display = 'none';
                document.getElementById('btn-submit-video').disabled = false;

                formUploadVideo.action = uploadVideoUrlTemplate.replace('PASIEN_ID', pasienId);
                formDeleteVideo.action = deleteVideoUrlTemplate.replace('PASIEN_ID', pasienId); 

                // --- LOGIKA TAMPILAN FILE BEFORE ---
                if (filePathBefore) {
                    if (isImageFile(filePathBefore)) {
                        imageBefore.src = filePathBefore;
                        imageBefore.style.display = 'block';
                        playerBefore.style.display = 'none';
                        playerBefore.removeAttribute('src');
                    } else if (isVideoFile(filePathBefore)) {
                        playerBefore.src = filePathBefore;
                        playerBefore.style.display = 'block';
                        imageBefore.style.display = 'none';
                        imageBefore.removeAttribute('src');
                        plyrBefore = new Plyr(playerBefore);
                    } else {
                        playerBefore.removeAttribute('src');
                        imageBefore.removeAttribute('src');
                        playerBefore.style.display = 'none';
                        imageBefore.style.display = 'none';
                    }
                    btnDeleteBefore.style.display = 'block';
                    notFoundBefore.style.display = 'none';
                } else {
                    playerBefore.removeAttribute('src'); 
                    imageBefore.removeAttribute('src'); 
                    playerBefore.style.display = 'none';
                    imageBefore.style.display = 'none';
                    btnDeleteBefore.style.display = 'none';
                    notFoundBefore.style.display = 'block';
                }

                // --- LOGIKA TAMPILAN FILE AFTER ---
                if (filePathAfter) {
                    if (isImageFile(filePathAfter)) {
                        imageAfter.src = filePathAfter;
                        imageAfter.style.display = 'block';
                        playerAfter.style.display = 'none';
                        playerAfter.removeAttribute('src');
                    } else if (isVideoFile(filePathAfter)) {
                        playerAfter.src = filePathAfter;
                        playerAfter.style.display = 'block';
                        imageAfter.style.display = 'none';
                        imageAfter.removeAttribute('src');
                        plyrAfter = new Plyr(playerAfter);
                    } else {
                        playerAfter.removeAttribute('src');
                        imageAfter.removeAttribute('src');
                        playerAfter.style.display = 'none';
                        imageAfter.style.display = 'none';
                    }
                    btnDeleteAfter.style.display = 'block';
                    notFoundAfter.style.display = 'none';
                } else {
                    playerAfter.removeAttribute('src'); 
                    imageAfter.removeAttribute('src'); 
                    playerAfter.style.display = 'none';
                    imageAfter.style.display = 'none';
                    btnDeleteAfter.style.display = 'none';
                    notFoundAfter.style.display = 'block';
                }

                videoModal.show();
            });
        });

        // Hentikan pemutaran Plyr saat modal ditutup
        document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
            destroyPlyrInstances();
        });


        // Handler Upload (Tidak Berubah)
        formUploadVideo.addEventListener('submit', function(e) {
            const fileBefore = document.getElementById('video_before_file').files.length;
            const fileAfter = document.getElementById('video_after_file').files.length;

            if (fileBefore > 0 || fileAfter > 0) {
                if (fileBefore > 0) document.getElementById('upload-loading-before').style.display = 'flex';
                if (fileAfter > 0) document.getElementById('upload-loading-after').style.display = 'flex';
                document.getElementById('btn-submit-video').disabled = true;
            } else {
                e.preventDefault();
                alert('Pilih setidaknya satu file.');
            }
        });

        // Handle Delete (Tidak Berubah)
        document.querySelectorAll('.btn-delete-video').forEach(button => {
            button.addEventListener('click', function() {
                videoModal.hide(); 
                videoTypeToDelete = this.dataset.type; 
                const videoName = (videoTypeToDelete === 'video_before') ? 'File Sebelum' : 'File Sesudah';
                document.getElementById('video-name-confirm').textContent = videoName;
                confirmDeleteModal.show();
            });
        });
        
        document.getElementById('btn-confirm-delete').addEventListener('click', function() {
            if (videoTypeToDelete && currentPasienId) {
                document.getElementById('delete-video-type').value = videoTypeToDelete;
                formDeleteVideo.submit(); 
                confirmDeleteModal.hide(); 
            }
        });

        // Modal Catatan (Tidak Berubah)
        function displayCatatanAlert(isSuccess, message) {
            const successAlert = document.getElementById('catatan-alert-success');
            const errorAlert = document.getElementById('catatan-alert-error');
            
            successAlert.classList.add('d-none');
            errorAlert.classList.add('d-none');
            
            if (message) {
                if (isSuccess) {
                    successAlert.textContent = message;
                    successAlert.classList.remove('d-none');
                } else {
                    errorAlert.textContent = message;
                    errorAlert.classList.remove('d-none');
                }
            }
        }

        document.querySelectorAll('.btn-catatan').forEach(button => {
            button.addEventListener('click', function() {
                currentPasienId = this.dataset.id;
                const pasienId = currentPasienId;
                const loading = document.getElementById('catatan-loading');
                
                loading.style.display = 'block';
                displayCatatanAlert(false, '');
                
                document.getElementById('catatan-pasien-id').textContent = `ID: ${pasienId}`;
                formUpdateCatatan.action = updateCatatanUrlTemplate.replace('PASIEN_ID', pasienId);
                
                document.getElementById('catatan_pemeriksaan').value = '';
                document.getElementById('catatan_obat').value = '';

                fetch(getCatatanUrlTemplate.replace('PASIEN_ID', pasienId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('catatan_pemeriksaan').value = data.data.catatan_pemeriksaan || '';
                        document.getElementById('catatan_obat').value = data.data.catatan_obat || '';
                        loading.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        displayCatatanAlert(false, 'Gagal memuat catatan.');
                        loading.style.display = 'none';
                    });
                
                catatanModal.show();
            });
        });

        formUpdateCatatan.addEventListener('submit', function(e) {
            e.preventDefault();
            const loading = document.getElementById('catatan-loading');
            loading.style.display = 'block';
            displayCatatanAlert(false, '');

            fetch(formUpdateCatatan.action, {
                method: 'POST',
                body: new FormData(formUpdateCatatan),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                }
            })
            .then(response => response.json())
            .then(data => {
                loading.style.display = 'none';
                if (data.status === 'success') {
                    displayCatatanAlert(true, data.message);
                    document.getElementById('catatan_pemeriksaan').value = data.data.catatan_pemeriksaan || '';
                    document.getElementById('catatan_obat').value = data.data.catatan_obat || '';
                } else {
                    displayCatatanAlert(false, 'Gagal menyimpan.');
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                displayCatatanAlert(false, 'Terjadi kesalahan.');
                console.error('Error:', error);
            });
        });

        document.querySelectorAll('.btn-delete-catatan').forEach(button => {
            button.addEventListener('click', function() {
                const field = this.dataset.field;
                const fieldName = (field === 'catatan_pemeriksaan') ? 'Catatan Pemeriksaan' : 'Catatan Obat';
                
                if (confirm(`Yakin hapus ${fieldName}?`)) {
                    const pasienId = currentPasienId;
                    const loading = document.getElementById('catatan-loading');
                    loading.style.display = 'block';
                    displayCatatanAlert(false, '');

                    fetch(deleteCatatanUrlTemplate.replace('PASIEN_ID', pasienId), {
                        method: 'POST',
                        body: JSON.stringify({ field: field, _token: document.querySelector('input[name="_token"]').value }),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        loading.style.display = 'none';
                        if (data.status === 'success') {
                            displayCatatanAlert(true, data.message);
                            document.getElementById(field).value = '';
                        } else {
                            displayCatatanAlert(false, 'Gagal menghapus.');
                        }
                    })
                    .catch(error => {
                        loading.style.display = 'none';
                        displayCatatanAlert(false, 'Terjadi kesalahan.');
                        console.error('Error:', error);
                    });
                }
            });
        });
        
    });
</script>
@endsection
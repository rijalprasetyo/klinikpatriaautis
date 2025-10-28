@extends('layouts.dokter-sidebar')

@section('content')

{{-- Tambahkan Hidden Input untuk Nama Dokter yang Login (Digunakan di Modal Status) --}}
{{-- Asumsi Auth::user() adalah instance dari Model Dokter dan memiliki kolom nama_dokter --}}
<input type="hidden" id="dokter-login-nama" value="{{ Auth::user()->nama_dokter ?? 'Dokter ID: ' . Auth::id() }}">

{{-- Terapkan CSS baru di sini untuk menimpa gaya default Bootstrap dan memberikan tema biru-putih modern --}}
<style>
    /* Variabel Warna Baru */
    :root {
        --primary-blue: #007bff; /* Biru Primer untuk Aksi/Header */
        --secondary-blue: #0056b3; /* Biru Lebih Gelap */
        --text-dark: #343a40;
        --bg-light: #f8f9fa;
        --border-light: #dee2e6;
    }

    /* Tampilan Header & Kontainer */
    .container-fluid h2 {
        color: var(--secondary-blue);
        border-bottom: 2px solid var(--border-light);
        padding-bottom: 10px;
    }
    
    /* Tombol Toggle Hari Ini/Esok (Minimalis & Profesional) */
    .btn-group .btn {
        border-radius: 0.5rem;
        margin-right: 5px;
        transition: all 0.2s ease;
        font-weight: 500;
        border: 1px solid var(--primary-blue);
    }

    /* Tombol Aktif (Hari Ini/Esok) */
    .btn-group .btn.btn-primary.active {
        background-color: var(--primary-blue) !important;
        border-color: var(--primary-blue) !important;
        color: white !important;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.2);
    }
    
    /* Tombol Non-Aktif (Menggunakan warna putih/light-gray) */
    .btn-group .btn-secondary {
        background-color: var(--bg-light) !important;
        border-color: var(--border-light) !important;
        color: var(--text-dark) !important;
    }
    
    .btn-group .btn-secondary:hover {
        background-color: #e9ecef !important;
    }

    /* Tampilan Tabel */
    .table-responsive {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        overflow: hidden;
        margin-top: 20px;
        position: relative; /* Penting untuk loading overlay */
        min-height: 200px; /* Minimal tinggi agar loading terlihat */
    }
    
    .table-primary thead tr, .table-secondary thead tr {
        background-color: var(--primary-blue);
        color: white;
        font-weight: 600;
        border-bottom: none;
    }

    /* Tombol Aksi (Kecil, Ikonik, Minimalis) */
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
    
    /* Tombol Aksi hanya icon, hilangkan padding horizontal */
    .btn-action-icon {
        padding: 0.3rem !important; /* Kurangi padding untuk icon saja */
        width: 30px; /* Lebar tetap untuk konsistensi */
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
    .modal-header.bg-success { background-color: #28a745 !important; } /* Warna baru untuk modal catatan */

    /* Detail Table dalam Modal */
    .modal-body table th {
        width: 25%;
        font-weight: 600;
        color: var(--secondary-blue);
    }
    
    /* Styling untuk Video Player di Modal */
    .video-container {
        position: relative;
        text-align: center;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
    }

    /* Styling untuk Loading Overlay */
    .loading-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.8);
        z-index: 10;
        display: none; /* Default hidden */
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Responsif untuk Filter Group */
    @media (max-width: 991.98px) {
        .filter-group .col-md-3, .col-md-2 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    @media (max-width: 575.98px) {
        .filter-group .col-md-3, .col-md-2 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .btn-group {
            width: 100%;
            display: flex;
            flex-direction: column;
        }
        .btn-group .btn {
            margin-right: 0 !important;
            margin-bottom: 5px;
        }
    }
</style>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-users me-2"></i> Data Pasien (Jadwal Kunjungan)</h2>
    <p class="text-muted">Daftar pasien yang dijadwalkan untuk kunjungan hari ini dan jadwal mendatang.</p>

    <hr>

    <div class="row mb-4">
        <div class="col-12">
            
            {{-- Tombol Navigasi Toggle --}}
            <div class="btn-group" role="group" aria-label="Jadwal Pasien">
                <button type="button" class="btn btn-primary active" id="btn-today">
                    <i class="fa-solid fa-calendar-day me-1"></i> Hari Ini ({{ \Carbon\Carbon::parse($today)->isoFormat('D MMM') }})
                </button>
                <button type="button" class="btn btn-secondary" id="btn-upcoming">
                    <i class="fa-solid fa-calendar-alt me-1"></i> Jadwal Mendatang
                </button>
            </div>

            {{-- Alert Notifikasi --}}
            @if(session('success'))
                <div class="alert alert-success mt-3">{{ session('success') }}</div>
            @endif
            @if(session('warning'))
                <div class="alert alert-warning mt-3">{{ session('warning') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert alert-danger mt-3">
                    Mohon periksa unggahan video Anda.
                </div>
            @endif


        </div>
    </div>
    
    {{-- ======================================================= --}}
    {{-- DAFTAR PASIEN HARI INI & MENDATANG --}}
    {{-- ======================================================= --}}
    @php
        // Definisi data untuk loop
        $dataTabs = [
            'today' => ['label' => 'Jadwal Hari Ini', 'data' => $pasienHariIni, 'color' => 'primary'],
            'upcoming' => ['label' => 'Jadwal Mendatang', 'data' => $pasienMendatang, 'color' => 'secondary']
        ];
    @endphp

    @foreach ($dataTabs as $key => $tab)
        {{-- Menggunakan id form unik untuk setiap tab jika ada filter tanggal spesifik --}}
        <div id="pasien-{{ $key }}">
            <h4 class="mb-3 mt-4 text-{{ $tab['color'] }}">{{ $tab['label'] }}</h4>
            
            {{-- FILTER BERDASARKAN TAB --}}
            <form method="GET" action="{{ route('dokter.data-pasien') }}" id="filter-form-{{ $key }}" class="filter-group row mb-4 align-items-end">
                <input type="hidden" name="tab" value="{{ $key }}">
                
                {{-- FILTER TANGGAL (Hanya untuk Jadwal Mendatang) --}}
                @if ($key == 'upcoming')
                    <div class="col-md-4 col-sm-6 mb-2">
                        <label for="filter_date_{{ $key }}" class="form-label">Tanggal Kunjungan</label>
                        <select name="date" id="filter_date_{{ $key }}" class="form-select">
                            <option value="">-- Semua Tanggal Mendatang --</option>
                            @foreach ($availableDates as $date)
                                <option value="{{ $date }}" {{ $currentFilterDate == $date ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::parse($date)->isoFormat('dddd, D MMMM YYYY') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif
                
                {{-- FILTER STATUS PEMERIKSAAN (Untuk Hari Ini & Mendatang) --}}
                <div class="col-md-4 col-sm-6 mb-2">
                    <label for="filter_status_pemeriksaan_{{ $key }}" class="form-label">Status Pemeriksaan</label>
                    <select name="status_pemeriksaan" id="filter_status_pemeriksaan_{{ $key }}" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="Belum Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Belum Diperiksa' ? 'selected' : '' }}>Belum Diperiksa</option>
                        <option value="Sedang Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Sedang Diperiksa' ? 'selected' : '' }}>Sedang Diperiksa</option>
                        <option value="Selesai Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Selesai Diperiksa' ? 'selected' : '' }}>Selesai Diperiksa</option>
                    </select>
                </div>
                
                {{-- FILTER NAMA PASIEN (Untuk Hari Ini & Mendatang) --}}
                <div class="col-md-4 col-sm-6 mb-2">
                    <label for="filter_nama_pasien_{{ $key }}" class="form-label">Cari Nama Pasien</label>
                    <input type="text" name="nama_pasien" id="filter_nama_pasien_{{ $key }}" class="form-control" placeholder="Ketik nama pasien..." value="{{ $currentFilterNamaPasien }}">
                </div>
                
                <div class="col-md-2 col-sm-6 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary w-100 me-2" onclick="showLoading('{{ $key }}')"><i class="fa-solid fa-filter"></i> Filter</button>
                    <a href="{{ route('dokter.data-pasien', ['tab' => $key]) }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
            {{-- END OF FILTER --}}

            <div class="table-responsive">
                {{-- Loading Overlay --}}
                <div class="loading-overlay" id="loading-{{ $key }}">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                @if($tab['data']->isEmpty())
                    <div class="alert alert-info text-center">Tidak ada pasien yang ditemukan pada jadwal ini (atau sesuai filter).</div>
                @else
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-{{ $tab['color'] }}">
                            <tr>
                                @if ($key == 'upcoming')
                                    <th>Tanggal Kunjungan</th>
                                @endif
                                <th>Antrian</th>
                                <th>Nama Pasien</th>
                                <th>Kategori</th>
                                <th>Layanan</th>
                                <th>Waktu</th>
                                <th>Status Periksa</th>
                                <th style="width: 25%;">Aksi</th> {{-- Lebar aksi ditambah --}}
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tab['data'] as $pasien)
                                <tr>
                                    @if ($key == 'upcoming')
                                        <td>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</td>
                                    @endif
                                    <td class="fw-bold">{{ $pasien->nomor_antrian }}</td>
                                    <td>{{ $pasien->nama_pasien }}</td>
                                    <td>{{ $pasien->kategori_pendaftaran }}</td>
                                    <td>{{ $pasien->layanan->pelayanan ?? '-' }}</td>
                                    <td>{{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}</td>
                                    <td>
                                        {{-- LOGIKA WARNA BADGE STATUS PEMERIKSAAN --}}
                                        @php
                                            $badgeClass = 'bg-danger';
                                            if ($pasien->status_pemeriksaan == 'Sedang Diperiksa') {
                                                $badgeClass = 'bg-primary';
                                            } elseif ($pasien->status_pemeriksaan == 'Selesai Diperiksa') {
                                                $badgeClass = 'bg-success'; // Warna hijau untuk selesai
                                            }
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $pasien->status_pemeriksaan }}
                                        </span>
                                    </td>
                                    <td>
                                        {{-- Tombol Aksi Detail --}}
                                        <button class="btn btn-sm btn-info text-white me-1 btn-detail btn-action-icon" data-id="{{ $pasien->id }}" title="Detail Pasien">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </button>
                                        
                                        {{-- Tombol Ubah Status Pemeriksaan --}}
                                        <button class="btn btn-sm btn-warning me-1 btn-status-pemeriksaan btn-action-icon" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_pemeriksaan }}" title="Ubah Status Pemeriksaan">
                                            <i class="fa-solid fa-stethoscope"></i>
                                        </button>
                                        
                                        {{-- Tombol Unggah/Lihat Video --}}
                                        <button class="btn btn-sm btn-primary me-1 btn-video btn-action-icon" 
                                                data-id="{{ $pasien->id }}" 
                                                data-video-before="{{ $pasien->video_before ? asset('storage/' . $pasien->video_before) : '' }}" 
                                                data-video-after="{{ $pasien->video_after ? asset('storage/' . $pasien->video_after) : '' }}" 
                                                title="Unggah/Lihat Video">
                                            <i class="fa-solid fa-video"></i>
                                        </button>

                                        {{-- Tombol Tambah/Edit Catatan --}}
                                        <button class="btn btn-sm btn-success btn-catatan btn-action-icon" 
                                                data-id="{{ $pasien->id }}" 
                                                title="Tambah/Edit Catatan Pemeriksaan/Obat">
                                            <i class="fa-solid fa-notes-medical"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    @endforeach

</div>

{{-- ======================================================= --}}
{{-- MODALS --}}
{{-- ======================================================= --}}
{{-- Modal 1: Detail Pasien --}}
<div class="modal fade" id="detailPasienModal" tabindex="-1" aria-labelledby="detailPasienModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="detailPasienModalLabel">Detail Pasien - Antrian: <span id="detail-antrian"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-striped table-bordered">
                    <tbody>
                        <tr><th>Nama Pasien</th><td id="detail-nama"></td><th>Nomor HP</th><td id="detail-hp"></td></tr>
                        <tr><th>Tgl Lahir / JK</th><td id="detail-tgl-jk"></td><th>Pendamping</th><td id="detail-pendamping"></td></tr>
                        <tr><th>Tgl Kunjungan</th><td id="detail-tgl-kunjungan"></td><th>Waktu Kunjungan</th><td id="detail-waktu"></td></tr>
                        <tr><th>Layanan</th><td id="detail-layanan"></td><th>Kategori</th><td id="detail-kategori"></td></tr>
                        {{-- BARIS DOKTER PENANGGUNG JAWAB UNTUK MODAL DETAIL --}}
                        <tr><th>Dokter Penanggung Jawab</th><td colspan="3" id="detail-dokter"></td></tr>
                        {{-- END BARIS DOKTER PENANGGUNG JAWAB --}}
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

{{-- Modal 2: Ubah Status Pemeriksaan (SUDAH DIMODIFIKASI) --}}
<div class="modal fade" id="statusPemeriksaanModal" tabindex="-1" aria-labelledby="statusPemeriksaanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="statusPemeriksaanModalLabel">
                    <i class="fa-solid fa-stethoscope me-2"></i>Ubah Status Pemeriksaan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form id="form-status-pemeriksaan" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    {{-- SPINNER LOADING BARU --}}
                    <div id="status-loading-spinner" class="text-center mb-3" style="display: block;">
                        <div class="spinner-border text-warning" role="status"></div> Loading Data...
                    </div>

                    <div id="status-content" style="display: none;">
                        {{-- BARIS DOKTER PASIEN (dari data AJAX) --}}
                        <p class="mb-1">
                            Dokter Penanggung Jawab: 
                            <strong id="current-dokter-text">N/A</strong>
                        </p>
                        {{-- BARIS DOKTER YANG LOGIN (dari hidden input) --}}
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
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 3: Unggah dan Lihat Video (DENGAN ANIMASI UPLOAD) --}}
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="videoModalLabel">
                    <i class="fa-solid fa-video me-2"></i>Video Pemeriksaan Pasien
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="form-upload-video" method="POST" action="" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <p class="text-muted mb-4">Unggah video baru (MP4/MOV, Maks. 5MB) atau lihat video yang sudah ada. Pilih hanya video yang ingin diubah.</p>

                    <div class="row">
                        {{-- Unggah/Lihat Video Before --}}
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light fw-bold">Video Sebelum Pemeriksaan</div>
                                <div class="card-body">
                                    
                                    {{-- Video Player --}}
                                    <div class="video-container mb-3" id="player-before">
                                        {{-- ANIMASI LOADING UNTUK UPLOAD --}}
                                        <div class="loading-overlay" id="upload-loading-before">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Uploading...</span>
                                            </div>
                                            <p class="mt-2 text-primary">Mengunggah Video...</p>
                                        </div>
                                        {{-- AKHIR ANIMASI LOADING --}}

                                        <video id="video-before-player" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                        <div id="video-before-not-found" class="alert alert-info text-center" style="display: block;">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada video diunggah.
                                        </div>
                                    </div>
                                    
                                    {{-- Upload Input --}}
                                    <div class="mb-3">
                                        <label for="video_before_file" class="form-label">Unggah Video Baru</label>
                                        <input class="form-control @error('video_before') is-invalid @enderror" type="file" id="video_before_file" name="video_before" accept="video/mp4,video/quicktime">
                                        <small class="text-muted">Maks. 5MB (MP4/MOV)</small>
                                        @error('video_before')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Tombol Hapus Video Before (Hanya tampil jika video ada) --}}
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-2 btn-delete-video" data-type="video_before" data-bs-target="#confirmDeleteModal" data-bs-toggle="modal" id="btn-delete-before" style="display: none;">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus Video Sebelumnya
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Unggah/Lihat Video After --}}
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light fw-bold">Video Sesudah Pemeriksaan</div>
                                <div class="card-body">
                                    
                                    {{-- Video Player --}}
                                    <div class="video-container mb-3" id="player-after">
                                        {{-- ANIMASI LOADING UNTUK UPLOAD --}}
                                        <div class="loading-overlay" id="upload-loading-after">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Uploading...</span>
                                            </div>
                                            <p class="mt-2 text-primary">Mengunggah Video...</p>
                                        </div>
                                        {{-- AKHIR ANIMASI LOADING --}}

                                        <video id="video-after-player" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                        <div id="video-after-not-found" class="alert alert-info text-center" style="display: block;">
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada video diunggah.
                                        </div>
                                    </div>

                                    {{-- Upload Input --}}
                                    <div class="mb-3">
                                        <label for="video_after_file" class="form-label">Unggah Video Baru</label>
                                        <input class="form-control @error('video_after') is-invalid @enderror" type="file" id="video_after_file" name="video_after" accept="video/mp4,video/quicktime">
                                        <small class="text-muted">Maks. 5MB (MP4/MOV)</small>
                                        @error('video_after')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    
                                    {{-- Tombol Hapus Video After (Hanya tampil jika video ada) --}}
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-2 btn-delete-video" data-type="video_after" data-bs-target="#confirmDeleteModal" data-bs-toggle="modal" id="btn-delete-after" style="display: none;">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus Video Sesudahnya
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
            
            {{-- Form Tersembunyi untuk Penghapusan Video --}}
            <form id="form-delete-video" method="POST" action="" style="display: none;">
                @csrf
                <input type="hidden" name="video_type" id="delete-video-type">
            </form>
        </div>
    </div>
</div>

{{-- Modal 4: Catatan Pemeriksaan & Obat (Catatan Khusus) --}}
<div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="catatanModalLabel">
                    <i class="fa-solid fa-file-waveform me-2"></i>Catatan Pemeriksaan Pasien: <span id="catatan-pasien-id" class="fw-bold"></span>
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
                            <textarea name="catatan_pemeriksaan" id="catatan_pemeriksaan" class="form-control" rows="8" placeholder="Tulis hasil pemeriksaan dan diagnosis..."></textarea>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2 btn-delete-catatan" data-field="catatan_pemeriksaan" id="btn-delete-pemeriksaan"><i class="fa-solid fa-trash-can me-1"></i> Hapus Catatan</button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="catatan_obat" class="form-label fw-bold"><i class="fa-solid fa-capsules me-1"></i> Catatan Obat / Resep</label>
                            <textarea name="catatan_obat" id="catatan_obat" class="form-control" rows="8" placeholder="Tulis resep atau rekomendasi obat..."></textarea>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2 btn-delete-catatan" data-field="catatan_obat" id="btn-delete-obat"><i class="fa-solid fa-trash-can me-1"></i> Hapus Catatan</button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success" id="btn-save-catatan"><i class="fa-solid fa-save me-1"></i> Simpan Catatan</button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- MODAL KHUSUS: Konfirmasi Hapus Video --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel"><i class="fa-solid fa-triangle-exclamation me-2"></i> Konfirmasi Penghapusan</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>Apakah Anda yakin ingin menghapus **<span id="video-name-confirm" class="fw-bold"></span>**?</p>
                <p class="text-danger small">Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete"><i class="fa-solid fa-trash-can me-1"></i> Hapus Permanen</button>
            </div>
        </div>
    </div>
</div>


{{-- ======================================================= --}}
{{-- SCRIPT INTERAKSI MODAL & AJAX --}}
{{-- ======================================================= --}}
<script>
    // Fungsi untuk menampilkan loading spinner pada tab tertentu
    function showLoading(key) {
        document.getElementById(`loading-${key}`).style.display = 'flex';
        const table = document.querySelector(`#pasien-${key} .table-responsive table`);
        if (table) table.style.opacity = '0.5';
        const alert = document.querySelector(`#pasien-${key} .alert-info`);
        if (alert) alert.style.opacity = '0.5';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // --- INISIALISASI MODAL & URL ---
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

        // Ambil nama dokter yang login dari hidden input dan tampilkan di modal status
        const dokterLoginNama = document.getElementById('dokter-login-nama').value; 
        // Note: Ini diisi di modal status saat diklik, bukan di DOMContentLoaded
        
        // --- SETUP TABS & FILTERS ---
        document.getElementById('loading-today').style.display = 'none';
        document.getElementById('loading-upcoming').style.display = 'none';
        
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab') || 'today';

        if (activeTab === 'upcoming') {
            document.getElementById('pasien-today').style.display = 'none';
            document.getElementById('pasien-upcoming').style.display = 'block';
            document.getElementById('btn-today').classList.remove('active', 'btn-primary');
            document.getElementById('btn-today').classList.add('btn-secondary');
            document.getElementById('btn-upcoming').classList.add('active', 'btn-primary');
            document.getElementById('btn-upcoming').classList.remove('btn-secondary');
        } else {
            document.getElementById('pasien-today').style.display = 'block';
            document.getElementById('pasien-upcoming').style.display = 'none';
        }

        document.getElementById('filter-form-today').style.display = activeTab === 'today' ? 'flex' : 'none';
        document.getElementById('filter-form-upcoming').style.display = activeTab === 'upcoming' ? 'flex' : 'none';

        function updateTabs(activeKey) {
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.delete('tab');
            const queryString = currentParams.toString();
            const newUrl = `{{ route('dokter.data-pasien') }}?tab=${activeKey}` + (queryString ? `&${queryString}` : '');
            showLoading(activeKey);
            window.location.href = newUrl;
        }

        document.getElementById('btn-today').addEventListener('click', () => updateTabs('today'));
        document.getElementById('btn-upcoming').addEventListener('click', () => updateTabs('upcoming'));

        document.querySelectorAll('.filter-group select').forEach(select => {
            select.addEventListener('change', function() {
                showLoading(this.closest('.filter-group').querySelector('input[name="tab"]').value);
                this.closest('form').submit();
            });
        });
        
        document.querySelectorAll('.filter-group button[type="submit"]').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                showLoading(this.closest('.filter-group').querySelector('input[name="tab"]').value);
                this.closest('form').submit();
            });
        });
        // --- END SETUP TABS & FILTERS ---


        // =========================================================
        // 1. MODAL DETAIL PASIEN
        // =========================================================
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const loading = document.getElementById('loading-spinner');
                const detailTable = document.querySelector('#detailPasienModal table');
                
                loading.style.display = 'block';
                detailTable.style.display = 'none';
                detailModal.show();

                fetch(detailUrlTemplate.replace('PASIEN_ID', pasienId))
                    .then(response => response.json())
                    .then(data => {
                        const d = data.data;
                        document.getElementById('detail-antrian').textContent = d.nomor_antrian;
                        document.getElementById('detail-nama').textContent = d.nama_pasien;
                        document.getElementById('detail-hp').textContent = d.nomor_hp;
                        document.getElementById('detail-tgl-jk').textContent = `${d.tgl_lahir} / ${d.jenis_kelamin}`;
                        document.getElementById('detail-pendamping').textContent = d.pendamping;
                        document.getElementById('detail-tgl-kunjungan').textContent = d.tgl_kunjungan;
                        document.getElementById('detail-waktu').textContent = d.waktu_kunjungan;
                        document.getElementById('detail-layanan').textContent = d.layanan;
                        document.getElementById('detail-kategori').textContent = d.kategori_pendaftaran;
                        
                        // Isi nama dokter penanggung jawab
                        document.getElementById('detail-dokter').textContent = d.dokter_penanggung_jawab || 'Belum Ditentukan'; 

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

        // =========================================================
        // 2. MODAL UBAH STATUS PEMERIKSAAN
        // =========================================================
        document.querySelectorAll('.btn-status-pemeriksaan').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const loading = document.getElementById('status-loading-spinner');
                const content = document.getElementById('status-content');
                const footer = document.getElementById('status-modal-footer');
                
                // Tampilkan loading, sembunyikan konten dan footer
                loading.style.display = 'block';
                content.style.display = 'none';
                footer.style.display = 'none';
                statusPemeriksaanModal.show();
                
                // Isi nama dokter yang login (dari hidden input)
                document.getElementById('dokter-yg-login-nama').textContent = dokterLoginNama;
                
                // Ambil Detail Pasien (hanya untuk status dan dokter yang menangani saat ini)
                fetch(detailUrlTemplate.replace('PASIEN_ID', pasienId))
                    .then(response => response.json())
                    .then(data => {
                        const d = data.data;
                        
                        document.getElementById('form-status-pemeriksaan').action = updatePemeriksaanUrlTemplate.replace('PASIEN_ID', pasienId);
                        
                        // Tampilkan nama dokter penanggung jawab dari data pasien
                        document.getElementById('current-dokter-text').textContent = d.dokter_penanggung_jawab || 'Belum Ditentukan';
                        
                        // Tampilkan status saat ini
                        document.getElementById('current-status-text').textContent = d.status_pemeriksaan;
                        
                        // Set nilai pada dropdown sesuai status saat ini
                        document.getElementById('status_pemeriksaan_select').value = d.status_pemeriksaan;
                        
                        // Sembunyikan loading, tampilkan konten dan footer
                        loading.style.display = 'none';
                        content.style.display = 'block';
                        footer.style.display = 'flex';
                    })
                    .catch(error => {
                        console.error('Error fetching status detail:', error);
                        document.getElementById('current-status-text').textContent = 'Gagal memuat';
                        document.getElementById('current-dokter-text').textContent = 'Gagal memuat';
                        loading.style.display = 'none';
                        content.style.display = 'block';
                        alert('Gagal memuat detail pasien.');
                    });
            });
        });

        // =========================================================
        // 3. MODAL VIDEO & HAPUS (DENGAN ANIMASI UPLOAD)
        // =========================================================
        document.querySelectorAll('.btn-video').forEach(button => {
            button.addEventListener('click', function() {
                currentPasienId = this.dataset.id;
                const pasienId = currentPasienId;
                const videoBeforePath = this.dataset.videoBefore;
                const videoAfterPath = this.dataset.videoAfter;

                const playerBefore = document.getElementById('video-before-player');
                const notFoundBefore = document.getElementById('video-before-not-found');
                const btnDeleteBefore = document.getElementById('btn-delete-before');
                
                const playerAfter = document.getElementById('video-after-player');
                const notFoundAfter = document.getElementById('video-after-not-found');
                const btnDeleteAfter = document.getElementById('btn-delete-after');
                
                // Reset input file saat modal dibuka
                document.getElementById('video_before_file').value = '';
                document.getElementById('video_after_file').value = '';

                // Reset Upload loading
                document.getElementById('upload-loading-before').style.display = 'none';
                document.getElementById('upload-loading-after').style.display = 'none';
                document.getElementById('btn-submit-video').disabled = false;


                // 1. Setup Form Action URL
                formUploadVideo.action = uploadVideoUrlTemplate.replace('PASIEN_ID', pasienId);
                formDeleteVideo.action = deleteVideoUrlTemplate.replace('PASIEN_ID', pasienId); 

                // 2. Tampilkan/Sembunyikan Video Before dan Tombol Hapus
                if (videoBeforePath) {
                    playerBefore.src = videoBeforePath;
                    playerBefore.load(); 
                    playerBefore.style.display = 'block';
                    btnDeleteBefore.style.display = 'block';
                    notFoundBefore.style.display = 'none';
                } else {
                    playerBefore.removeAttribute('src'); 
                    playerBefore.style.display = 'none';
                    btnDeleteBefore.style.display = 'none';
                    notFoundBefore.style.display = 'block';
                }

                // 3. Tampilkan/Sembunyikan Video After dan Tombol Hapus
                if (videoAfterPath) {
                    playerAfter.src = videoAfterPath;
                    playerAfter.load(); 
                    playerAfter.style.display = 'block';
                    btnDeleteAfter.style.display = 'block';
                    notFoundAfter.style.display = 'none';
                } else {
                    playerAfter.removeAttribute('src'); 
                    playerAfter.style.display = 'none';
                    btnDeleteAfter.style.display = 'none';
                    notFoundAfter.style.display = 'block';
                }

                videoModal.show();
            });
        });

        // Handler untuk menampilkan loading saat form upload video disubmit
        formUploadVideo.addEventListener('submit', function(e) {
            
            const fileBefore = document.getElementById('video_before_file').files.length;
            const fileAfter = document.getElementById('video_after_file').files.length;

            if (fileBefore > 0 || fileAfter > 0) {
                // Tampilkan loading spinner yang relevan
                if (fileBefore > 0) {
                    document.getElementById('upload-loading-before').style.display = 'flex';
                }
                if (fileAfter > 0) {
                    document.getElementById('upload-loading-after').style.display = 'flex';
                }
                
                // Menonaktifkan tombol submit untuk mencegah double klik
                document.getElementById('btn-submit-video').disabled = true;
                
            } else {
                // Jika tidak ada file yang dipilih, batalkan submit dan beri peringatan
                e.preventDefault();
                alert('Pilih setidaknya satu file video sebelum menyimpan.');
            }
        });


        // Handle Tombol Hapus Video (Memicu Modal Konfirmasi)
        document.querySelectorAll('.btn-delete-video').forEach(button => {
            button.addEventListener('click', function() {
                videoModal.hide(); 
                videoTypeToDelete = this.dataset.type; 
                const videoName = (videoTypeToDelete === 'video_before') ? 'Video Sebelum Pemeriksaan' : 'Video Sesudah Pemeriksaan';
                document.getElementById('video-name-confirm').textContent = videoName;
                confirmDeleteModal.show();
            });
        });
        
        // Handle Konfirmasi Hapus (Dari Modal Konfirmasi)
        document.getElementById('btn-confirm-delete').addEventListener('click', function() {
            if (videoTypeToDelete && currentPasienId) {
                document.getElementById('delete-video-type').value = videoTypeToDelete;
                formDeleteVideo.submit(); 
                confirmDeleteModal.hide(); 
            }
        });

        // =========================================================
        // 4. MODAL CATATAN PEMERIKSAAN & OBAT
        // =========================================================
        
        // Helper untuk menampilkan notifikasi AJAX
        function displayCatatanAlert(isSuccess, message) {
            const successAlert = document.getElementById('catatan-alert-success');
            const errorAlert = document.getElementById('catatan-alert-error');
            
            // Sembunyikan semua alert terlebih dahulu
            successAlert.classList.add('d-none');
            errorAlert.classList.add('d-none');
            
            if (message) { // Hanya tampilkan jika ada message
                if (isSuccess) {
                    successAlert.textContent = message;
                    successAlert.classList.remove('d-none');
                } else {
                    errorAlert.textContent = message;
                    errorAlert.classList.remove('d-none');
                }
            }
        }

        // Memuat data catatan saat tombol diklik
        document.querySelectorAll('.btn-catatan').forEach(button => {
            button.addEventListener('click', function() {
                currentPasienId = this.dataset.id;
                const pasienId = currentPasienId;
                const loading = document.getElementById('catatan-loading');
                
                loading.style.display = 'block';
                displayCatatanAlert(false, ''); // Reset alert
                
                document.getElementById('catatan-pasien-id').textContent = `ID Pasien: ${pasienId}`;
                formUpdateCatatan.action = updateCatatanUrlTemplate.replace('PASIEN_ID', pasienId);
                
                // Reset form sebelum memuat data baru
                document.getElementById('catatan_pemeriksaan').value = '';
                document.getElementById('catatan_obat').value = '';

                // 1. Ambil data catatan via AJAX
                fetch(getCatatanUrlTemplate.replace('PASIEN_ID', pasienId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('catatan_pemeriksaan').value = data.data.catatan_pemeriksaan || '';
                        document.getElementById('catatan_obat').value = data.data.catatan_obat || '';
                        loading.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error fetching catatan:', error);
                        displayCatatanAlert(false, 'Gagal memuat data catatan.');
                        loading.style.display = 'none';
                    });
                
                catatanModal.show();
            });
        });

        // Menyimpan/Update Catatan (menggunakan AJAX)
        formUpdateCatatan.addEventListener('submit', function(e) {
            e.preventDefault();
            const loading = document.getElementById('catatan-loading');
            loading.style.display = 'block';
            displayCatatanAlert(false, ''); // Reset alert

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
                    // Update tampilan catatan di modal
                    document.getElementById('catatan_pemeriksaan').value = data.data.catatan_pemeriksaan || '';
                    document.getElementById('catatan_obat').value = data.data.catatan_obat || '';
                } else {
                    displayCatatanAlert(false, 'Gagal menyimpan data.');
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                displayCatatanAlert(false, 'Terjadi kesalahan server saat menyimpan.');
                console.error('Error saving catatan:', error);
            });
        });

        // Menghapus Catatan (menggunakan AJAX)
        document.querySelectorAll('.btn-delete-catatan').forEach(button => {
            button.addEventListener('click', function() {
                const field = this.dataset.field;
                const fieldName = (field === 'catatan_pemeriksaan') ? 'Catatan Pemeriksaan' : 'Catatan Obat';
                
                if (confirm(`Anda yakin ingin menghapus ${fieldName} ini?`)) {
                    const pasienId = currentPasienId;
                    const loading = document.getElementById('catatan-loading');
                    loading.style.display = 'block';
                    displayCatatanAlert(false, ''); // Reset alert

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
                            displayCatatanAlert(false, 'Gagal menghapus data.');
                        }
                    })
                    .catch(error => {
                        loading.style.display = 'none';
                        displayCatatanAlert(false, 'Terjadi kesalahan server saat menghapus.');
                        console.error('Error deleting catatan:', error);
                    });
                }
            });
        });
        
    });
</script>
@endsection
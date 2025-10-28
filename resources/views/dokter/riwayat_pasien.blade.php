@extends('layouts.dokter-sidebar')

@section('content')

{{-- Tambahkan Hidden Input untuk Nama Dokter yang Login (Digunakan di Modal Status) --}}
{{-- Asumsi Auth::user() adalah instance dari Model Dokter dan memiliki kolom nama_dokter --}}
<input type="hidden" id="dokter-login-nama" value="{{ Auth::user()->nama_dokter ?? 'Anda' }}">


<style>
/* Variabel Warna Baru */
:root {
    --primary-blue: #007bff;
    --secondary-blue: #0056b3;
    --text-dark: #343a40;
    --bg-light: #f8f9fa;
    --border-light: #dee2e6;
}

.container-fluid h2 {
    color: var(--secondary-blue);
    border-bottom: 2px solid var(--border-light);
    padding-bottom: 10px;
}

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

.btn-sm {
    padding: 0.3rem 0.6rem;
    border-radius: 0.3rem;
    font-size: 0.8rem;
}

.btn-info { background-color: var(--primary-blue) !important; border-color: var(--primary-blue) !important; }
.btn-info:hover { background-color: var(--secondary-blue) !important; }

.btn-warning { background-color: #ffc107 !important; border-color: #ffc107 !important; color: var(--text-dark) !important; }
.btn-warning:hover { background-color: #e0a800 !important; }

.btn-action-icon {
    padding: 0.3rem !important;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.modal-header.bg-info { background-color: var(--primary-blue) !important; }
.modal-header.bg-success { background-color: #28a745 !important; }
.modal-header.bg-danger { background-color: #dc3545 !important; }


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
}

/* Penyesuaian khusus untuk Modal Status */
#status-loading-spinner, #status-content, #status-modal-footer, 
#upload-loading-before, #upload-loading-after {
    display: none; /* Default hidden */
}


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
}
</style>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Pasien</h2>
    <p class="text-muted">Daftar riwayat semua pasien yang pernah berkunjung</p>

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
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            Mohon periksa unggahan video Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- FILTER --}}
    <form method="GET" action="{{ route('dokter.riwayat-pasien') }}" id="filter-form" class="filter-group row mb-4 align-items-end">
        
        {{-- Filter Tanggal Mulai --}}
        <div class="col-md-3 col-sm-6 mb-2">
            <label for="start_date" class="form-label">Tanggal Mulai</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ $filterStartDate }}">
        </div>
        
        {{-- Filter Tanggal Akhir --}}
        <div class="col-md-3 col-sm-6 mb-2">
            <label for="end_date" class="form-label">Tanggal Akhir</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ $filterEndDate }}">
        </div>
        
        {{-- Filter Status Pemeriksaan --}}
        <div class="col-md-2 col-sm-6 mb-2">
            <label for="status_pemeriksaan" class="form-label">Status</label>
            <select name="status_pemeriksaan" id="status_pemeriksaan" class="form-select">
                <option value="">-- Semua --</option>
                <option value="Belum Diperiksa" {{ $filterStatusPemeriksaan == 'Belum Diperiksa' ? 'selected' : '' }}>Belum Diperiksa</option>
                <option value="Sedang Diperiksa" {{ $filterStatusPemeriksaan == 'Sedang Diperiksa' ? 'selected' : '' }}>Sedang Diperiksa</option>
                <option value="Selesai Diperiksa" {{ $filterStatusPemeriksaan == 'Selesai Diperiksa' ? 'selected' : '' }}>Selesai Diperiksa</option>
            </select>
        </div>
        
        {{-- Filter Nama Pasien --}}
        <div class="col-md-2 col-sm-6 mb-2">
            <label for="nama_pasien" class="form-label">Cari Nama</label>
            <input type="text" name="nama_pasien" id="nama_pasien" class="form-control" placeholder="Nama pasien..." value="{{ $filterNamaPasien }}">
        </div>
        
        <div class="col-md-2 col-sm-6 mb-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill" onclick="showLoading()">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <a href="{{ route('dokter.riwayat-pasien') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
        </div>
    </form>

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
                Tidak ada data pasien yang ditemukan sesuai filter yang dipilih.
            </div>
        @else
            <table class="table table-striped table-hover align-middle">
                <thead class="table-primary">
                    <tr>
                        <th>No</th>
                        <th>Nama Pasien</th>
                        <th>Kategori</th>
                        <th>Layanan</th>
                        <th>Tgl Kunjungan</th>
                        <th>Dokter</th> {{-- Kolom Dokter Ditambahkan --}}
                        <th>Status</th>
                        <th style="width: 25%;">Aksi</th> {{-- Lebar aksi ditambah --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($dataPasien as $index => $pasien)
                        <tr>
                            <td class="fw-bold">{{ $index + 1 }}</td>
                            <td>{{ $pasien->nama_pasien }}</td>
                            <td>{{ $pasien->kategori_pendaftaran }}</td>
                            <td>{{ $pasien->layanan_id ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $pasien->dokter->nama_dokter ?? '-' }}</td> {{-- Menampilkan nama Dokter --}}
                            <td>
                                @php
                                    $badgeClass = 'bg-danger';
                                    if ($pasien->status_pemeriksaan == 'Sedang Diperiksa') {
                                        $badgeClass = 'bg-primary';
                                    } elseif ($pasien->status_pemeriksaan == 'Selesai Diperiksa') {
                                        $badgeClass = 'bg-success';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }}">
                                    {{ $pasien->status_pemeriksaan }}
                                </span>
                            </td>
                            <td>
                                {{-- Tombol Detail --}}
                                <button class="btn btn-sm btn-info text-white me-1 btn-detail btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        title="Detail Pasien">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </button>
                                
                                {{-- Tombol Ubah Status --}}
                                <button class="btn btn-sm btn-warning me-1 btn-status-pemeriksaan btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        data-current-status="{{ $pasien->status_pemeriksaan }}" 
                                        title="Ubah Status">
                                    <i class="fa-solid fa-stethoscope"></i>
                                </button>
                                
                                {{-- Tombol Video --}}
                                <button class="btn btn-sm btn-primary me-1 btn-video btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        data-video-before="{{ $pasien->video_before ? asset('storage/' . $pasien->video_before) : '' }}" 
                                        data-video-after="{{ $pasien->video_after ? asset('storage/' . $pasien->video_after) : '' }}" 
                                        title="Video">
                                    <i class="fa-solid fa-video"></i>
                                </button>

                                {{-- Tombol Catatan --}}
                                <button class="btn btn-sm btn-success me-1 btn-catatan btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        title="Catatan">
                                    <i class="fa-solid fa-notes-medical"></i>
                                </button>
                                
                                {{-- Tombol Lihat Feedback (Baru) --}}
                                <button class="btn btn-sm btn-secondary btn-feedback-view btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        data-feedback="{{ $pasien->feedback }}" 
                                        title="Lihat Feedback">
                                    <i class="fa-solid fa-comment-dots"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- SEMUA MODAL TETAP SAMA SEPERTI SEBELUMNYA --}}
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
                        {{-- BARIS DOKTER UNTUK MODAL DETAIL --}}
                        <tr><th>Dokter Penanggung Jawab</th><td colspan="3" id="detail-dokter-pj"></td></tr>
                        {{-- END BARIS DOKTER --}}
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

{{-- Modal 3: Video (DENGAN ANIMASI UPLOAD) --}}
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
                    <p class="text-muted mb-4">Unggah video baru (MP4/MOV, Maks. 5MB) atau lihat video yang sudah ada.</p>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light fw-bold">Video Sebelum</div>
                                <div class="card-body">
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
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada video
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="video_before_file" class="form-label">Unggah Video Baru</label>
                                        <input class="form-control" type="file" id="video_before_file" name="video_before" accept="video/mp4,video/quicktime">
                                        <small class="text-muted">Maks. 5MB</small>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-2 btn-delete-video" data-type="video_before" data-bs-target="#confirmDeleteModal" data-bs-toggle="modal" id="btn-delete-before" style="display: none;">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-header bg-light fw-bold">Video Sesudah</div>
                                <div class="card-body">
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
                                            <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada video
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="video_after_file" class="form-label">Unggah Video Baru</label>
                                        <input class="form-control" type="file" id="video_after_file" name="video_after" accept="video/mp4,video/quicktime">
                                        <small class="text-muted">Maks. 5MB</small>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-sm w-100 mt-2 btn-delete-video" data-type="video_after" data-bs-target="#confirmDeleteModal" data-bs-toggle="modal" id="btn-delete-after" style="display: none;">
                                        <i class="fa-solid fa-trash me-1"></i> Hapus
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary" id="btn-submit-video">Simpan</button>
                </div>
            </form>
            <form id="form-delete-video" method="POST" action="" style="display: none;">
                @csrf
                <input type="hidden" name="video_type" id="delete-video-type">
            </form>
        </div>
    </div>
</div>

{{-- Modal 4: Catatan --}}
<div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="catatanModalLabel">
                    <i class="fa-solid fa-file-waveform me-2"></i>Catatan Pemeriksaan: <span id="catatan-pasien-id" class="fw-bold"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-update-catatan" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <div id="catatan-alert-success" class="alert alert-success d-none"></div>
                    <div id="catatan-alert-error" class="alert alert-danger d-none"></div>
                    <div id="catatan-loading" class="text-center" style="display: none;">
                        <div class="spinner-border text-success" role="status"></div> Loading...
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="catatan_pemeriksaan" class="form-label fw-bold">
                                <i class="fa-solid fa-clipboard-check me-1"></i> Catatan Pemeriksaan
                            </label>
                            <textarea name="catatan_pemeriksaan" id="catatan_pemeriksaan" class="form-control" rows="8" placeholder="Tulis hasil pemeriksaan..."></textarea>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2 btn-delete-catatan" data-field="catatan_pemeriksaan">
                                <i class="fa-solid fa-trash-can me-1"></i> Hapus
                            </button>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="catatan_obat" class="form-label fw-bold">
                                <i class="fa-solid fa-capsules me-1"></i> Catatan Obat
                            </label>
                            <textarea name="catatan_obat" id="catatan_obat" class="form-control" rows="8" placeholder="Tulis resep obat..."></textarea>
                            <button type="button" class="btn btn-sm btn-outline-danger mt-2 btn-delete-catatan" data-field="catatan_obat">
                                <i class="fa-solid fa-trash-can me-1"></i> Hapus
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-save me-1"></i> Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 5: Lihat Feedback (Baru) --}}
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


{{-- Modal Konfirmasi Hapus Video --}}
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="confirmDeleteModalLabel">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> Konfirmasi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <p>Hapus <strong id="video-name-confirm"></strong>?</p>
                <p class="text-danger small">Tindakan tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="btn-confirm-delete">
                    <i class="fa-solid fa-trash-can me-1"></i> Hapus
                </button>
            </div>
        </div>
    </div>
</div>

{{-- JAVASCRIPT --}}
<script>
    function showLoading() {
        document.getElementById('loading').style.display = 'flex';
        const table = document.querySelector('.table-responsive table');
        if (table) table.style.opacity = '0.5';
    }

    document.addEventListener('DOMContentLoaded', function() {
        const detailModal = new bootstrap.Modal(document.getElementById('detailPasienModal'));
        const statusPemeriksaanModal = new bootstrap.Modal(document.getElementById('statusPemeriksaanModal'));
        const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
        const catatanModal = new bootstrap.Modal(document.getElementById('catatanModal'));
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal')); // Modal Baru
        
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
        
        // Ambil nama dokter yang login dari hidden input
        const dokterLoginNama = document.getElementById('dokter-login-nama').value;


        // Default hide/show modals components
        const statusLoading = document.getElementById('status-loading-spinner');
        const statusContent = document.getElementById('status-content');
        const statusFooter = document.getElementById('status-modal-footer');
        statusLoading.style.display = 'none';
        statusContent.style.display = 'none';
        statusFooter.style.display = 'none';

        // Hide main page loading on page load
        document.getElementById('loading').style.display = 'none';

        // ===== MODAL DETAIL PASIEN =====
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
                        document.getElementById('detail-dokter-pj').textContent = d.dokter_nama || 'Belum Ditentukan';

                        document.getElementById('detail-alamat').textContent = d.alamat;
                        document.getElementById('detail-keluhan').textContent = d.keluhan;
                        document.getElementById('detail-status-pemeriksaan').textContent = d.status_pemeriksaan;
                        document.getElementById('detail-status-berkas').textContent = d.status_berkas;

                        loading.style.display = 'none';
                        detailTable.style.display = 'table';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mengambil detail pasien.');
                        detailModal.hide();
                    });
            });
        });

        // ===== MODAL UBAH STATUS (DIMODIFIKASI) =====
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


        // ===== MODAL VIDEO (DENGAN ANIMASI UPLOAD) =====
        document.querySelectorAll('.btn-video').forEach(button => {
            button.addEventListener('click', function() {
                currentPasienId = this.dataset.id;
                const videoBeforePath = this.dataset.videoBefore;
                const videoAfterPath = this.dataset.videoAfter;

                const playerBefore = document.getElementById('video-before-player');
                const notFoundBefore = document.getElementById('video-before-not-found');
                const btnDeleteBefore = document.getElementById('btn-delete-before');
                
                const playerAfter = document.getElementById('video-after-player');
                const notFoundAfter = document.getElementById('video-after-not-found');
                const btnDeleteAfter = document.getElementById('btn-delete-after');
                
                // Reset input files
                document.getElementById('video_before_file').value = '';
                document.getElementById('video_after_file').value = '';

                // Reset Upload loading
                document.getElementById('upload-loading-before').style.display = 'none';
                document.getElementById('upload-loading-after').style.display = 'none';
                document.getElementById('btn-submit-video').disabled = false;


                document.getElementById('form-upload-video').action = uploadVideoUrlTemplate.replace('PASIEN_ID', currentPasienId);
                formDeleteVideo.action = deleteVideoUrlTemplate.replace('PASIEN_ID', currentPasienId);

                // Video Before
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

                // Video After
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

        // Handle delete video button
        document.querySelectorAll('.btn-delete-video').forEach(button => {
            button.addEventListener('click', function() {
                videoModal.hide();
                videoTypeToDelete = this.dataset.type;
                const videoName = (videoTypeToDelete === 'video_before') ? 'Video Sebelum' : 'Video Sesudah';
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

        // ===== MODAL FEEDBACK (Baru) =====
        document.querySelectorAll('.btn-feedback-view').forEach(button => {
            button.addEventListener('click', function() {
                const pasienName = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                const feedback = this.dataset.feedback;

                document.getElementById('feedback-pasien-nama').textContent = pasienName;
                document.getElementById('feedback-text-view').textContent = feedback || 'Pasien belum memberikan feedback.';
                
                feedbackModal.show();
            });
        });


        // ===== MODAL CATATAN =====
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
                const loading = document.getElementById('catatan-loading');
                
                loading.style.display = 'block';
                displayCatatanAlert(false, '');
                
                document.getElementById('catatan-pasien-id').textContent = `ID: ${currentPasienId}`;
                formUpdateCatatan.action = updateCatatanUrlTemplate.replace('PASIEN_ID', currentPasienId);
                
                document.getElementById('catatan_pemeriksaan').value = '';
                document.getElementById('catatan_obat').value = '';

                fetch(getCatatanUrlTemplate.replace('PASIEN_ID', currentPasienId))
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('catatan_pemeriksaan').value = data.data.catatan_pemeriksaan || '';
                        document.getElementById('catatan_obat').value = data.data.catatan_obat || '';
                        loading.style.display = 'none';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        displayCatatanAlert(false, 'Gagal memuat data catatan.');
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
                    displayCatatanAlert(false, 'Gagal menyimpan data.');
                }
            })
            .catch(error => {
                loading.style.display = 'none';
                displayCatatanAlert(false, 'Terjadi kesalahan server.');
                console.error('Error:', error);
            });
        });

        document.querySelectorAll('.btn-delete-catatan').forEach(button => {
            button.addEventListener('click', function() {
                const field = this.dataset.field;
                const fieldName = (field === 'catatan_pemeriksaan') ? 'Catatan Pemeriksaan' : 'Catatan Obat';
                
                if (confirm(`Hapus ${fieldName}?`)) {
                    const loading = document.getElementById('catatan-loading');
                    loading.style.display = 'block';
                    displayCatatanAlert(false, '');

                    fetch(deleteCatatanUrlTemplate.replace('PASIEN_ID', currentPasienId), {
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
                        displayCatatanAlert(false, 'Terjadi kesalahan server.');
                        console.error('Error:', error);
                    });
                }
            });
        });
    });
</script>

@endsection
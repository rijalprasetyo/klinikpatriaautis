@extends('layouts.admin-sidebar')

@section('content')

<style>
/* Variabel Warna Baru */
:root {
    --primary-blue: #007bff;
    --secondary-blue: #0056b3;
    --text-dark: #343a40;
    --bg-light: #f8f9fa;
    --border-light: #dee2e6;
    --status-wait: #ffc107; /* Kuning untuk Menunggu */
    --status-reject: #dc3545; /* Merah untuk Ditolak */
}

/* Penyesuaian Container untuk Memaksimalkan Lebar */
.container-fluid {
    padding-left: 10px !important;
    padding-right: 10px !important;
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

/* Warna untuk Aksi */
.btn-info { background-color: var(--primary-blue) !important; border-color: var(--primary-blue) !important; }
.btn-info:hover { background-color: var(--secondary-blue) !important; }

/* Warna untuk Filter */
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
.modal-header.bg-warning-custom { background-color: #ffc107 !important; color: var(--text-dark) !important;}


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

/* Status Berkas Custom Colors */
.badge.bg-berkas-merah { /* Belum Diverifikasi, Ditolak */
    background-color: var(--status-reject) !important;
}
.badge.bg-berkas-biru { /* Sudah Diverifikasi */
    background-color: var(--primary-blue) !important;
}
.badge.bg-berkas-kuning { /* Menunggu (Khusus Masyarakat Umum) */
    background-color: var(--status-wait) !important;
    color: var(--text-dark) !important;
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
    <h2 class="mb-4"><i class="fa-solid fa-clock-rotate-left me-2"></i> Riwayat Pasien (Admin View)</h2>
    <p class="text-muted">Daftar riwayat semua pasien yang pernah berkunjung.</p>

    <hr>

    {{-- Alert Notifikasi (dari session Laravel, tetap dipertahankan) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    {{-- FILTER --}}
    <form method="GET" action="{{ route('admin.riwayat-pasien') }}" id="filter-form" class="filter-group row mb-4 align-items-end">
        
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
        
        {{-- Filter Kategori BARU --}}
        <div class="col-md-2 col-sm-6 mb-2">
            <label for="kategori_pendaftaran" class="form-label">Kategori</label>
            <select name="kategori_pendaftaran" id="kategori_pendaftaran" class="form-select">
                <option value="">-- Semua --</option>
                {{-- $kategoriList dan $filterKategori harus di-pass dari controller --}}
                @if(isset($kategoriList))
                    @foreach($kategoriList as $kategori)
                        <option value="{{ $kategori }}" {{ (isset($filterKategori) && $filterKategori == $kategori) ? 'selected' : '' }}>
                            {{ $kategori }}
                        </option>
                    @endforeach
                @endif
            </select>
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
            <a href="{{ route('admin.riwayat-pasien') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
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
                        <th>Dokter</th>
                        <th>Status Periksa</th>
                        <th>Status Berkas</th>
                        <th style="width: 25%;">Aksi</th>
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
                            <td>{{ $pasien->dokter->nama_dokter ?? '-' }}</td>
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
                                @php
                                    $fileStatus = $pasien->status_berkas ?? 'Belum Diverifikasi';
                                    $fileBadgeClass = 'bg-berkas-merah';
                                    if ($fileStatus == 'Sudah Diverifikasi') {
                                        $fileBadgeClass = 'bg-berkas-biru';
                                    } elseif ($fileStatus == 'Menunggu') {
                                        $fileBadgeClass = 'bg-berkas-kuning';
                                    }
                                @endphp
                                <span class="badge {{ $fileBadgeClass }}">
                                    {{ $fileStatus }}
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    {{-- Tombol Detail (Lihat) --}}
                                    <button class="btn btn-sm btn-info text-white me-1 btn-detail btn-action-icon" 
                                            data-id="{{ $pasien->id }}" 
                                            title="Detail Pasien">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </button>
                                    
                                    {{-- Tombol Video/File (Lihat) --}}
                                    <button class="btn btn-sm btn-primary me-1 btn-video btn-action-icon" 
                                            data-id="{{ $pasien->id }}" 
                                            data-video-before="{{ $pasien->video_before ? asset('public/storage/' . $pasien->video_before) : '' }}" 
                                            data-video-after="{{ $pasien->video_after ? asset('public/storage/' . $pasien->video_after) : '' }}" 
                                            title="Lihat File">
                                        <i class="fa-solid fa-video"></i>
                                    </button>

                                    {{-- Tombol Catatan (Lihat) --}}
                                    <button class="btn btn-sm btn-success btn-catatan-view btn-action-icon" 
                                            data-id="{{ $pasien->id }}" 
                                            title="Lihat Catatan">
                                        <i class="fa-solid fa-notes-medical"></i>
                                    </button>
                                    
                                    {{-- Tombol Dokumen --}}
                                    <button class="btn btn-sm btn-secondary btn-dokumen btn-action-icon" 
                                            data-bukti="{{ asset('public/storage/' . $pasien->bukti_pembayaran) }}" 
                                            data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" 
                                            title="Lihat Dokumen">
                                        <i class="fa-solid fa-cloud-arrow-down"></i>
                                    </button>
                                    
                                    {{-- Tombol Lihat Feedback --}}
                                    <button class="btn btn-sm btn-secondary btn-feedback-view btn-action-icon" 
                                            data-id="{{ $pasien->id }}" 
                                            data-feedback="{{ $pasien->feedback }}" 
                                            title="Lihat Feedback">
                                        <i class="fa-solid fa-comment-dots"></i>
                                    </button>
                                    
                                    {{-- Tombol UBAH STATUS BERKAS --}}
                                    <button class="btn btn-sm btn-warning btn-status-berkas btn-action-icon" 
                                            data-id="{{ $pasien->id }}" 
                                            data-kategori="{{ $pasien->kategori_pendaftaran }}"
                                            data-status="{{ $pasien->status_berkas ?? 'Belum Diverifikasi' }}" 
                                            title="Ubah Status Berkas">
                                        <i class="fa-solid fa-file-shield"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

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
                        <tr><th>Dokter Penanggung Jawab</th><td colspan="3" id="detail-dokter-pj"></td></tr>
                        <tr><th>Alamat</th><td colspan="3" id="detail-alamat"></td></tr>
                        <tr><th>Keluhan</th><td colspan="3" id="detail-keluhan"></td></tr>
                        <tr><th>Status Pemeriksaan</th><td id="detail-status-pemeriksaan"></td><th>Status Berkas</th><td id="detail-status-berkas"></td></tr>
                    </tbody>
                </table>
                <div id="loading-spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-info" role="status"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 2: Video/File (Lihat Saja) --}}
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="videoModalLabel">
                    <i class="fa-solid fa-video me-2"></i>File Pemeriksaan Pasien
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <p class="text-muted mb-4">File sebelum dan sesudah pemeriksaan (Video/Foto).</p>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light fw-bold">File Sebelum Pemeriksaan</div>
                            <div class="card-body">
                                <div class="video-container mb-3" id="player-before">
                                    
                                    {{-- ELEMENT BARU UNTUK FOTO --}}
                                    <img id="image-before-player" style="width: 100%; max-height: 300px; display: none;" class="rounded" alt="Foto Sebelum">
                                    
                                    {{-- ELEMENT VIDEO --}}
                                    <video id="video-before-player" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                    
                                    <div id="file-before-not-found" class="alert alert-info text-center" style="display: block;">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada file.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light fw-bold">File Sesudah Pemeriksaan</div>
                            <div class="card-body">
                                <div class="video-container mb-3" id="player-after">
                                    
                                    {{-- ELEMENT BARU UNTUK FOTO --}}
                                    <img id="image-after-player" style="width: 100%; max-height: 300px; display: none;" class="rounded" alt="Foto Sesudah">

                                    {{-- ELEMENT VIDEO --}}
                                    <video id="video-after-player" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                    
                                    <div id="file-after-not-found" class="alert alert-info text-center" style="display: block;">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada file.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 3: Lihat Dokumen --}}
<div class="modal fade" id="dokumenModal" tabindex="-1" aria-labelledby="dokumenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="dokumenModalLabel"><i class="fa-solid fa-folder-open me-2"></i>Dokumen Pasien</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6>Bukti Pembayaran</h6>
                        <iframe id="bukti-pembayaran-frame" style="width: 100%; height: 500px; border: 1px solid var(--border-light);"></iframe>
                    </div>
                    <div class="col-md-6 mb-3">
                        <h6>Surat Keterangan Tidak Mampu (SKTM)</h6>
                        <div id="sktm-container">
                            <iframe id="sktm-frame" style="width: 100%; height: 500px; border: 1px solid var(--border-light);"></iframe>
                            <p id="sktm-not-found" class="alert alert-warning text-center mt-2" style="display: none;">Dokumen SKTM tidak diunggah.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal 4: Catatan (Lihat Saja) --}}
<div class="modal fade" id="catatanModal" tabindex="-1" aria-labelledby="catatanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="catatanModalLabel">
                    <i class="fa-solid fa-file-waveform me-2"></i>Catatan Pemeriksaan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="catatan-loading-view" class="text-center" style="display: block;">
                    <div class="spinner-border text-success" role="status"></div> Loading...
                </div>
                <div class="row" id="catatan-content-view" style="display: none;">
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><i class="fa-solid fa-clipboard-check me-1"></i> Catatan Pemeriksaan</label>
                        <p id="view_catatan_pemeriksaan" class="border p-2 rounded bg-light">Tidak ada catatan.</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold"><i class="fa-solid fa-capsules me-1"></i> Catatan Obat</label>
                        <p id="view_catatan_obat" class="border p-2 rounded bg-light">Tidak ada catatan.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 5: Lihat Feedback --}}
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
                    <p id="feedback-text-view" class="mb-0 text-dark">Pasien belum memberikan feedback.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 6: UBAH STATUS BERKAS (BARU) --}}
<div class="modal fade" id="statusBerkasModal" tabindex="-1" aria-labelledby="statusBerkasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <form id="form-update-status-berkas" method="POST">
                @csrf
                {{-- INPUT METHOD SPOOFING UNTUK LARAVEL (PUT) --}}
                <input type="hidden" name="_method" id="method-spoofing" value=""> 
                
                <div class="modal-header bg-warning-custom">
                    <h5 class="modal-title" id="statusBerkasModalLabel">
                        <i class="fa-solid fa-file-shield me-2"></i> Ubah Status Berkas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted">Pasien: <strong id="status-pasien-nama"></strong> | Kategori: <strong id="status-pasien-kategori"></strong></p>
                    <div class="mb-3">
                        <label for="status_berkas_select" class="form-label fw-bold">Pilih Status Baru</label>
                        <select class="form-select" id="status_berkas_select" name="status_berkas" required>
                            {{-- Opsi akan diisi oleh JavaScript --}}
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark" id="btn-submit-status-berkas">
                        <span id="status-loading-spinner-modal" class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true" style="display: none;"></span>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
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

{{-- Container untuk Toast (Pop-up notifikasi non-blokir) --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast align-items-center text-white border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="toast-message">
                {{-- Pesan akan dimasukkan di sini oleh JavaScript --}}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

{{-- JAVASCRIPT BARU DENGAN TOAST --}}
<script>
    // Fungsi untuk menampilkan Toast Notifikasi
    function showToast(message, type = 'success') {
        const toastElement = document.getElementById('liveToast');
        const toastBody = document.getElementById('toast-message');
        
        // Reset class
        toastElement.className = 'toast align-items-center text-white border-0';
        
        // Set warna dan pesan
        let backgroundColor = '';
        if (type === 'success') {
            backgroundColor = 'bg-success';
        } else if (type === 'error') {
            backgroundColor = 'bg-danger';
        } else {
            backgroundColor = 'bg-info';
        }

        toastElement.classList.add(backgroundColor);
        toastBody.textContent = message;

        const toast = new bootstrap.Toast(toastElement);
        toast.show();
    }


    function showLoading() {
        document.getElementById('loading').style.display = 'flex';
        const table = document.querySelector('.table-responsive table');
        if (table) table.style.opacity = '0.5';
    }

    // Helper functions untuk identifikasi tipe file
    function isImageFile(path) {
        if (!path) return false;
        const extension = path.split('.').pop().toLowerCase();
        // Mendukung JPEG, PNG, GIF, WebP, dan format iPhone (HEIC, HEIF)
        return ['jpg', 'jpeg', 'png', 'gif', 'webp', 'heic', 'heif'].includes(extension);
    }

    function isVideoFile(path) {
        if (!path) return false;
        const extension = path.split('.').pop().toLowerCase();
        // Mendukung MP4 dan format video iPhone (MOV)
        return ['mp4', 'mov', 'flv', 'avi', 'wmv'].includes(extension);
    }


    document.addEventListener('DOMContentLoaded', function() {
        const detailModal = new bootstrap.Modal(document.getElementById('detailPasienModal'));
        const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
        const catatanModal = new bootstrap.Modal(document.getElementById('catatanModal'));
        const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal')); 
        const dokumenModal = new bootstrap.Modal(document.getElementById('dokumenModal'));
        const statusBerkasModal = new bootstrap.Modal(document.getElementById('statusBerkasModal'));
        
        // --- URL Endpoints ---
        const detailUrlTemplate = `{{ route('admin.pasien.detail', ['id' => 'PASIEN_ID']) }}`;
        const getCatatanUrlTemplate = `{{ route('admin.pasien.get-catatan', ['id' => 'PASIEN_ID']) }}`;
        
        // Route POST: admin.pasien.update-berkas -> /admin/pasien/{id}/update-berkas
        const updateNonMasyarakatUmumUrl = (id) => `{{ route('admin.pasien.update-berkas', ['id' => 'PASIEN_ID']) }}`.replace('PASIEN_ID', id);
        
        // Route PUT: admin.berkas.update-masyarakat-umum -> /admin/berkas-umum/{id}/update
        const updateMasyarakatUmumUrl = (id) => `{{ route('admin.berkas.update-masyarakat-umum', ['id' => 'PASIEN_ID']) }}`.replace('PASIEN_ID', id);
        
        let currentPasienId = null;
        
        document.getElementById('loading').style.display = 'none';

        // ===== MODAL DETAIL PASIEN (VIEW ONLY) =====
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
                        showToast('Gagal mengambil detail pasien.', 'error'); // Menggunakan Toast
                        detailModal.hide();
                    });
            });
        });


        // ===== MODAL VIDEO/FILE (VIEW ONLY) - DIUBAH UNTUK DUKUNGAN FOTO HEIC/MOV =====
        document.querySelectorAll('.btn-video').forEach(button => {
            button.addEventListener('click', function() {
                const fileBeforePath = this.dataset.videoBefore;
                const fileAfterPath = this.dataset.videoAfter;

                const videoBefore = document.getElementById('video-before-player');
                const imageBefore = document.getElementById('image-before-player');
                const notFoundBefore = document.getElementById('file-before-not-found');
                
                const videoAfter = document.getElementById('video-after-player');
                const imageAfter = document.getElementById('image-after-player');
                const notFoundAfter = document.getElementById('file-after-not-found');

                // --- Logic File Before ---
                if (fileBeforePath) {
                    if (isImageFile(fileBeforePath)) {
                        // Tampilkan Foto
                        imageBefore.src = fileBeforePath;
                        imageBefore.style.display = 'block';
                        videoBefore.style.display = 'none';
                        videoBefore.removeAttribute('src');
                    } else if (isVideoFile(fileBeforePath)) {
                        // Tampilkan Video
                        videoBefore.src = fileBeforePath;
                        videoBefore.load();
                        videoBefore.style.display = 'block';
                        imageBefore.style.display = 'none';
                        imageBefore.removeAttribute('src');
                    } else {
                        // Tipe tidak didukung
                        videoBefore.removeAttribute('src');
                        imageBefore.removeAttribute('src');
                        videoBefore.style.display = 'none';
                        imageBefore.style.display = 'none';
                    }
                    notFoundBefore.style.display = 'none';
                } else {
                    // Tidak ada file
                    videoBefore.removeAttribute('src');
                    imageBefore.removeAttribute('src');
                    videoBefore.style.display = 'none';
                    imageBefore.style.display = 'none';
                    notFoundBefore.style.display = 'block';
                }

                // --- Logic File After ---
                if (fileAfterPath) {
                    if (isImageFile(fileAfterPath)) {
                        // Tampilkan Foto
                        imageAfter.src = fileAfterPath;
                        imageAfter.style.display = 'block';
                        videoAfter.style.display = 'none';
                        videoAfter.removeAttribute('src');
                    } else if (isVideoFile(fileAfterPath)) {
                        // Tampilkan Video
                        videoAfter.src = fileAfterPath;
                        videoAfter.load();
                        videoAfter.style.display = 'block';
                        imageAfter.style.display = 'none';
                        imageAfter.removeAttribute('src');
                    } else {
                        // Tipe tidak didukung
                        videoAfter.removeAttribute('src');
                        imageAfter.removeAttribute('src');
                        videoAfter.style.display = 'none';
                        imageAfter.style.display = 'none';
                    }
                    notFoundAfter.style.display = 'none';
                } else {
                    // Tidak ada file
                    videoAfter.removeAttribute('src');
                    imageAfter.removeAttribute('src');
                    videoAfter.style.display = 'none';
                    imageAfter.style.display = 'none';
                    notFoundAfter.style.display = 'block';
                }

                videoModal.show();
            });
        });

        // Menghentikan video saat modal ditutup
        videoModal._element.addEventListener('hidden.bs.modal', function () {
            const videoBefore = document.getElementById('video-before-player');
            const videoAfter = document.getElementById('video-after-player');
            
            if (videoBefore) videoBefore.pause();
            if (videoAfter) videoAfter.pause();
        });


        // ===== MODAL DOKUMEN (VIEW ONLY) =====
        document.querySelectorAll('.btn-dokumen').forEach(button => {
            button.addEventListener('click', function() {
                const buktiPath = this.dataset.bukti;
                const sktmPath = this.dataset.sktm;
                
                document.getElementById('bukti-pembayaran-frame').src = buktiPath;

                const sktmFrame = document.getElementById('sktm-frame');
                const sktmNotFound = document.getElementById('sktm-not-found');
                
                if (sktmPath) {
                    sktmFrame.src = sktmPath;
                    sktmFrame.style.display = 'block';
                    sktmNotFound.style.display = 'none';
                } else {
                    sktmFrame.src = '';
                    sktmFrame.style.display = 'none';
                    sktmNotFound.style.display = 'block';
                }

                dokumenModal.show();
            });
        });
    

        // ===== MODAL CATATAN (VIEW ONLY) =====
        document.querySelectorAll('.btn-catatan-view').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const loading = document.getElementById('catatan-loading-view');
                const content = document.getElementById('catatan-content-view');
                
                loading.style.display = 'block';
                content.style.display = 'none';
                
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
                        console.error('Error fetching catatan:', error);
                        document.getElementById('view_catatan_pemeriksaan').textContent = 'Gagal memuat.';
                        document.getElementById('view_catatan_obat').textContent = 'Gagal memuat.';
                        loading.style.display = 'none';
                        content.style.display = 'flex';
                    });
                
                catatanModal.show();
            });
        });

        // ===== MODAL FEEDBACK (VIEW ONLY) =====
        document.querySelectorAll('.btn-feedback-view').forEach(button => {
            button.addEventListener('click', function() {
                const pasienName = this.closest('tr').querySelector('td:nth-child(2)').textContent;
                const feedback = this.dataset.feedback;

                document.getElementById('feedback-pasien-nama').textContent = pasienName;
                document.getElementById('feedback-text-view').textContent = feedback || 'Pasien belum memberikan feedback.';
                
                feedbackModal.show();
            });
        });


        // ===== MODAL UBAH STATUS BERKAS (BARU) =====
        document.querySelectorAll('.btn-status-berkas').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const kategori = this.dataset.kategori;
                const currentStatus = this.dataset.status;
                const pasienName = this.closest('tr').querySelector('td:nth-child(2)').textContent;

                const selectElement = document.getElementById('status_berkas_select');
                const form = document.getElementById('form-update-status-berkas');
                const methodSpoofingInput = document.getElementById('method-spoofing');
                
                // Reset opsi dan method
                selectElement.innerHTML = ''; 
                methodSpoofingInput.value = '';

                document.getElementById('status-pasien-nama').textContent = pasienName;
                document.getElementById('status-pasien-kategori').textContent = kategori;
                
                let options = [];

                if (kategori.toLowerCase().includes('masyarakat umum')) {
                    options = ['Menunggu', 'Sudah Diverifikasi', 'Ditolak'];
                    form.action = updateMasyarakatUmumUrl(pasienId);
                    methodSpoofingInput.value = 'PUT'; // Set method untuk Laravel spoofing
                } else {
                    options = ['Belum Diverifikasi', 'Sudah Diverifikasi'];
                    form.action = updateNonMasyarakatUmumUrl(pasienId);
                    // methodSpoofingInput tetap kosong, karena default method adalah POST
                }

                options.forEach(option => {
                    const newOption = document.createElement('option');
                    newOption.value = option;
                    newOption.textContent = option;
                    if (option === currentStatus) {
                        newOption.selected = true;
                    }
                    selectElement.appendChild(newOption);
                });
                
                // Pastikan status saat ini masuk ke opsi jika belum ada
                if (!options.includes(currentStatus)) {
                    const currentOption = document.createElement('option');
                    currentOption.value = currentStatus;
                    currentOption.textContent = currentStatus;
                    currentOption.selected = true;
                    selectElement.prepend(currentOption);
                }

                statusBerkasModal.show();
            });
        });

        // Handler pengiriman form AJAX untuk Update Status Berkas
        document.getElementById('form-update-status-berkas').addEventListener('submit', function(e) {
            e.preventDefault();

            const submitBtn = document.getElementById('btn-submit-status-berkas');
            const loadingSpinner = document.getElementById('status-loading-spinner-modal');
            
            submitBtn.disabled = true;
            loadingSpinner.style.display = 'inline-block';

            const formData = new FormData(this);
            const actionUrl = this.action;
            const methodSpoofing = document.getElementById('method-spoofing').value;
            
            if (methodSpoofing === 'PUT' && !formData.has('_method')) {
                formData.append('_method', 'PUT');
            }

            fetch(actionUrl, {
                method: 'POST', // Selalu POST di frontend saat menggunakan method spoofing Laravel
                headers: {
                    'X-CSRF-TOKEN': formData.get('_token'), 
                    'Accept': 'application/json',
                },
                body: formData 
            })
            .then(response => {
                loadingSpinner.style.display = 'none';
                submitBtn.disabled = false;
                if (!response.ok) {
                    return response.json().then(err => { 
                        if (response.status === 422 && err.errors) {
                            throw new Error(Object.values(err.errors).flat().join(', '));
                        }
                        const errorMessage = err.message || 'Terjadi kesalahan saat memproses permintaan.';
                        throw new Error(errorMessage); 
                    });
                }
                return response.json();
            })
            .then(data => {
                // POP UP SUKSES MENGGUNAKAN TOAST
                showToast(data.message, 'success');
                statusBerkasModal.hide();
                // Muat ulang halaman untuk update tabel
                setTimeout(() => {
                    window.location.reload();
                }, 1000); 
            })
            .catch(error => {
                console.error('Error:', error);
                // POP UP GAGAL MENGGUNAKAN TOAST
                showToast('Gagal menyimpan status berkas. Pesan: ' + error.message, 'error');
                loadingSpinner.style.display = 'none';
                submitBtn.disabled = false;
            });
        });
    });
</script>

@endsection
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
}

/* Penyesuaian Container untuk Memaksimalkan Lebar */
/* Menghilangkan padding horizontal di container-fluid */
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

/* Status Berkas Custom Colors */
.badge.bg-berkas-merah { /* Belum Diverifikasi */
    background-color: #dc3545 !important;
}
.badge.bg-berkas-biru { /* Sudah Diverifikasi */
    background-color: #007bff !important;
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

    {{-- Alert Notifikasi (hanya untuk tampilan) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
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
                        <th>Status Berkas</th> {{-- KOLOM BARU --}}
                        <th style="width: 20%;">Aksi</th> {{-- Dikecilkan sedikit karena ada tambahan kolom --}}
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
                                    $fileBadgeClass = ($fileStatus == 'Sudah Diverifikasi') ? 'bg-berkas-biru' : 'bg-berkas-merah';
                                @endphp
                                <span class="badge {{ $fileBadgeClass }}">
                                    {{ $fileStatus }}
                                </span>
                            </td> {{-- DATA BARU --}}
                            <td>
                                {{-- Tombol Detail (Lihat) --}}
                                <button class="btn btn-sm btn-info text-white me-1 btn-detail btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        title="Detail Pasien">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </button>
                                
                                {{-- Tombol Video (Lihat) --}}
                                <button class="btn btn-sm btn-primary me-1 btn-video btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        data-video-before="{{ $pasien->video_before ? asset('public/storage/' . $pasien->video_before) : '' }}" 
                                        data-video-after="{{ $pasien->video_after ? asset('public/storage/' . $pasien->video_after) : '' }}" 
                                        title="Lihat Video">
                                    <i class="fa-solid fa-video"></i>
                                </button>

                                {{-- Tombol Catatan (Lihat) --}}
                                <button class="btn btn-sm btn-success btn-catatan-view btn-action-icon" 
                                        data-id="{{ $pasien->id }}" 
                                        title="Lihat Catatan">
                                    <i class="fa-solid fa-notes-medical"></i>
                                </button>
                                
                                <button class="btn btn-sm btn-secondary btn-dokumen btn-action-icon" data-bukti="{{ asset('public/storage/' . $pasien->bukti_pembayaran) }}" data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" title="Lihat Dokumen">
                                    <i class="fa-solid fa-cloud-arrow-down"></i>
                                </button>
                                
                                {{-- Tombol Lihat Feedback --}}
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
                        <tr><th>Dokter Penanggung Jawab</th><td colspan="3" id="detail-dokter-pj"></td></tr> {{-- Dokter Penanggung Jawab --}}
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

{{-- Modal 2: Video (Lihat Saja) --}}
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="videoModalLabel">
                    <i class="fa-solid fa-video me-2"></i>Video Pemeriksaan Pasien
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <p class="text-muted mb-4">Video sebelum dan sesudah pemeriksaan.</p>
                <div class="row">
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light fw-bold">Video Sebelum</div>
                            <div class="card-body">
                                <div class="video-container mb-3" id="player-before">
                                    <video id="video-before-player" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                    <div id="video-before-not-found" class="alert alert-info text-center" style="display: block;">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada video
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-4">
                        <div class="card shadow-sm h-100">
                            <div class="card-header bg-light fw-bold">Video Sesudah</div>
                            <div class="card-body">
                                <div class="video-container mb-3" id="player-after">
                                    <video id="video-after-player" controls style="width: 100%; max-height: 300px; display: none;" class="rounded"></video>
                                    <div id="video-after-not-found" class="alert alert-info text-center" style="display: block;">
                                        <i class="fa-solid fa-triangle-exclamation me-1"></i> Belum ada video
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

{{-- Modal 3: Catatan (Lihat Saja) --}}
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

{{-- Modal 4: Lihat Feedback (Baru) --}}
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
        const videoModal = new bootstrap.Modal(document.getElementById('videoModal'));
        const catatanModal = new bootstrap.Modal(document.getElementById('catatanModal'));
        const feedbackModal = new bootstrap.Modal(document.getElementById('feedbackModal')); 
        
        // --- URL Endpoints ---
        // Gunakan rute admin
        const detailUrlTemplate = `{{ route('admin.pasien.detail', ['id' => 'PASIEN_ID']) }}`;
        const getCatatanUrlTemplate = `{{ route('admin.pasien.get-catatan', ['id' => 'PASIEN_ID']) }}`;
        
        let currentPasienId = null;
        
        // --- ADMIN VIEW ONLY: Hapus/Nonaktifkan elemen yang tidak digunakan ---

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

        // ===== MODAL VIDEO (VIEW ONLY) =====
        document.querySelectorAll('.btn-video').forEach(button => {
            button.addEventListener('click', function() {
                const videoBeforePath = this.dataset.videoBefore;
                const videoAfterPath = this.dataset.videoAfter;

                const playerBefore = document.getElementById('video-before-player');
                const notFoundBefore = document.getElementById('video-before-not-found');
                const playerAfter = document.getElementById('video-after-player');
                const notFoundAfter = document.getElementById('video-after-not-found');
                
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

                videoModal.show();
            });
        });
        
        // Menghentikan video saat modal ditutup
        videoModal._element.addEventListener('hidden.bs.modal', function () {
            document.getElementById('video-before-player').pause();
            document.getElementById('video-after-player').pause();
        });


        const dokumenModal = new bootstrap.Modal(document.getElementById('dokumenModal'));

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
    });
</script>

@endsection
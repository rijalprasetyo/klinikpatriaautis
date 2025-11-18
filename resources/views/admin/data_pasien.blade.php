@extends('layouts.admin-sidebar')

@section('content')

{{-- Terapkan CSS baru di sini untuk menimpa gaya default Bootstrap dan memberikan tema biru-putih modern --}}
<style>
    /* Variabel Warna Baru */
    :root {
        --primary-blue: #007bff; /* Biru Primer untuk Aksi/Header */
        --secondary-blue: #0056b3; /* Biru Lebih Gelap */
        --text-dark: #343a40;
        --bg-light: #f8f9fa;
        --border-light: #dee2e6;
        --danger-red: #dc3545;
        --success-green: #28a745;
        --card-header-bg: var(--primary-blue); /* Biru untuk Header Card */
    }

    /* Tampilan Header & Kontainer */
    .container-fluid h2 {
        color: var(--secondary-blue);
        border-bottom: 2px solid var(--border-light);
        padding-bottom: 10px;
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

    /* Tampilan Tabel */
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
    .btn-info { background-color: var(--primary-blue) !important; border-color: var(--primary-blue) !important; }
    .btn-info:hover { background-color: var(--secondary-blue) !important; }
    .btn-warning { background-color: #ffc107 !important; border-color: #ffc107 !important; color: var(--text-dark) !important; }
    .btn-action-icon {
        padding: 0.3rem !important;
        width: 30px;
        height: 30px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .action-buttons-group {
        display: flex;
        gap: 5px;
    }

    /* Modal Styling */
    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .modal-header.bg-info { background-color: var(--primary-blue) !important; }
    .modal-header.bg-secondary { background-color: #6c757d !important; }

    /* Detail Table dalam Modal */
    .modal-body table th {
        width: 25%;
        font-weight: 600;
        color: var(--secondary-blue);
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
        display: none;
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
    }
    
    /* ======================================= */
    /* ====== MOBILE CARD STYLES ====== */
    /* ======================================= */
    .card-mobile-list {
        display: none; 
        margin-top: 15px;
    }

    .pasien-card {
        border: 1px solid var(--border-light);
        border-radius: 0.7rem; /* Sedikit lebih membulat */
        margin-bottom: 15px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background-color: white; /* Putih */
    }

    .card-header-status {
        padding: 12px 15px;
        background-color: var(--card-header-bg); /* Biru Primary */
        color: white;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid var(--secondary-blue); /* Garis pemisah biru tua */
    }
    /* Ganti warna header untuk tab "Mendatang" agar konsisten dengan tema Biru */
    .card-header-status.bg-secondary-theme {
        background-color: var(--primary-blue) !important; /* Tetap Biru */
        color: white;
    }
    
    .card-header-status .badge-status {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
        border-radius: 0.5rem;
        background-color: rgba(255, 255, 255, 0.2); /* Latar belakang badge transparan/putih di header */
        color: white;
    }

    .card-body-mobile {
        padding: 15px;
        background-color: white; /* Body tetap putih */
    }

    .card-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.9em;
        padding-bottom: 3px;
        border-bottom: 1px dashed var(--border-light); /* Pembeda data yang lembut */
    }
    .card-item:last-child {
        border-bottom: none;
    }

    .card-item-label {
        font-weight: 500;
        color: var(--secondary-blue); /* Label lebih menonjol */
        width: 45%;
        text-align: left;
    }

    .card-item-value {
        text-align: right;
        width: 55%;
        color: var(--text-dark);
    }

    .card-actions-mobile {
        padding: 10px 15px;
        border-top: 1px solid var(--border-light);
        display: flex;
        justify-content: flex-end;
        align-items: center;
        background-color: var(--bg-light); /* Latar belakang abu-abu muda untuk area aksi */
    }
    /* ======================================= */
    /* ====== RESPONSIVE MODAL & LAYOUT ====== */
    /* ======================================= */

    /* Modal Responsif untuk Layar Kecil */
    @media (max-width: 768px) {
        .modal-dialog {
            margin: 0.5rem; /* Margin tipis di layar kecil */
        }
        .modal-dialog.modal-lg, .modal-dialog.modal-xl {
            max-width: 95vw; /* Maksimal 95% lebar viewport */
        }

        /* Responsive Table/Card Toggle */
        .table-responsive {
            display: none;
        }
        .card-mobile-list {
            display: block;
        }

        /* Filter layout */
        .filter-group .col-md-3, .filter-group .col-md-2, .filter-group .col-sm-6 {
            flex: 0 0 100%;
            max-width: 100%;
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
        <div id="pasien-{{ $key }}">
            <h4 class="mb-3 mt-4 text-{{ $tab['color'] }}">{{ $tab['label'] }}</h4>
            
            {{-- FILTER BERDASARKAN TAB --}}
            <form method="GET" action="{{ route('admin.data-pasien') }}" id="filter-form-{{ $key }}" class="filter-group row mb-4 align-items-end">
                <input type="hidden" name="tab" value="{{ $key }}">
                
                {{-- FILTER TANGGAL --}}
                @if ($key == 'upcoming')
                    <div class="col-md-3 col-sm-6 mb-2">
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

                {{-- FILTER STATUS BERKAS --}}
                <div class="col-md-3 col-sm-6 mb-2">
                    <label for="filter_status_berkas_{{ $key }}" class="form-label">Status Berkas</label>
                    <select name="status_berkas" id="filter_status_berkas_{{ $key }}" class="form-select">
                        <option value="">-- Semua Status Berkas --</option>
                        <option value="Belum Diverifikasi" {{ $currentFilterStatusBerkas == 'Belum Diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="Sudah Diverifikasi" {{ $currentFilterStatusBerkas == 'Sudah Diverifikasi' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                    </select>
                </div>
                
                {{-- FILTER STATUS PEMERIKSAAN --}}
                <div class="col-md-3 col-sm-6 mb-2">
                    <label for="filter_status_pemeriksaan_{{ $key }}" class="form-label">Status Pemeriksaan</label>
                    <select name="status_pemeriksaan" id="filter_status_pemeriksaan_{{ $key }}" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="Belum Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Belum Diperiksa' ? 'selected' : '' }}>Belum Diperiksa</option>
                        <option value="Sudah Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Sudah Diperiksa' ? 'selected' : '' }}>Sudah Diperiksa</option>
                    </select>
                </div>
                
                {{-- FILTER NAMA PASIEN --}}
                <div class="col-md-3 col-sm-6 mb-2">
                    <label for="filter_nama_pasien_{{ $key }}" class="form-label">Cari Nama Pasien</label>
                    <input type="text" name="nama_pasien" id="filter_nama_pasien_{{ $key }}" class="form-control" placeholder="Ketik nama pasien..." value="{{ $currentFilterNamaPasien }}">
                </div>
                
                <div class="col-md-2 col-sm-6 mb-2 d-flex">
                    <button type="submit" class="btn btn-primary w-100 me-2" onclick="showLoading('{{ $key }}')"><i class="fa-solid fa-filter"></i> Filter</button>
                    <a href="{{ route('admin.data-pasien', ['tab' => $key]) }}" class="btn btn-outline-secondary w-100">Reset</a>
                </div>
            </form>
            {{-- END OF FILTER --}}

            {{-- KONTEN UTAMA: TABEL (Desktop) --}}
            <div class="table-responsive">
                {{-- Loading Overlay --}}
                <div class="loading-overlay" id="loading-{{ $key }}">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                @if($tab['data']->isEmpty())
                    <div class="alert alert-info text-center d-none d-md-block">Tidak ada pasien yang ditemukan pada jadwal ini (atau sesuai filter).</div>
                @else
                    
                    {{-- Tampilan Tabel (Desktop) --}}
                    <table class="table table-striped table-hover align-middle d-none d-md-table">
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
                                <th>Status Berkas</th>
                                <th>Status Periksa</th>
                                <th style="width: 15%;">Aksi</th>
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
                                    <td>{{ $pasien->layanan_id ?? '-' }}</td>
                                    <td>{{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}</td>
                                    <td>
                                        <span class="badge {{ $pasien->status_berkas == 'Belum Diverifikasi' ? 'bg-danger' : 'bg-success' }}">
                                            {{ $pasien->status_berkas }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $pasien->status_pemeriksaan == 'Belum Diperiksa' ? 'bg-danger' : 'bg-primary' }}">
                                            {{ $pasien->status_pemeriksaan }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons-group">
                                            {{-- Tombol Aksi (Hanya Ikon) --}}
                                            <button class="btn btn-sm btn-info text-white btn-detail btn-action-icon" data-id="{{ $pasien->id }}" title="Detail Pasien">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </button>
                                            
                                            <button class="btn btn-sm btn-warning btn-status-berkas btn-action-icon" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_berkas }}" title="Ubah Status Berkas">
                                                <i class="fa-solid fa-file-circle-check"></i>
                                            </button>

                                            <button class="btn btn-sm btn-secondary btn-dokumen btn-action-icon" data-bukti="{{ asset('public/storage/' . $pasien->bukti_pembayaran) }}" data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" title="Lihat Dokumen">
                                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

            {{-- Tampilan Card (Mobile) --}}
            <div class="card-mobile-list d-block d-md-none">
                @if($tab['data']->isEmpty())
                    <div class="alert alert-info text-center">Tidak ada pasien yang ditemukan pada jadwal ini (atau sesuai filter).</div>
                @else
                    @foreach ($tab['data'] as $pasien)
                        @php
                            $dateFormatted = \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY');
                            // Tetapkan warna header card ke Biru Primary untuk kedua tab
                            $headerClass = 'bg-primary'; 
                            $statusBerkasBadge = $pasien->status_berkas == 'Belum Diverifikasi' ? 'bg-danger' : 'bg-success';
                            $statusPeriksaBadge = $pasien->status_pemeriksaan == 'Belum Diperiksa' ? 'bg-danger' : 'bg-success';
                        @endphp
                        <div class="pasien-card">
                            <div class="card-header-status {{ $headerClass }}">
                                <div>
                                    <i class="fa-solid fa-user-injured me-2"></i> {{ $pasien->nama_pasien }}
                                </div>
                                <span class="badge-status {{ $statusPeriksaBadge }}">
                                    {{ $pasien->status_pemeriksaan }}
                                </span>
                            </div>
                            <div class="card-body-mobile">
                                @if ($key == 'upcoming')
                                    <div class="card-item">
                                        <span class="card-item-label"><i class="fa-solid fa-calendar-day me-1"></i> Tgl. Kunjungan</span>
                                        <span class="card-item-value fw-bold">{{ $dateFormatted }}</span>
                                    </div>
                                @endif
                                <div class="card-item">
                                    <span class="card-item-label"><i class="fa-solid fa-ticket-simple me-1"></i> Antrian</span>
                                    <span class="card-item-value fw-bold">{{ $pasien->nomor_antrian }}</span>
                                </div>
                                <div class="card-item">
                                    <span class="card-item-label"><i class="fa-solid fa-clock me-1"></i> Waktu</span>
                                    <span class="card-item-value">{{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}</span>
                                </div>
                                <div class="card-item">
                                    <span class="card-item-label"><i class="fa-solid fa-stethoscope me-1"></i> Layanan</span>
                                    <span class="card-item-value">{{ $pasien->layanan_id ?? '-' }} ({{ $pasien->kategori_pendaftaran }})</span>
                                </div>
                                <div class="card-item">
                                    <span class="card-item-label"><i class="fa-solid fa-file-invoice me-1"></i> Status Berkas</span>
                                    <span class="card-item-value"><span class="badge {{ $statusBerkasBadge }}">{{ $pasien->status_berkas }}</span></span>
                                </div>
                            </div>
                            <div class="card-actions-mobile">
                                <div class="action-buttons-group">
                                    {{-- Tombol Aksi --}}
                                    <button class="btn btn-sm btn-info text-white btn-detail btn-action-icon" data-id="{{ $pasien->id }}" title="Detail Pasien">
                                        <i class="fa-solid fa-file-invoice"></i>
                                    </button>
                                    
                                    <button class="btn btn-sm btn-warning btn-status-berkas btn-action-icon" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_berkas }}" title="Ubah Status Berkas">
                                        <i class="fa-solid fa-file-circle-check"></i>
                                    </button>

                                    <button class="btn btn-sm btn-secondary btn-dokumen btn-action-icon" data-bukti="{{ asset('public/storage/' . $pasien->bukti_pembayaran) }}" data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" title="Lihat Dokumen">
                                        <i class="fa-solid fa-cloud-arrow-down"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    @endforeach

</div>

{{-- ======================================================= --}}
{{-- MODALS (DIBUAT RESPONSIVE) --}}
{{-- Modal 1: Detail Pasien --}}
<div class="modal fade" id="detailPasienModal" tabindex="-1" aria-labelledby="detailPasienModalLabel" aria-hidden="true">
    {{-- Tambahkan modal-dialog-scrollable untuk modal yang panjang --}}
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
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

{{-- Modal 2: Ubah Status Berkas --}}
<div class="modal fade" id="statusBerkasModal" tabindex="-1" aria-labelledby="statusBerkasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="statusBerkasModalLabel"><i class="fa-solid fa-file-circle-check me-2"></i>Ubah Status Berkas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="form-status-berkas" method="POST" action="">
                @csrf
                <div class="modal-body">
                    <p>Ubah status berkas untuk pasien ini. Status saat ini: <strong id="current-status-text"></strong></p>
                    <div class="mb-3">
                        <label for="status_berkas" class="form-label">Status Berkas Baru</label>
                        <select name="status_berkas" id="status_berkas" class="form-select" required>
                            <option value="Belum Diverifikasi">Belum Diverifikasi</option>
                            <option value="Sudah Diverifikasi">Sudah Diverifikasi</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal 3: Lihat Dokumen --}}
<div class="modal fade" id="dokumenModal" tabindex="-1" aria-labelledby="dokumenModalLabel" aria-hidden="true">
    {{-- Modal dokumen menggunakan modal-xl agar tampilan dokumen maksimal --}}
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
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


{{-- SCRIPT INTERAKSI MODAL & AJAX (Tidak Berubah Fungsionalitas) --}}
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
        // Sembunyikan semua loading saat DOMContentLoaded
        document.getElementById('loading-today').style.display = 'none';
        document.getElementById('loading-upcoming').style.display = 'none';
        
        // --- SETUP TABS ---
        const btnToday = document.getElementById('btn-today');
        const btnUpcoming = document.getElementById('btn-upcoming');
        const pasienTodayDiv = document.getElementById('pasien-today');
        const pasienUpcomingDiv = document.getElementById('pasien-upcoming');
        
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab') || 'today';

        if (activeTab === 'upcoming') {
            pasienTodayDiv.style.display = 'none';
            pasienUpcomingDiv.style.display = 'block';
            btnToday.classList.remove('active', 'btn-primary');
            btnToday.classList.add('btn-secondary');
            btnUpcoming.classList.add('active', 'btn-primary');
            btnUpcoming.classList.remove('btn-secondary');
        } else {
            pasienTodayDiv.style.display = 'block';
            pasienUpcomingDiv.style.display = 'none';
        }

        document.getElementById('filter-form-today').style.display = activeTab === 'today' ? 'flex' : 'none';
        document.getElementById('filter-form-upcoming').style.display = activeTab === 'upcoming' ? 'flex' : 'none';


        function updateTabs(activeKey) {
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.delete('tab');
            const queryString = currentParams.toString();
            const newUrl = `{{ route('admin.data-pasien') }}?tab=${activeKey}` + (queryString ? `&${queryString}` : '');
            showLoading(activeKey);
            window.location.href = newUrl;
        }
        
        btnToday.addEventListener('click', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') !== 'today') {
                updateTabs('today');
            }
        });
        
        btnUpcoming.addEventListener('click', () => {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') !== 'upcoming') {
                updateTabs('upcoming');
            }
        });

        // Auto-submit saat filter select berubah (dan munculkan loading)
        document.getElementById('filter-form-today').querySelectorAll('select').forEach(select => {
             select.addEventListener('change', function() {
                showLoading('today');
                document.getElementById('filter-form-today').submit();
            });
        });
        
        document.getElementById('filter-form-upcoming').querySelectorAll('select').forEach(select => {
             select.addEventListener('change', function() {
                showLoading('upcoming');
                document.getElementById('filter-form-upcoming').submit();
            });
        });
        
        // Menambahkan fungsi loading pada tombol submit form
        document.getElementById('filter-form-today').querySelector('button[type="submit"]').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading('today');
            document.getElementById('filter-form-today').submit();
        });
        
        document.getElementById('filter-form-upcoming').querySelector('button[type="submit"]').addEventListener('click', function(e) {
            e.preventDefault();
            showLoading('upcoming');
            document.getElementById('filter-form-upcoming').submit();
        });

        // --- MODAL 1: LIHAT DETAIL (AJAX) ---
        const detailModal = new bootstrap.Modal(document.getElementById('detailPasienModal'));
        const detailUrlTemplate = `{{ route('admin.pasien.detail', ['id' => 'PASIEN_ID']) }}`; 

        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const loading = document.getElementById('loading-spinner');
                const detailTable = document.querySelector('#detailPasienModal table');
                
                loading.style.display = 'block';
                detailTable.style.display = 'none';
                detailModal.show();

                const fetchUrl = detailUrlTemplate.replace('PASIEN_ID', pasienId);

                fetch(fetchUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        const d = data.data;

                        // Mengisi data
                        document.getElementById('detail-antrian').textContent = d.nomor_antrian;
                        document.getElementById('detail-nama').textContent = d.nama_pasien;
                        document.getElementById('detail-hp').textContent = d.nomor_hp;
                        document.getElementById('detail-tgl-jk').textContent = `${d.tgl_lahir} / ${d.jenis_kelamin}`;
                        document.getElementById('detail-pendamping').textContent = d.pendamping;
                        document.getElementById('detail-tgl-kunjungan').textContent = d.tgl_kunjungan;
                        document.getElementById('detail-waktu').textContent = d.waktu_kunjungan; 
                        document.getElementById('detail-layanan').textContent = d.layanan; 
                        document.getElementById('detail-dokter').textContent = d.dokter_nama; 
                        document.getElementById('detail-kategori').textContent = d.kategori_pendaftaran;
                        document.getElementById('detail-alamat').textContent = d.alamat;
                        document.getElementById('detail-keluhan').textContent = d.keluhan;
                        document.getElementById('detail-status-pemeriksaan').textContent = d.status_pemeriksaan;
                        document.getElementById('detail-status-berkas').textContent = d.status_berkas;

                        // Sembunyikan loading dan tampilkan tabel
                        loading.style.display = 'none';
                        detailTable.style.display = 'table';
                    })
                    .catch(error => {
                        console.error('Error fetching detail:', error);
                        alert(`Gagal mengambil detail pasien: ${error.message}`);
                        detailModal.hide();
                    });
            });
        });


        // --- MODAL 2: UBAH STATUS BERKAS ---
        const statusModal = new bootstrap.Modal(document.getElementById('statusBerkasModal'));
        const formStatus = document.getElementById('form-status-berkas');
        const updateUrlTemplate = `{{ route('admin.pasien.update-berkas', ['id' => 'PASIEN_ID']) }}`;

        document.querySelectorAll('.btn-status-berkas').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const currentStatus = this.dataset.currentStatus;
                const selectStatus = document.getElementById('status_berkas');
                
                formStatus.action = updateUrlTemplate.replace('PASIEN_ID', pasienId);
                
                document.getElementById('current-status-text').textContent = currentStatus;
                selectStatus.value = currentStatus;
                
                statusModal.show();
            });
        });


        // --- MODAL 3: LIHAT DOKUMEN ---
        const dokumenModal = new bootstrap.Modal(document.getElementById('dokumenModal'));

        document.querySelectorAll('.btn-dokumen').forEach(button => {
            button.addEventListener('click', function() {
                const buktiPath = this.dataset.bukti;
                const sktmPath = this.dataset.sktm;
                
                document.getElementById('bukti-pembayaran-frame').src = buktiPath;

                const sktmFrame = document.getElementById('sktm-frame');
                const sktmNotFound = document.getElementById('sktm-not-found');
                const assetBaseUrl = '{{ asset('public/storage/') }}';

                // Tampilkan Bukti Pembayaran
                if (buktiPath && !buktiPath.endsWith(assetBaseUrl)) {
                    document.getElementById('bukti-pembayaran-frame').src = buktiPath;
                } else {
                     document.getElementById('bukti-pembayaran-frame').src = '';
                }

                // Tampilkan SKTM
                if (sktmPath && !sktmPath.endsWith(assetBaseUrl)) {
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
    });
</script>
@endsection
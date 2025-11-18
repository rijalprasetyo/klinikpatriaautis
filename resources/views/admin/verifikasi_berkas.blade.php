@extends('layouts.admin-sidebar')

@section('content')

{{-- Terapkan CSS yang sama dari data_pasien untuk konsistensi tema --}}
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
    
    /* Tampilan Tabel */
    .table-responsive {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        overflow: hidden;
        margin-top: 20px;
        position: relative;
        min-height: 100px;
    }
    
    .table-info thead tr {
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

    /* Tombol Aksi Icon Only (Wajib untuk Tampilan Rapi di Semua Resolusi) */
    .btn-action-icon-verif {
        padding: 0.3rem !important; 
        width: 35px !important; 
        height: 35px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* Mengelompokkan tombol aksi */
    .action-buttons-group {
        display: flex;
        gap: 5px;
        align-items: center;
        flex-wrap: nowrap;
    }

    /* Sembunyikan Teks pada Tombol Aksi (Hanya Ikon yang Tampil) */
    .action-buttons-group .btn-sm span {
        display: none !important;
    }

    /* ======================================= */
    /* ====== MOBILE CARD STYLES (BIRU-PUTIH) ====== */
    /* ======================================= */
    .card-mobile-list {
        display: none; 
        margin-top: 15px;
    }

    .pasien-card {
        border: 1px solid var(--border-light);
        border-radius: 0.7rem; 
        margin-bottom: 15px;
        overflow: hidden;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        background-color: white; 
    }

    .card-header-status {
        padding: 12px 15px;
        background-color: var(--card-header-bg);
        color: white;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 2px solid var(--secondary-blue);
    }
    
    .card-header-status .badge-status {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
        border-radius: 0.5rem;
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
    }

    .card-body-mobile {
        padding: 15px;
        background-color: white; 
    }

    .card-item {
        display: flex;
        flex-wrap: wrap; 
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.9em;
        padding-bottom: 3px;
        border-bottom: 1px dashed var(--border-light); 
    }
    .card-item:last-child {
        border-bottom: none;
    }

    .card-item-label {
        font-weight: 500;
        color: var(--secondary-blue);
        width: 45%;
        text-align: left;
    }

    .card-item-value {
        text-align: right;
        width: 55%;
        color: var(--text-dark);
    }

    .card-item-value.contact-links {
        width: 100%;
        text-align: left;
        margin-top: 5px; 
    }
    .card-item-value.contact-links a {
        display: block;
    }

    .card-actions-mobile {
        padding: 10px 15px;
        border-top: 1px solid var(--border-light);
        display: flex;
        justify-content: flex-end;
        align-items: center;
        background-color: var(--bg-light); 
    }
    .card-actions-mobile .action-buttons-group {
        width: 100%;
        justify-content: space-between;
    }
    /* ======================================= */
    /* ====== RESPONSIVE MODAL & LAYOUT ====== */
    /* ======================================= */

    /* Sembunyikan Tabel di layar kecil */
    @media (max-width: 768px) {
        .table-responsive > table {
            display: none;
        }
        .card-mobile-list {
            display: block;
        }

        /* Responsive Modal */
        .modal-dialog {
            margin: 0.5rem; 
        }
        .modal-dialog.modal-lg, .modal-dialog.modal-xl {
            max-width: 95vw; 
        }

        /* Filter layout */
        .filter-group .col-md-4 {
            flex: 0 0 100%;
            max-width: 100%;
        }
        .filter-group .col-md-4:last-child {
            margin-top: 5px;
        }
    }
</style>

<div class="container-fluid">
    <h2 class="mb-4"><i class="fa-solid fa-file-check me-2"></i> Halaman Verifikasi Berkas Pasien</h2>
    <p class="text-muted">Daftar semua pasien yang **Belum Diperiksa**. Verifikasi kelengkapan berkas sebelum pasien masuk ruang periksa.</p>

    <hr>

    {{-- Alert Notifikasi --}}
    @if(session('success'))
        <div class="alert alert-success mt-3">{{ session('success') }}</div>
    @endif
    
    {{-- FILTER VERIFIKASI --}}
    <form method="GET" action="{{ route('admin.verifikasi-berkas') }}" id="filter-verifikasi-form" class="filter-group row mb-4 align-items-end">
        
        <div class="col-md-4 mb-2">
            <label for="filter_kategori" class="form-label">Filter Kategori Pendaftaran</label>
            <select name="kategori" id="filter_kategori" class="form-select" onchange="document.getElementById('filter-verifikasi-form').submit()">
                <option value="">-- Semua Kategori --</option>
                @foreach ($availableKategori as $kategori)
                    <option value="{{ $kategori }}" {{ $currentKategori == $kategori ? 'selected' : '' }}>
                        {{ $kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-md-4 mb-2 d-flex align-items-end">
            <a href="{{ route('admin.verifikasi-berkas') }}" class="btn btn-outline-secondary w-100">Reset Filter</a>
        </div>
    </form>

    {{-- DAFTAR PASIEN UNTUK VERIFIKASI --}}
    <div class="table-responsive">
        @if($pasienVerifikasi->isEmpty())
            <div class="alert alert-info text-center">Tidak ada pasien yang menunggu verifikasi berkas saat ini (atau sesuai filter).</div>
        @else
            {{-- TABEL (Desktop Only) --}}
            <table class="table table-striped table-hover align-middle d-none d-md-table">
                <thead class="table-info">
                    <tr>
                        <th>Antrian</th>
                        <th>Tanggal</th>
                        <th>Nama Pasien</th>
                        <th>Pendamping</th>
                        <th>Kategori</th>
                        <th>Kontak</th>
                        <th>Status Berkas</th>
                        <th style="width: 15%;">Aksi</th> {{-- Dikecilkan lagi lebarnya --}}
                    </tr>
                </thead>
                <tbody>
                    @foreach ($pasienVerifikasi as $pasien)
                        <tr>
                            <td class="fw-bold">{{ $pasien->nomor_antrian }}</td>
                            <td>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $pasien->nama_pasien }}</td>
                            <td>{{ $pasien->pendamping ?? '-' }}</td>
                            <td>{{ $pasien->kategori_pendaftaran }}</td>
                            <td>
                                {{-- Link Kontak --}}
                                <a href="https://wa.me/62{{ ltrim(preg_replace('/[^0-9]/', '', $pasien->nomor_hp), '0') }}" 
                                target="_blank" 
                                class="text-success me-2" 
                                title="Hubungi via WhatsApp">
                                <i class="fa-brands fa-whatsapp me-1"></i> {{ $pasien->nomor_hp }}
                                </a>

                                @if($pasien->email)
                                <a href="mailto:{{ $pasien->email }}" class="text-danger" title="Kirim Email">
                                    <i class="fa-solid fa-envelope me-1"></i> Email
                                </a>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $pasien->status_berkas == 'Belum Diverifikasi' ? 'bg-danger' : 'bg-success' }}">
                                    {{ $pasien->status_berkas }}
                                </span>
                            </td>
                            <td>
                                {{-- KELOMPOK TOMBOL AKSI HANYA IKON (DESKTOP) --}}
                                <div class="action-buttons-group">
                                    <button class="btn btn-sm btn-info text-white btn-detail btn-action-icon-verif" data-id="{{ $pasien->id }}" title="Detail Pasien">
                                        <i class="fa-solid fa-file-invoice"></i> <span>Detail</span>
                                    </button>
                                    
                                    <button class="btn btn-sm btn-warning btn-status-berkas btn-action-icon-verif" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_berkas }}" title="Verifikasi Berkas">
                                        <i class="fa-solid fa-file-circle-check"></i> <span>Status Berkas</span>
                                    </button>

                                    <button class="btn btn-sm btn-secondary btn-dokumen btn-action-icon-verif" data-bukti="{{ asset('public/storage/' . $pasien->bukti_pembayaran) }}" data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" title="Lihat Dokumen">
                                        <i class="fa-solid fa-cloud-arrow-down"></i> <span>Dokumen</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{-- CARD (Mobile Only) --}}
            <div class="card-mobile-list d-block d-md-none">
                @foreach ($pasienVerifikasi as $pasien)
                    @php
                        $dateFormatted = \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY');
                        $statusBerkasBadge = $pasien->status_berkas == 'Belum Diverifikasi' ? 'bg-danger' : 'bg-success';
                    @endphp
                    <div class="pasien-card">
                        <div class="card-header-status">
                            <div>
                                <i class="fa-solid fa-file-check me-2"></i> **{{ $pasien->nama_pasien }}**
                            </div>
                            <span class="badge-status {{ $statusBerkasBadge }}">
                                {{ $pasien->status_berkas }}
                            </span>
                        </div>
                        <div class="card-body-mobile">
                            <div class="card-item">
                                <span class="card-item-label"><i class="fa-solid fa-ticket-simple me-1"></i> Antrian</span>
                                <span class="card-item-value fw-bold">{{ $pasien->nomor_antrian }}</span>
                            </div>
                            <div class="card-item">
                                <span class="card-item-label"><i class="fa-solid fa-calendar-day me-1"></i> Tgl Kunjungan</span>
                                <span class="card-item-value">{{ $dateFormatted }}</span>
                            </div>
                            <div class="card-item">
                                <span class="card-item-label"><i class="fa-solid fa-user-tag me-1"></i> Kategori</span>
                                <span class="card-item-value">{{ $pasien->kategori_pendaftaran }}</span>
                            </div>
                            <div class="card-item">
                                <span class="card-item-label"><i class="fa-solid fa-user me-1"></i> Pendamping</span>
                                <span class="card-item-value">{{ $pasien->pendamping ?? '-' }}</span>
                            </div>
                            <div class="card-item">
                                <span class="card-item-label"><i class="fa-solid fa-phone me-1"></i> Kontak</span>
                                <span class="card-item-value contact-links">
                                    <a href="https://wa.me/62{{ ltrim(preg_replace('/[^0-9]/', '', $pasien->nomor_hp), '0') }}" target="_blank" class="text-success small">
                                        <i class="fa-brands fa-whatsapp me-1"></i> {{ $pasien->nomor_hp }}
                                    </a>
                                    @if($pasien->email)
                                    <a href="mailto:{{ $pasien->email }}" class="text-danger small">
                                        <i class="fa-solid fa-envelope me-1"></i> Email
                                    </a>
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="card-actions-mobile">
                            <div class="action-buttons-group">
                                {{-- Tombol Aksi (Hanya Ikon) --}}
                                <button class="btn btn-sm btn-info text-white btn-detail btn-action-icon-verif" data-id="{{ $pasien->id }}" title="Detail Pasien">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </button>
                                
                                <button class="btn btn-sm btn-warning btn-status-berkas btn-action-icon-verif" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_berkas }}" title="Verifikasi Berkas">
                                    <i class="fa-solid fa-file-circle-check"></i>
                                </button>

                                <button class="btn btn-sm btn-secondary btn-dokumen btn-action-icon-verif" data-bukti="{{ asset('public/storage/' . $pasien->bukti_pembayaran) }}" data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" title="Lihat Dokumen">
                                    <i class="fa-solid fa-cloud-arrow-down"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- MODALS (RESPONSIVE) --}}
<div class="modal fade" id="detailPasienModal" tabindex="-1" aria-labelledby="detailPasienModalLabel" aria-hidden="true">
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

<div class="modal fade" id="statusBerkasModal" tabindex="-1" aria-labelledby="statusBerkasModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="statusBerkasModalLabel"><i class="fa-solid fa-file-check me-2"></i>Ubah Status Berkas</h5>
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

<div class="modal fade" id="dokumenModal" tabindex="-1" aria-labelledby="dokumenModalLabel" aria-hidden="true">
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


{{-- SCRIPT INTERAKSI MODAL & AJAX --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- MODAL 1: LIHAT DETAIL (AJAX) ---
        const detailModal = new bootstrap.Modal(document.getElementById('detailPasienModal'));
        const detailUrlTemplate = `{{ route('admin.pasien.detail', ['id' => 'PASIEN_ID']) }}`; 

        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const loading = document.getElementById('loading-spinner');
                const detailTable = document.querySelector('#detailPasienModal table');
                
                detailTable.style.display = 'none';
                loading.style.display = 'block';
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
                        
                        document.getElementById('detail-antrian').textContent = d.nomor_antrian;
                        document.getElementById('detail-nama').textContent = d.nama_pasien;
                        document.getElementById('detail-hp').textContent = d.nomor_hp;
                        document.getElementById('detail-tgl-jk').textContent = `${d.tgl_lahir} / ${d.jenis_kelamin}`;
                        document.getElementById('detail-pendamping').textContent = d.pendamping;
                        document.getElementById('detail-tgl-kunjungan').textContent = d.tgl_kunjungan;
                        document.getElementById('detail-waktu').textContent = d.waktu_kunjungan;
                        document.getElementById('detail-layanan').textContent = d.layanan;
                        document.getElementById('detail-kategori').textContent = d.kategori_pendaftaran;
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
                
                const sktmFrame = document.getElementById('sktm-frame');
                const sktmNotFound = document.getElementById('sktm-not-found');
                const assetBaseUrl = '{{ asset('public/storage/') }}';

                // Bukti Pembayaran
                if (buktiPath && !buktiPath.endsWith(assetBaseUrl)) {
                    document.getElementById('bukti-pembayaran-frame').src = buktiPath;
                } else {
                     document.getElementById('bukti-pembayaran-frame').src = '';
                }

                // SKTM
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
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
    }
    
    .table-info thead tr { /* Mengganti table-primary/secondary dengan table-info untuk Verifikasi */
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

    /* Modal Styling */
    .modal-content {
        border-radius: 1rem;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
    }
    .modal-header.bg-info { background-color: var(--primary-blue) !important; }
    .modal-header.bg-warning { background-color: #ffc107 !important; }

    /* Detail Table dalam Modal */
    .modal-body table th {
        width: 25%;
        font-weight: 600;
        color: var(--secondary-blue);
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
    
    {{-- ======================================================= --}}
    {{-- FILTER VERIFIKASI --}}
    {{-- ======================================================= --}}
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
        
        <div class="col-md-4 mb-2">
            <label for="filter_status_berkas" class="form-label">Filter Status Berkas</label>
            <select name="status_berkas" id="filter_status_berkas" class="form-select" onchange="document.getElementById('filter-verifikasi-form').submit()">
                <option value="">-- Semua Status Berkas --</option>
                @foreach ($availableStatus as $status)
                    <option value="{{ $status }}" {{ $currentStatusBerkas == $status ? 'selected' : '' }}>
                        {{ $status }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 mb-2">
            <a href="{{ route('admin.verifikasi-berkas') }}" class="btn btn-outline-secondary w-100">Reset Filter</a>
        </div>
    </form>

    {{-- ======================================================= --}}
    {{-- DAFTAR PASIEN UNTUK VERIFIKASI --}}
    {{-- ======================================================= --}}

    <div class="table-responsive">
        @if($pasienVerifikasi->isEmpty())
            <div class="alert alert-info text-center">Tidak ada pasien yang menunggu verifikasi berkas saat ini (atau sesuai filter).</div>
        @else
            <table class="table table-striped table-hover align-middle">
                <thead class="table-info">
                    <tr>
                        <th>Antrian</th>
                        <th>Tanggal</th>
                        <th>Nama Pasien</th>
                        <th>Pendamping</th>
                        <th>Kategori</th>
                        <th>Kontak</th>
                        <th>Status Berkas</th>
                        <th style="width: 28%;">Aksi</th>
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
                                {{-- Link Langsung WhatsApp dan Email --}}
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
                                <button class="btn btn-sm btn-info text-white me-1 btn-detail" data-id="{{ $pasien->id }}" title="Detail Pasien">
                                    <i class="fa-solid fa-file-invoice"></i> Detail
                                </button>
                                
                                <button class="btn btn-sm btn-warning me-1 btn-status-berkas" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_berkas }}" title="Verifikasi Berkas">
                                    <i class="fa-solid fa-file-check"></i> Status Berkas
                                </button>

                                <button class="btn btn-sm btn-secondary btn-dokumen" data-bukti="{{ asset('public/storage/' . $pasien->bukti_pembayaran) }}" data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" title="Lihat Dokumen">
                                    <i class="fa-solid fa-cloud-arrow-down"></i> Dokumen
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

{{-- ======================================================= --}}
{{-- MODALS (Menggunakan modal yang sama dengan data_pasien) --}}
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


{{-- ======================================================= --}}
{{-- SCRIPT INTERAKSI MODAL & AJAX --}}
{{-- ======================================================= --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- MODAL 1: LIHAT DETAIL (AJAX) ---
        const detailModal = new bootstrap.Modal(document.getElementById('detailPasienModal'));
        // Template URL untuk mengambil detail pasien (menggunakan route yang sudah ada)
        const detailUrlTemplate = `{{ route('admin.pasien.detail', ['id' => 'PASIEN_ID']) }}`;

        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                const pasienId = this.dataset.id;
                const loading = document.getElementById('loading-spinner');
                const detailTable = document.querySelector('#detailPasienModal table');
                
                // Reset tampilan modal dan tampilkan loading
                detailTable.style.display = 'none';
                loading.style.display = 'block';
                detailModal.show();

                const fetchUrl = detailUrlTemplate.replace('PASIEN_ID', pasienId);

                fetch(fetchUrl)
                    .then(response => response.json())
                    .then(data => {
                        const d = data.data;
                        document.getElementById('detail-antrian').textContent = d.nomor_antrian;
                        document.getElementById('detail-nama').textContent = d.nama_pasien;
                        document.getElementById('detail-hp').textContent = d.nomor_hp;
                        // Diasumsikan controller Anda memiliki field 'email' di response detail
                        // document.getElementById('detail-email').textContent = d.email; 
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
        // Template URL untuk update status berkas (menggunakan route yang sudah ada)
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
    });
</script>
@endsection
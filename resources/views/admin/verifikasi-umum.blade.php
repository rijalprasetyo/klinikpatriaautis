@extends('layouts.admin-sidebar')

@section('content')

<style>
/* ... (Bagian Style CSS tidak berubah, tambahkan tab style jika diperlukan) ... */
:root {
    --primary-blue: #007bff;
    --secondary-blue: #0056b3;
    --text-dark: #343a40;
    --bg-light: #f8f9fa;
    --border-light: #dee2e6;
}

/* Penyesuaian Container untuk Memaksimalkan Lebar */
.container-fluid {
    padding-left: 10px !important;
    padding-right: 10px !important;
}

.container-fluid h2 {
    color: var(--secondary-blue);
    border-bottom: 2dpx solid var(--border-light);
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

/* Warna untuk Ubah Status */
.btn-status-ubah { 
    background-color: #ffc107 !important; 
    border-color: #ffc107 !important; 
    color: var(--text-dark) !important; 
}
.btn-status-ubah:hover { background-color: #e0a800 !important; }

.btn-action-icon {
    padding: 0.3rem !important;
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

/* Penyesuaian lebar kolom aksi untuk menampung 4 tombol */
.action-buttons {
    display: flex;
    gap: 5px;
    align-items: center;
}

.modal-content {
    border-radius: 1rem;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.15);
}
.modal-header.bg-info { background-color: var(--primary-blue) !important; }
.modal-header.bg-warning { background-color: #ffc107 !important; color: var(--text-dark) !important; }
.modal-header.bg-secondary { background-color: #6c757d !important; }


.modal-body table th {
    width: 30%; 
    font-weight: 600;
    color: var(--secondary-blue);
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
.badge.bg-berkas-merah { /* Ditolak */
    background-color: #dc3545 !important;
}
.badge.bg-berkas-biru { /* Sudah Diverifikasi */
    background-color: #007bff !important;
}
.badge.bg-berkas-kuning { /* Menunggu (Penting) */
    background-color: #ffc107 !important; 
    color: var(--text-dark) !important;
}

/* Tambahkan style untuk tab-content agar terlihat bagus */
.tab-content > .active {
    display: block;
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
    <h2 class="mb-4"><i class="fa-solid fa-people-group me-2"></i> Verifikasi Pasien Masyarakat Umum</h2>
    <p class="text-muted">Daftar pasien dengan kategori Masyarakat Umum yang memerlukan verifikasi berkas.</p>
    <h6 clas="text-muted">Gunakan Check List untuk menandai pengembalian Dana</h6>
    <hr>

    {{-- Alert Notifikasi --}}
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
    <form method="GET" action="{{ route('admin.verifikasi-umum') }}" id="filter-form" class="filter-group row mb-4 align-items-end">
        
        {{-- Filter Nama Pasien (Status Berkas dihapus) --}}
        <div class="col-md-3 col-sm-6 mb-2">
            <label for="nama_pasien" class="form-label">Cari Nama</label>
            <input type="text" name="nama_pasien" id="nama_pasien" class="form-control" placeholder="Nama pasien..." value="{{ $filterNamaPasien }}">
        </div>
        
        <div class="col-md-3 col-sm-6 mb-2 d-flex gap-2">
            <button type="submit" class="btn btn-primary flex-fill" onclick="showLoading()">
                <i class="fa-solid fa-filter"></i> Filter
            </button>
            <a href="{{ route('admin.verifikasi-umum') }}" class="btn btn-outline-secondary flex-fill">Reset</a>
        </div>
    </form>


    {{-- TAB NAVIGATION --}}
    <ul class="nav nav-tabs mb-3" id="verifikasiTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="listing-tab" data-bs-toggle="tab" data-bs-target="#listing" type="button" role="tab" aria-controls="listing" aria-selected="true">
                Listing <span class="badge bg-primary ms-1">{{ $dataListing->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="reject-tab" data-bs-toggle="tab" data-bs-target="#reject" type="button" role="tab" aria-controls="reject" aria-selected="false">
                Reject <span class="badge bg-danger ms-1">{{ $dataReject->count() }}</span>
            </button>
        </li>
    </ul>

    {{-- TAB CONTENT --}}
    <div class="tab-content" id="verifikasiTabsContent">
        
        {{-- TAB 1: LISTING (Menunggu) --}}
        <div class="tab-pane fade show active" id="listing" role="tabpanel" aria-labelledby="listing-tab">
            <div class="table-responsive">
                
                {{-- Loading Overlay --}}
                <div class="loading-overlay" id="loading-listing">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                @if($dataListing->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Tidak ada data pasien **Masyarakat Umum** dengan status **Menunggu**.
                    </div>
                @else
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama Pasien</th>
                                <th>Layanan</th>
                                <th>Nomor HP</th>
                                <th>Tgl Kunjungan</th>
                                <th>Status Berkas</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataListing as $index => $pasien)
                                <tr>
                                    <td class="fw-bold">{{ $index + 1 }}</td>
                                    <td>{{ $pasien->nama_pasien }}</td>
                                    <td>{{ $pasien->layanan_id ?? '-' }}</td>
                                    <td>
                                        @if($pasien->nomor_hp)
                                            @php
                                                $cleanHp = preg_replace('/[^0-9]/', '', $pasien->nomor_hp);
                                                $waNumber = (substr($cleanHp, 0, 1) === '0') ? '62' . substr($cleanHp, 1) : $cleanHp;
                                                $waLink = 'https://wa.me/' . $waNumber;
                                            @endphp
                                            <a href="{{ $waLink }}" target="_blank" class="text-success text-decoration-none">
                                                <i class="fab fa-whatsapp me-1"></i> {{ $pasien->nomor_hp }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</td>
                                    <td>
                                        @php
                                            $fileStatus = $pasien->status_berkas ?? 'Menunggu';
                                            $fileBadgeClass = 'bg-berkas-kuning'; 
                                            // Status di Listing hanya 'Menunggu'
                                        @endphp
                                        <span class="badge {{ $fileBadgeClass }}">
                                            {{ $fileStatus }}
                                        </span>
                                    </td> 
                                    <td>
                                        <div class="action-buttons">
                                            
                                            {{-- Tombol Detail (Lihat) --}}
                                            <button type="button" class="btn btn-sm btn-info text-white btn-detail btn-action-icon" 
                                                    data-id="{{ $pasien->id }}" 
                                                    title="Detail Pasien">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </button>
                                            
                                            {{-- Tombol Dokumen (Lihat) --}}
                                            <button type="button" class="btn btn-sm btn-secondary btn-dokumen btn-action-icon" 
                                                    data-bukti="{{ $pasien->bukti_pembayaran ? asset('public/storage/' . $pasien->bukti_pembayaran) : '' }}" 
                                                    data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" 
                                                    title="Lihat Dokumen">
                                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                            </button>

                                            {{-- Tombol Ubah Status Berkas --}}
                                            <button type="button" class="btn btn-sm btn-status-ubah btn-action-icon" 
                                                    data-id="{{ $pasien->id }}" 
                                                    data-current-status="{{ $fileStatus }}" 
                                                    title="Ubah Status Berkas">
                                                <i class="fa-solid fa-clipboard-check"></i>
                                            </button>

                                            {{-- Checkbox dihapus dari sini --}}
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>

        {{-- TAB 2: REJECT (Ditolak) --}}
        <div class="tab-pane fade" id="reject" role="tabpanel" aria-labelledby="reject-tab">
            <div class="table-responsive">
                
                {{-- Loading Overlay --}}
                <div class="loading-overlay" id="loading-reject">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                @if($dataReject->isEmpty())
                    <div class="alert alert-info text-center">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Tidak ada data pasien **Masyarakat Umum** dengan status **Ditolak**.
                    </div>
                @else
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-primary">
                            <tr>
                                <th>No</th>
                                <th>Nama Pasien</th>
                                <th>Layanan</th>
                                <th>Nomor HP</th>
                                <th>Tgl Kunjungan</th>
                                <th>Status Berkas</th>
                                <th style="width: 20%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataReject as $index => $pasien)
                                <tr>
                                    <td class="fw-bold">{{ $index + 1 }}</td>
                                    <td>{{ $pasien->nama_pasien }}</td>
                                    <td>{{ $pasien->layanan_id ?? '-' }}</td>
                                    <td>
                                        @if($pasien->nomor_hp)
                                            @php
                                                $cleanHp = preg_replace('/[^0-9]/', '', $pasien->nomor_hp);
                                                $waNumber = (substr($cleanHp, 0, 1) === '0') ? '62' . substr($cleanHp, 1) : $cleanHp;
                                                $waLink = 'https://wa.me/' . $waNumber;
                                            @endphp
                                            <a href="{{ $waLink }}" target="_blank" class="text-success text-decoration-none">
                                                <i class="fab fa-whatsapp me-1"></i> {{ $pasien->nomor_hp }}
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</td>
                                    <td>
                                        @php
                                            $fileStatus = $pasien->status_berkas ?? 'Menunggu'; // Seharusnya 'Ditolak' di tab ini
                                            $fileBadgeClass = 'bg-berkas-merah'; 
                                        @endphp
                                        <span class="badge {{ $fileBadgeClass }}">
                                            {{ $fileStatus }}
                                        </span>
                                    </td> 
                                    <td>
                                        <div class="action-buttons">
                                            
                                            {{-- Tombol Detail (Lihat) --}}
                                            <button type="button" class="btn btn-sm btn-info text-white btn-detail btn-action-icon" 
                                                    data-id="{{ $pasien->id }}" 
                                                    title="Detail Pasien">
                                                <i class="fa-solid fa-file-invoice"></i>
                                            </button>
                                            
                                            {{-- Tombol Dokumen (Lihat) --}}
                                            <button type="button" class="btn btn-sm btn-secondary btn-dokumen btn-action-icon" 
                                                    data-bukti="{{ $pasien->bukti_pembayaran ? asset('public/storage/' . $pasien->bukti_pembayaran) : '' }}" 
                                                    data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" 
                                                    title="Lihat Dokumen">
                                                <i class="fa-solid fa-cloud-arrow-down"></i>
                                            </button>

                                            {{-- Tombol Ubah Status Berkas --}}
                                            <button type="button" class="btn btn-sm btn-status-ubah btn-action-icon" 
                                                    data-id="{{ $pasien->id }}" 
                                                    data-current-status="{{ $fileStatus }}" 
                                                    title="Ubah Status Berkas">
                                                <i class="fa-solid fa-clipboard-check"></i>
                                            </button>
                                            <input type="checkbox" 
                                                class="form-check-input" 
                                                data-id="{{ $pasien->id }}"
                                                {{ $pasien->check ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
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
                        <tr><th>Layanan</th><td id="detail-layanan-name"></td><th>Kategori</th><td id="detail-kategori"></td></tr> 
                        <tr><th>Tgl Lahir / JK</th><td id="detail-tgl-jk"></td><th>Pendamping</th><td id="detail-pendamping"></td></tr>
                        <tr><th>Tgl Kunjungan</th><td id="detail-tgl-kunjungan"></td><th>Waktu Kunjungan</th><td id="detail-waktu"></td></tr>
                        <tr><th>Keluhan</th><td colspan="3" id="detail-keluhan"></td></tr>
                        <tr><th>Alamat</th><td colspan="3" id="detail-alamat"></td></tr>
                        <tr><th>Status Berkas</th><td colspan="3" id="detail-status-berkas"></td></tr>
                    </tbody>
                </table>
                <div id="loading-spinner" class="text-center" style="display: none;">
                    <div class="spinner-border text-info" role="status"></div> Loading...
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

{{-- Modal 2: Lihat Dokumen --}}
<div class="modal fade" id="dokumenModal" tabindex="-1" aria-labelledby="dokumenModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title" id="dokumenModalLabel"><i class="fa-solid fa-folder-open me-2"></i>Dokumen Pasien</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">Pastikan telah melihat **Bukti Pembayaran** dan **SKTM** (jika ada) sebelum memverifikasi status berkas.</p>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <h6>Bukti Pembayaran</h6>
                        <iframe id="bukti-pembayaran-frame" style="width: 100%; height: 500px; border: 1px solid var(--border-light);"></iframe>
                        <p id="bukti-not-found" class="alert alert-warning text-center mt-2" style="display: none;">Bukti Pembayaran tidak diunggah.</p>
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

{{-- Modal 3: Ubah Status Berkas --}}
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="statusModalLabel">
                    <i class="fa-solid fa-check-to-slot me-2"></i> Ubah Status Berkas
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="status-update-form" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <p class="text-muted">Pasien: <strong id="status-pasien-nama"></strong></p>
                    
                    <div class="mb-3">
                        <label for="status_berkas_select" class="form-label fw-bold">Pilih Status Verifikasi Berkas</label>
                        <select name="status_berkas" id="status_berkas_select" class="form-select">
                            <option value="Menunggu">Menunggu</option>
                            <option value="Sudah Diverifikasi">Sudah Diverifikasi</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>

                    <div id="status-loading-spinner" class="text-center" style="display: none;">
                        <div class="spinner-border text-warning" role="status"></div> Mengirim...
                    </div>
                </div>
                <div class="modal-footer" id="status-modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark" id="btn-save-status">
                        <i class="fa-solid fa-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- JAVASCRIPT --}}
<script>
    function showLoading() {
        // Tampilkan loading di kedua tab saat filter dijalankan
        document.getElementById('loading-listing').style.display = 'flex';
        document.getElementById('loading-reject').style.display = 'flex';
        const activeTabContent = document.querySelector('.tab-content .active .table-responsive table');
        if (activeTabContent) activeTabContent.style.opacity = '0.5';
    }
    
    // Helper untuk membuat link WhatsApp (tetap sama)
    function createWhatsAppLink(phoneNumber) {
        if (!phoneNumber || phoneNumber === '-') return phoneNumber;
        let cleanHp = phoneNumber.replace(/[^0-9]/g, '');
        let waNumber = (cleanHp.startsWith('0')) ? '62' + cleanHp.substring(1) : cleanHp;
        const waLink = 'https://wa.me/' + waNumber;
        return `<a href="${waLink}" target="_blank" class="text-success text-decoration-none"><i class="fab fa-whatsapp me-1"></i> ${phoneNumber}</a>`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const detailModal = new bootstrap.Modal(document.getElementById('detailPasienModal'));
        const dokumenModal = new bootstrap.Modal(document.getElementById('dokumenModal'));
        const statusModal = new bootstrap.Modal(document.getElementById('statusModal'));
        
        // --- URL Endpoints --- (tetap sama)
        const detailUrlTemplate = `{{ route('admin.pasien.detail', ['id' => 'PASIEN_ID']) }}`;
        const updateStatusUrlTemplate = `{{ route('admin.berkas.update-masyarakat-umum', ['id' => 'PASIEN_ID']) }}`; 
        
        let currentPasienId = null;
        
        // Sembunyikan semua loading overlay setelah DOM siap
        document.getElementById('loading-listing').style.display = 'none';
        document.getElementById('loading-reject').style.display = 'none';

        // Event listener untuk tab, pastikan loading dihapus
        const verifikasiTabs = document.getElementById('verifikasiTabs');
        if (verifikasiTabs) {
            verifikasiTabs.addEventListener('show.bs.tab', function (event) {
                // Sembunyikan overlay pada tab yang akan aktif
                const targetTabId = event.target.getAttribute('data-bs-target');
                const loadingOverlay = document.querySelector(`${targetTabId} .loading-overlay`);
                if (loadingOverlay) loadingOverlay.style.display = 'none';
                
                const table = document.querySelector(`${targetTabId} .table-responsive table`);
                if (table) table.style.opacity = '1';
            });
        }


        // ===== CHECKLIST NON-DATABASE (Sederhana) - Berada di Tab Reject =====
        document.querySelectorAll('#reject .form-check-input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener('change', function() {

                const checkedIds = Array.from(
                    document.querySelectorAll('#reject .form-check-input[type="checkbox"]:checked')
                ).map(cb => cb.dataset.id);

                fetch("{{ route('admin.update-checkbox') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        checked: checkedIds
                    })
                })
                .then(res => res.json())
                .then(res => console.log(res));
            });
        });




        // ===== MODAL DETAIL PASIEN (VIEW ONLY) - Diambil dari kedua tab =====
        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                // ... (Logic detail tetap sama) ...
                const pasienId = this.dataset.id;
                const loading = document.getElementById('loading-spinner');
                const detailTable = document.querySelector('#detailPasienModal table');
                
                // Reset dan tampilkan spinner
                loading.style.display = 'block';
                detailTable.style.display = 'none';
                detailModal.show();

                fetch(detailUrlTemplate.replace('PASIEN_ID', pasienId))
                    .then(response => response.json())
                    .then(data => {
                        const d = data.data;

                        // Pastikan format tanggal/waktu sesuai kebutuhan (gunakan Carbon/JS for Intl)
                        const tglLahirFormatted = d.tgl_lahir ? new Date(d.tgl_lahir).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';
                        const tglKunjunganFormatted = d.tgl_kunjungan ? new Date(d.tgl_kunjungan).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' }) : '-';

                        document.getElementById('detail-antrian').textContent = d.nomor_antrian || '-';
                        document.getElementById('detail-nama').textContent = d.nama_pasien || '-';
                        document.getElementById('detail-hp').innerHTML = createWhatsAppLink(d.nomor_hp || '-'); 
                        document.getElementById('detail-layanan-name').textContent = d.layanan || '-'; 
                        document.getElementById('detail-kategori').textContent = d.kategori_pendaftaran || '-';
                        document.getElementById('detail-tgl-jk').textContent = `${tglLahirFormatted} / ${d.jenis_kelamin || '-'}`;
                        document.getElementById('detail-pendamping').textContent = d.pendamping || '-';
                        document.getElementById('detail-tgl-kunjungan').textContent = tglKunjunganFormatted;
                        document.getElementById('detail-waktu').textContent = d.waktu_kunjungan || '-';
                        document.getElementById('detail-alamat').textContent = d.alamat || '-';
                        document.getElementById('detail-keluhan').textContent = d.keluhan || '-';
                        document.getElementById('detail-status-berkas').textContent = d.status_berkas || 'Menunggu';


                        loading.style.display = 'none';
                        detailTable.style.display = 'table';
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Gagal mengambil detail pasien.');
                        loading.style.display = 'none';
                        detailModal.hide();
                    });
            });
        });

        // ===== MODAL DOKUMEN (VIEW ONLY) - Diambil dari kedua tab =====
        document.querySelectorAll('.btn-dokumen').forEach(button => {
            // ... (Fungsi Modal Dokumen tetap sama) ...
            button.addEventListener('click', function() {
                const buktiPath = this.dataset.bukti;
                const sktmPath = this.dataset.sktm;
                
                const buktiFrame = document.getElementById('bukti-pembayaran-frame');
                const buktiNotFound = document.getElementById('bukti-not-found');
                
                if (buktiPath) {
                    buktiFrame.src = buktiPath;
                    buktiFrame.style.display = 'block';
                    buktiNotFound.style.display = 'none';
                } else {
                    buktiFrame.src = '';
                    buktiFrame.style.display = 'none';
                    buktiNotFound.style.display = 'block';
                }

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
    
        // ===== MODAL UBAH STATUS BERKAS (SINGLE) - Diambil dari kedua tab =====
        document.querySelectorAll('.btn-status-ubah').forEach(button => {
            button.addEventListener('click', function() {
                currentPasienId = this.dataset.id;
                const currentStatus = this.dataset.currentStatus || 'Menunggu';
                // Ambil nama pasien (kolom kedua setelah No)
                const pasienName = this.closest('tr').querySelector('td:nth-child(2)').textContent; 
                
                document.getElementById('status-pasien-nama').textContent = pasienName;
                document.getElementById('status_berkas_select').value = currentStatus;
                
                const form = document.getElementById('status-update-form');
                form.action = updateStatusUrlTemplate.replace('PASIEN_ID', currentPasienId);
                
                statusModal.show();
            });
        });

        // Handle Submit Form Status (Single) - (Tetap sama)
        document.getElementById('status-update-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const form = e.target;
            const submitButton = document.getElementById('btn-save-status');
            const loadingSpinner = document.getElementById('status-loading-spinner');
            
            submitButton.disabled = true;
            loadingSpinner.style.display = 'block';
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Memproses...';


            fetch(form.action, {
                method: 'POST', 
                body: new FormData(form),
                headers: {
                    // Pastikan token CSRF sudah ada di form dan header X-Requested-With untuk AJAX
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw err; });
                }
                return response.json();
            })
            .then(data => {
                if(data.status === 'success') {
                    statusModal.hide();
                    location.reload(); 
                } else {
                    alert('Gagal memperbarui status: ' + (data.message || 'Terjadi kesalahan.'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memperbarui status. Periksa konsol untuk detail.');
            })
            .finally(() => {
                submitButton.disabled = false;
                loadingSpinner.style.display = 'none';
                submitButton.innerHTML = '<i class="fa-solid fa-save me-1"></i> Simpan Perubahan';
            });
        });
    });
</script>

@endsection


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
        display: none; /* Default hidden */
        align-items: center;
        justify-content: center;
        border-radius: 0.5rem;
        transition: opacity 0.3s ease-in-out;
    }
    
    /* Responsif untuk Filter Group */
    @media (max-width: 991.98px) {
        .filter-group .col-md-3, .filter-group .col-md-2 {
            flex: 0 0 50%;
            max-width: 50%;
        }
    }
    @media (max-width: 575.98px) {
        .filter-group .col-md-3, .filter-group .col-md-2 {
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
            <form method="GET" action="{{ route('admin.data-pasien') }}" id="filter-form-{{ $key }}" class="filter-group row mb-4 align-items-end">
                <input type="hidden" name="tab" value="{{ $key }}">
                
                {{-- FILTER TANGGAL (Hanya untuk Jadwal Mendatang) --}}
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

                {{-- FILTER STATUS BERKAS (Untuk Hari Ini & Mendatang) --}}
                <div class="col-md-3 col-sm-6 mb-2">
                    <label for="filter_status_berkas_{{ $key }}" class="form-label">Status Berkas</label>
                    <select name="status_berkas" id="filter_status_berkas_{{ $key }}" class="form-select">
                        <option value="">-- Semua Status Berkas --</option>
                        <option value="Belum Diverifikasi" {{ $currentFilterStatusBerkas == 'Belum Diverifikasi' ? 'selected' : '' }}>Belum Diverifikasi</option>
                        <option value="Sudah Diverifikasi" {{ $currentFilterStatusBerkas == 'Sudah Diverifikasi' ? 'selected' : '' }}>Sudah Diverifikasi</option>
                    </select>
                </div>
                
                {{-- FILTER STATUS PEMERIKSAAN (Untuk Hari Ini & Mendatang) --}}
                <div class="col-md-3 col-sm-6 mb-2">
                    <label for="filter_status_pemeriksaan_{{ $key }}" class="form-label">Status Pemeriksaan</label>
                    <select name="status_pemeriksaan" id="filter_status_pemeriksaan_{{ $key }}" class="form-select">
                        <option value="">-- Semua Status --</option>
                        <option value="Belum Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Belum Diperiksa' ? 'selected' : '' }}>Belum Diperiksa</option>
                        <option value="Sudah Diperiksa" {{ $currentFilterStatusPemeriksaan == 'Sudah Diperiksa' ? 'selected' : '' }}>Sudah Diperiksa</option>
                    </select>
                </div>
                
                {{-- FILTER NAMA PASIEN (Untuk Hari Ini & Mendatang) --}}
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
                                        {{-- Tombol Aksi (Hanya Ikon) --}}
                                        <button class="btn btn-sm btn-info text-white me-1 btn-detail btn-action-icon" data-id="{{ $pasien->id }}" title="Detail Pasien">
                                            <i class="fa-solid fa-file-invoice"></i>
                                        </button>
                                        
                                        {{-- Perbaikan Ikon Berkas: Menggunakan fa-file-shield untuk verifikasi atau fa-file-circle-check --}}
                                        <button class="btn btn-sm btn-warning me-1 btn-status-berkas btn-action-icon" data-id="{{ $pasien->id }}" data-current-status="{{ $pasien->status_berkas }}" title="Ubah Status Berkas">
                                            <i class="fa-solid fa-file-circle-check"></i>
                                        </button>

                                        <button class="btn btn-sm btn-secondary btn-dokumen btn-action-icon" data-bukti="{{ asset('public/storage/' . $pasien->bukti_pembayaran) }}" data-sktm="{{ $pasien->sktm ? asset('public/storage/' . $pasien->sktm) : '' }}" title="Lihat Dokumen">
                                            <i class="fa-solid fa-cloud-arrow-down"></i>
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
{{-- MODALS (TETAP SAMA) --}}
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
                        {{-- BARU: Baris untuk Dokter --}}
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
{{-- SCRIPT INTERAKSI MODAL & AJAX (URL FIX) --}}
{{-- ======================================================= --}}
<script>
    // Fungsi untuk menampilkan loading spinner pada tab tertentu
    function showLoading(key) {
        document.getElementById(`loading-${key}`).style.display = 'flex';
        // Menyembunyikan tabel/konten agar spinner terlihat jelas
        const table = document.querySelector(`#pasien-${key} .table-responsive table`);
        if (table) table.style.opacity = '0.5';
        const alert = document.querySelector(`#pasien-${key} .alert-info`);
        if (alert) alert.style.opacity = '0.5';
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Sembunyikan semua loading saat DOMContentLoaded (sebelum script filter berjalan)
        document.getElementById('loading-today').style.display = 'none';
        document.getElementById('loading-upcoming').style.display = 'none';
        
        // --- SETUP TABS ---
        const btnToday = document.getElementById('btn-today');
        const btnUpcoming = document.getElementById('btn-upcoming');
        const pasienTodayDiv = document.getElementById('pasien-today');
        const pasienUpcomingDiv = document.getElementById('pasien-upcoming');
        
        // Dapatkan parameter URL saat ini
        const urlParams = new URLSearchParams(window.location.search);
        let activeTab = urlParams.get('tab') || 'today';

        // Setup display awal
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

        // Tampilkan/sembunyikan filter form sesuai tab
        document.getElementById('filter-form-today').style.display = activeTab === 'today' ? 'flex' : 'none';
        document.getElementById('filter-form-upcoming').style.display = activeTab === 'upcoming' ? 'flex' : 'none';


        function updateTabs(activeKey) {
            // Ambil semua parameter filter kecuali 'tab'
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.delete('tab');
            const queryString = currentParams.toString();
            const newUrl = `{{ route('admin.data-pasien') }}?tab=${activeKey}` + (queryString ? `&${queryString}` : '');

            // Tambahkan animasi loading sebelum pindah ke URL baru
            showLoading(activeKey);
            
            // Lakukan navigasi/reload untuk memuat data dengan filter baru
            window.location.href = newUrl;
        }
        
        // Event Listener untuk tombol Hari Ini
        btnToday.addEventListener('click', () => {
             // Cek apakah ada filter di URL, jika ya, paksa reload dengan tab=today
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('tab') !== 'today') {
                updateTabs('today');
            }
        });
        
        // Event Listener untuk tombol Jadwal Mendatang
        btnUpcoming.addEventListener('click', () => {
             // Cek apakah ada filter di URL, jika ya, paksa reload dengan tab=upcoming
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
        // ... (Kode JavaScript sebelumnya)

// --- MODAL 1: LIHAT DETAIL (AJAX) ---
        const detailModal = new bootstrap.Modal(document.getElementById('detailPasienModal'));
        const detailUrlTemplate = `{{ route('admin.pasien.detail', ['id' => 'PASIEN_ID']) }}`; // Asumsi route sudah benar

        document.querySelectorAll('.btn-detail').forEach(button => {
            button.addEventListener('click', function() {
                // ... (Kode setup dan loading)
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
                        
                        // Mengambil waktu kunjungan yang sudah diformat di Controller
                        document.getElementById('detail-waktu').textContent = d.waktu_kunjungan; 

                        // Mengambil nama layanan (string) yang sudah diformat di Controller
                        document.getElementById('detail-layanan').textContent = d.layanan; 

                        // BARU: Mengisi Nama Dokter
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
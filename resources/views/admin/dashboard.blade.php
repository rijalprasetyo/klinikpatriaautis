@extends('layouts.admin-sidebar')

@section('content')

<style>
    /* Global Styles (Ditingkatkan untuk estetika) */
    .card { 
        border-radius: 12px; 
        transition: transform 0.2s ease, box-shadow 0.2s ease; 
        border: none; /* Menghapus border default */
    }
    .card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1)!important;
    }
    .border-left-warning { border-left: 0.25rem solid #ffc107!important; }
    .border-left-danger { border-left: 0.25rem solid #dc3545!important; }
    .border-left-success { border-left: 0.25rem solid #28a745!important; }
    .text-xs { font-size: 0.75rem; letter-spacing: 0.05em; }
    .nav-tabs .nav-link.active { background-color: #f0f3f5; color: #007bff; border-color: #dee2e6 #dee2e6 #f0f3f5; }
    .card-body .fa-2x { opacity: 0.6; } /* Ikon di card ringkasan */
    
    /* --- CSS BARU UNTUK MOBILE CARD VIEW --- */
    .mobile-card-jadwal {
        display: none; /* Default hidden */
        background: #fff;
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        padding: 1rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    }

    .mobile-card-header-jadwal {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px dashed #e9ecef;
    }

    .mobile-card-antrian {
        font-size: 1.1rem;
        font-weight: bold;
        color: #007bff;
    }
    
    .mobile-card-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.25rem;
        font-size: 0.9rem;
    }

    .mobile-card-label {
        font-weight: 600;
        color: #343a40;
        min-width: 90px;
    }
    /* --- END CSS BARU --- */

    /* Responsive Logic */
    @media (max-width: 767.98px) {
        
        /* Sembunyikan tabel di mobile */
        .table-responsive table {
            display: none;
        }

        /* Tampilkan cards jadwal di mobile */
        .mobile-card-jadwal {
            display: block;
        }

        /* Nav Tabs: Jadikan satu per baris agar tidak berdesakan */
        .nav-tabs {
            flex-direction: column;
        }
        .nav-tabs .nav-item {
            width: 100%;
            margin-bottom: 5px;
        }
        .nav-tabs .nav-link {
            text-align: center;
        }
        
        /* Hapus styling tabel responsive yang tidak diperlukan di mobile */
        .table-responsive {
            border: none;
            box-shadow: none;
            overflow-x: hidden;
        }
    }
    
    @media (min-width: 768px) {
        /* Sembunyikan mobile cards di desktop */
        .mobile-card-jadwal {
            display: none;
        }
        /* Pastikan tabel desktop tampil */
        .table-responsive table {
            display: table;
        }
    }
</style>

<div class="container-fluid">
    <h2 class="mb-3 fw-semibold"><i class="fa-solid fa-chart-line me-2"></i> Dashboard Admin</h2>
    <p class="text-muted mb-4">Selamat datang, <strong>{{ $admin->nama }}</strong>. Berikut adalah ringkasan data pasien.</p>

    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-warning text-uppercase fw-bold mb-1">Belum Diperiksa (Hari Ini)</div>
                        <div class="h5 fw-bold text-dark">{{ $belumDiperiksaHariIni }} Pasien</div>
                    </div>
                    <i class="fa-solid fa-clock fa-2x text-warning opacity-75"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="card border-left-danger shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-danger text-uppercase fw-bold mb-1">Berkas Belum Diverifikasi</div>
                        <div class="h5 fw-bold text-dark">{{ $berkasBelumDiverifikasi }} Berkas</div>
                    </div>
                    <i class="fa-solid fa-file-excel fa-2x text-danger opacity-75"></i>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="card border-left-success shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-success text-uppercase fw-bold mb-1">Selesai Diperiksa (Hari Ini)</div>
                        <div class="h5 fw-bold text-dark">{{ $selesaiDiperiksaHariIni }} Pasien</div>
                    </div>
                    <i class="fa-solid fa-check-circle fa-2x text-success opacity-75"></i>
                </div>
            </div>
        </div>
    </div>
    
    <hr>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 fw-bold text-primary"><i class="fa-solid fa-calendar-alt me-2"></i> Jadwal Pasien Kunjungan</h5>
        </div>
        <div class="card-body">

            <ul class="nav nav-tabs mb-4" id="jadwalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="kemarin-tab" data-bs-toggle="tab" data-bs-target="#kemarin" type="button" role="tab" aria-controls="kemarin" aria-selected="false">
                        Kemarin ({{ $yesterday->isoFormat('D MMM') }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="hari-ini-tab" data-bs-toggle="tab" data-bs-target="#hari-ini" type="button" role="tab" aria-controls="hari-ini" aria-selected="true">
                        Hari Ini ({{ $today->isoFormat('D MMM') }})
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="besok-tab" data-bs-toggle="tab" data-bs-target="#besok" type="button" role="tab" aria-controls="besok" aria-selected="false">
                        Besok ({{ $tomorrow->isoFormat('D MMM') }})
                    </button>
                </li>
            </ul>

            <div class="tab-content" id="jadwalTabsContent">
                
                {{-- TAB KEMARIN --}}
                <div class="tab-pane fade" id="kemarin" role="tabpanel" aria-labelledby="kemarin-tab">
                    <div class="table-responsive">
                        @if ($pasienKemarin->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fa-solid fa-calendar-times me-2"></i> Tidak ada jadwal pasien untuk tanggal ini.
                            </div>
                        @else
                            {{-- DESKTOP TABLE VIEW --}}
                            <table class="table table-bordered table-striped align-middle" style="min-width: 600px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 35%;">Nama Pasien</th>
                                        <th style="width: 20%;">Kategori</th>
                                        <th style="width: 40%;">Waktu Kunjungan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pasienKemarin as $index => $pasien)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $pasien->nama_pasien }}</td>
                                            <td>{{ $pasien->kategori_pendaftaran }}</td>
                                            <td>
                                                <i class="fa-solid fa-calendar-day me-1 text-primary"></i> 
                                                {{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('dddd, D MMMM YYYY') }}
                                                
                                                <i class="fa-solid fa-clock me-1 text-success"></i> 
                                                {{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            {{-- MOBILE CARD VIEW --}}
                            @foreach ($pasienKemarin as $index => $pasien)
                                <div class="mobile-card-jadwal">
                                    <div class="mobile-card-header-jadwal">
                                        <span class="mobile-card-antrian">#{{ $index + 1 }}</span>
                                        <span class="badge bg-secondary">{{ $pasien->kategori_pendaftaran }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-user me-1"></i> Nama:</span>
                                        <span class="fw-semibold">{{ $pasien->nama_pasien }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-calendar-day me-1"></i> Tanggal:</span>
                                        <span>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-clock me-1"></i> Waktu:</span>
                                        <span>{{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}</span>
                                    </div>
                                </div>
                            @endforeach
                            
                        @endif
                    </div>
                </div>

                {{-- TAB HARI INI --}}
                <div class="tab-pane fade show active" id="hari-ini" role="tabpanel" aria-labelledby="hari-ini-tab">
                    <div class="table-responsive">
                        @if ($pasienHariIni->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fa-solid fa-calendar-times me-2"></i> Tidak ada jadwal pasien untuk tanggal ini.
                            </div>
                        @else
                            {{-- DESKTOP TABLE VIEW --}}
                            <table class="table table-bordered table-striped align-middle" style="min-width: 600px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 35%;">Nama Pasien</th>
                                        <th style="width: 20%;">Kategori</th>
                                        <th style="width: 40%;">Waktu Kunjungan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pasienHariIni as $index => $pasien)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $pasien->nama_pasien }}</td>
                                            <td>{{ $pasien->kategori_pendaftaran }}</td>
                                            <td>
                                                <i class="fa-solid fa-calendar-day me-1 text-primary"></i> 
                                                {{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('dddd, D MMMM YYYY') }}
                                                
                                                <i class="fa-solid fa-clock me-1 text-success"></i> 
                                                {{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- MOBILE CARD VIEW --}}
                            @foreach ($pasienHariIni as $index => $pasien)
                                <div class="mobile-card-jadwal">
                                    <div class="mobile-card-header-jadwal">
                                        <span class="mobile-card-antrian">#{{ $index + 1 }}</span>
                                        <span class="badge bg-primary">{{ $pasien->kategori_pendaftaran }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-user me-1"></i> Nama:</span>
                                        <span class="fw-semibold">{{ $pasien->nama_pasien }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-calendar-day me-1"></i> Tanggal:</span>
                                        <span>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-clock me-1"></i> Waktu:</span>
                                        <span>{{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}</span>
                                    </div>
                                </div>
                            @endforeach

                        @endif
                    </div>
                </div>

                {{-- TAB BESOK --}}
                <div class="tab-pane fade" id="besok" role="tabpanel" aria-labelledby="besok-tab">
                    <div class="table-responsive">
                        @if ($pasienBesok->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fa-solid fa-calendar-times me-2"></i> Tidak ada jadwal pasien untuk tanggal ini.
                            </div>
                        @else
                            {{-- DESKTOP TABLE VIEW --}}
                            <table class="table table-bordered table-striped align-middle" style="min-width: 600px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th style="width: 35%;">Nama Pasien</th>
                                        <th style="width: 20%;">Kategori</th>
                                        <th style="width: 40%;">Waktu Kunjungan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($pasienBesok as $index => $pasien)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td class="fw-semibold">{{ $pasien->nama_pasien }}</td>
                                            <td>{{ $pasien->kategori_pendaftaran }}</td>
                                            <td>
                                                <i class="fa-solid fa-calendar-day me-1 text-primary"></i> 
                                                {{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('dddd, D MMMM YYYY') }}
                                                
                                                <i class="fa-solid fa-clock me-1 text-success"></i> 
                                                {{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            
                            {{-- MOBILE CARD VIEW --}}
                            @foreach ($pasienBesok as $index => $pasien)
                                <div class="mobile-card-jadwal">
                                    <div class="mobile-card-header-jadwal">
                                        <span class="mobile-card-antrian">#{{ $index + 1 }}</span>
                                        <span class="badge bg-info text-white">{{ $pasien->kategori_pendaftaran }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-user me-1"></i> Nama:</span>
                                        <span class="fw-semibold">{{ $pasien->nama_pasien }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-calendar-day me-1"></i> Tanggal:</span>
                                        <span>{{ \Carbon\Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY') }}</span>
                                    </div>
                                    <div class="mobile-card-row">
                                        <span class="mobile-card-label"><i class="fa-solid fa-clock me-1"></i> Waktu:</span>
                                        <span>{{ $pasien->waktu->jam_mulai ?? '-' }} - {{ $pasien->waktu->jam_selesai ?? '-' }}</span>
                                    </div>
                                </div>
                            @endforeach

                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

{{-- Script untuk memastikan tab aktif saat dimuat (opsional, karena Bootstrap 5 sudah menangani ini dengan '.show .active') --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Cek apakah ada parameter 'tab' di URL dan pindah ke tab tersebut
        const urlParams = new URLSearchParams(window.location.search);
        const activeTabId = urlParams.get('tab'); // e.g., 'kemarin'
        
        // Ambil ID tab dari parameter atau gunakan default 'hari-ini'
        const targetTab = document.getElementById((activeTabId || 'hari-ini') + '-tab');
        
        if (targetTab) {
            new bootstrap.Tab(targetTab).show();
        }

        // Tambahkan logic untuk mengubah URL query param saat berganti tab
        var triggerTabList = [].slice.call(document.querySelectorAll('#jadwalTabs button'))
        triggerTabList.forEach(function (triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl)

            triggerEl.addEventListener('click', function (event) {
                event.preventDefault()
                tabTrigger.show()
                
                // Perbarui URL tanpa reload
                const newTabId = event.target.getAttribute('data-bs-target').substring(1); // e.g., 'kemarin'
                const newUrl = new URL(window.location.href);
                newUrl.searchParams.set('tab', newTabId);
                // Hapus parameter 'tab' jika kembali ke default 'hari-ini'
                if (newTabId === 'hari-ini') {
                    newUrl.searchParams.delete('tab');
                }
                window.history.pushState({}, '', newUrl);
            })
        })
    });
</script>
@endsection
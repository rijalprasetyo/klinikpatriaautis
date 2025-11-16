@extends('layouts.admin-sidebar')

@section('content')

<div class="container-fluid">
    <h2 class="mb-3 fw-semibold"><i class="fa-solid fa-chart-line me-2"></i> Dashboard Admin</h2>
    <p class="text-muted mb-4">Selamat datang, <strong>{{ $admin->nama }}</strong>. Berikut adalah ringkasan data pasien.</p>

    <!-- RINGKASAN DATA -->
    <div class="row g-4 mb-5">
        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs text-warning text-uppercase fw-bold mb-1">Belum Diperiksa (Hari Ini)</div>
                        <div class="h5 fw-bold text-dark">{{ $belumDiperiksaHariIni }} Pasien</div>
                    </div>
                    <i class="fa-solid fa-clock fa-2x text-secondary opacity-50"></i>
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
                    <i class="fa-solid fa-file-excel fa-2x text-secondary opacity-50"></i>
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
                    <i class="fa-solid fa-check-circle fa-2x text-secondary opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <hr>

    <!-- DATA PASIEN HARIAN (BARU - TABBED) -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 fw-bold text-primary"><i class="fa-solid fa-calendar-alt me-2"></i> Jadwal Pasien Kunjungan</h5>
        </div>
        <div class="card-body">

            <!-- Navigasi Tab -->
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

            <!-- Isi Tab -->
            <div class="tab-content" id="jadwalTabsContent">
                
                {{-- TAB KEMARIN --}}
                <div class="tab-pane fade" id="kemarin" role="tabpanel" aria-labelledby="kemarin-tab">
                    <div class="table-responsive">
                        @if ($pasienKemarin->isEmpty())
                            <div class="alert alert-info text-center">
                                <i class="fa-solid fa-calendar-times me-2"></i> Tidak ada jadwal pasien untuk tanggal ini.
                            </div>
                        @else
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
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<style>
    .card { border-radius: 12px; transition: transform 0.2s ease; }
    .card:hover { transform: translateY(-5px); }
    .border-left-warning { border-left: 0.25rem solid #ffc107!important; }
    .border-left-danger { border-left: 0.25rem solid #dc3545!important; }
    .border-left-success { border-left: 0.25rem solid #28a745!important; }
    .text-xs { font-size: 0.75rem; letter-spacing: 0.05em; }
    .nav-tabs .nav-link.active { background-color: #f0f3f5; color: #007bff; border-color: #dee2e6 #dee2e6 #f0f3f5; }
</style>
@endsection
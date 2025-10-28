@extends('layouts.admin-sidebar')

@section('content')
<div class="container-fluid">
    <h2 class="mb-3 fw-semibold">Dashboard Admin</h2>
    <p class="text-muted mb-4">Selamat datang, <strong>{{ $admin->nama }}</strong>. Berikut adalah ringkasan data pasien hari ini.</p>

    <div class="row g-4">
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
</div>

<style>
    .card { border-radius: 12px; transition: transform 0.2s ease; }
    .card:hover { transform: translateY(-5px); }
    .border-left-warning { border-left: 0.25rem solid #ffc107!important; }
    .border-left-danger { border-left: 0.25rem solid #dc3545!important; }
    .border-left-success { border-left: 0.25rem solid #28a745!important; }
    .text-xs { font-size: 0.75rem; letter-spacing: 0.05em; }
</style>
@endsection

@extends('layouts.dokter-sidebar')

@section('title', 'Dashboard Dokter - Klinik Patria')

@section('content')
<div class="container-fluid py-3">
    <h2 class="mb-4 fw-bold"><i class="fa-solid fa-chart-line me-2"></i> Dashboard Dokter</h2>

    <div class="row g-4">
        <!-- Total Pasien Hari Ini -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 text-center bg-primary text-white rounded-4">
                <div class="card-body">
                    <i class="fa-solid fa-users fa-2x mb-2"></i>
                    <h5>Total Pasien Hari Ini</h5>
                    <h2 class="fw-bold">{{ $totalPasienHariIni }}</h2>
                </div>
            </div>
        </div>

        <!-- Belum Diperiksa -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 text-center bg-warning text-dark rounded-4">
                <div class="card-body">
                    <i class="fa-solid fa-user-clock fa-2x mb-2"></i>
                    <h5>Belum Diperiksa</h5>
                    <h2 class="fw-bold">{{ $totalBelumDiperiksa }}</h2>
                </div>
            </div>
        </div>

        <!-- Selesai Diperiksa -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 text-center bg-success text-white rounded-4">
                <div class="card-body">
                    <i class="fa-solid fa-user-check fa-2x mb-2"></i>
                    <h5>Selesai Diperiksa</h5>
                    <h2 class="fw-bold">{{ $totalSelesaiDiperiksa }}</h2>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

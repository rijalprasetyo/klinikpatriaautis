@extends('layouts.dokter-sidebar')

@section('title', 'Dashboard Dokter - Klinik Patria')

@section('content')
<div class="container-fluid py-3">
    <h2 class="mb-4 fw-bold"><i class="fa-solid fa-chart-line me-2"></i> Dashboard Dokter</h2>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 text-center bg-primary text-white rounded-4">
                <div class="card-body">
                    <i class="fa-solid fa-users fa-2x mb-2"></i>
                    <h5>Total Pasien Hari Ini</h5>
                    <h2 class="fw-bold">{{ $totalPasienHariIni }}</h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3 text-center bg-warning text-dark rounded-4">
                <div class="card-body">
                    <i class="fa-solid fa-user-clock fa-2x mb-2"></i>
                    <h5>Belum Diperiksa</h5>
                    <h2 class="fw-bold">{{ $totalBelumDiperiksa }}</h2>
                </div>
            </div>
        </div>

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
    
    <hr class="my-4">

    <div class="alert alert-danger p-3 shadow-sm rounded-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fa-solid fa-triangle-exclamation fa-2x me-3 text-danger flex-shrink-0"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1 text-danger">PERHATIAN PENTING</h5>
                <p class="mb-0 small text-dark">
                    Pastikan semua pasien sudah diperiksa, jangan sampai ada yang terlewatkan yang melebihi tanggal kunjungannya, selalu cek Menu Riwayat Pasien.
                </p>
            </div>
        </div>
    </div>
    </div>
@endsection
@extends('layouts.admin-sidebar')

@section('title', 'Data Master Jenis Pelayanan')

@section('content')

<style>
    /* Variabel Warna */
    :root {
        --primary-blue: #007bff;
        --secondary-blue: #0056b3;
        --text-dark: #343a40;
        --bg-light: #f8f9fa;
        --border-light: #dee2e6;
        --danger-red: #dc3545; /* Definisi warna merah */
    }

    /* Penyesuaian Teks/Header */
    .h3.text-gray-800 {
        color: var(--secondary-blue) !important;
    }

    /* Card Styling */
    .card-header.bg-primary {
        background-color: var(--primary-blue) !important;
    }

    /* Button Styling */
    .btn-light {
        color: var(--primary-blue) !important;
        border-color: var(--primary-blue) !important;
    }
    /* >>>>>> PERBAIKAN WARNA TOMBOL HAPUS <<<<<< */
    .btn-danger {
        background-color: var(--danger-red) !important;
        border-color: var(--danger-red) !important;
    }
    .btn-danger:hover {
        background-color: #c82333 !important; /* Warna merah sedikit lebih gelap saat hover */
        border-color: #bd2130 !important;
    }


    /* Table Styling */
    .table-responsive {
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
        overflow: hidden;
    }
    .table-primary {
        background-color: var(--primary-blue) !important;
        color: white;
    }
    
    /* Tombol Hapus */
    .btn-action-icon {
        padding: 0.3rem 0.5rem;
        width: auto;
        height: auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* ======================================= */
    /* ====== MOBILE CARD STYLES ====== */
    /* ======================================= */
    .card-mobile-list {
        display: none; /* Sembunyi di desktop */
        padding: 1rem 0;
    }

    .pelayanan-card {
        border: 1px solid var(--border-light);
        border-radius: 0.7rem;
        margin-bottom: 15px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        background-color: white;
    }

    .card-header-mobile {
        padding: 10px 15px;
        background-color: var(--primary-blue);
        color: white;
        font-weight: bold;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-body-mobile {
        padding: 15px;
    }

    .card-item {
        display: flex;
        justify-content: space-between;
        margin-bottom: 8px;
        font-size: 0.9em;
        border-bottom: 1px dashed var(--border-light);
        padding-bottom: 5px;
    }

    .card-item-label {
        font-weight: 500;
        color: var(--secondary-blue);
        width: 35%;
    }

    .card-item-value {
        text-align: right;
        width: 65%;
    }

    .card-actions-mobile {
        padding: 10px 15px;
        border-top: 1px solid var(--border-light);
        background-color: var(--bg-light);
        text-align: right;
    }

    /* ======================================= */
    /* ====== RESPONSIVE LAYOUT & MODAL ====== */
    /* ======================================= */
    @media (max-width: 768px) {
        /* Sembunyikan Tabel di layar kecil */
        .table-responsive {
            display: none;
        }
        /* Tampilkan Card List di layar kecil */
        .card-mobile-list {
            display: block;
        }

        /* Atur lebar Modal agar responsif di mobile */
        .modal-dialog {
            margin: 0.5rem;
        }
        .modal-dialog.modal-dialog-centered {
            min-height: calc(100vh - 1rem);
        }
    }
</style>

<div class="container-fluid py-3">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold"><i class="fa-solid fa-server me-2"></i>Data Master Jenis Pelayanan</h1>
    </div>

    {{-- ALERTS --}}
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h5 class="alert-heading"><i class="fa-solid fa-triangle-exclamation me-2"></i>Terjadi Kesalahan!</h5>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- CARD TABEL (Desktop View) --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-semibold"><i class="fa-solid fa-list me-2"></i>Daftar Jenis Pelayanan</h6>
            <button class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahPelayananModal">
                <i class="fas fa-plus fa-sm me-1 text-primary"></i> Tambah Data
            </button>
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-bordered table-hover align-middle mb-0" id="dataTable" width="100%">
                    <thead class="table-primary text-center align-middle">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Pelayanan</th>
                            <th style="width: 180px;">Ikon</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jenisPelayanan as $index => $data)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td class="fw-semibold">{{ $data->pelayanan }}</td>
                                <td class="text-center">
                                    @if ($data->icon_pelayanan && file_exists(public_path('assets/' . $data->icon_pelayanan)))
                                        <img src="{{ asset('assets/' . $data->icon_pelayanan) }}"
                                            alt="{{ $data->pelayanan }}"
                                            class="rounded-circle shadow-sm border border-2 border-primary"
                                            style="width: 55px; height: 55px; object-fit: cover;">
                                    @else
                                        <span class="text-muted fst-italic">Tidak Ada Ikon</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.pelayanan.destroy', $data->id) }}"
                                        method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus data {{ $data->pelayanan }}?')">
                                            <i class="fa-solid fa-trash-can me-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada data jenis pelayanan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    {{-- CARD LIST (Mobile View) --}}
    <div class="card-mobile-list d-md-none">
        @forelse ($jenisPelayanan as $index => $data)
            <div class="pelayanan-card">
                <div class="card-header-mobile">
                    <span class="fw-bold">No. {{ $index + 1 }}</span>
                    <span><i class="fa-solid fa-list-check me-1"></i> Jenis Pelayanan</span>
                </div>
                <div class="card-body-mobile">
                    <div class="card-item">
                        <span class="card-item-label">Nama Pelayanan</span>
                        <span class="card-item-value fw-semibold text-break">{{ $data->pelayanan }}</span>
                    </div>
                    <div class="card-item" style="border-bottom: none !important;">
                        <span class="card-item-label">Ikon</span>
                        <span class="card-item-value">
                            @if ($data->icon_pelayanan && file_exists(public_path('assets/' . $data->icon_pelayanan)))
                                <img src="{{ asset('assets/' . $data->icon_pelayanan) }}"
                                    alt="{{ $data->pelayanan }}"
                                    class="rounded-circle shadow-sm border border-2 border-primary"
                                    style="width: 55px; height: 55px; object-fit: cover;">
                            @else
                                <span class="text-muted fst-italic">N/A</span>
                            @endif
                        </span>
                    </div>
                </div>
                <div class="card-actions-mobile">
                    <form action="{{ route('admin.pelayanan.destroy', $data->id) }}"
                        method="POST" class="d-inline delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm"
                            onclick="return confirm('Yakin ingin menghapus data {{ $data->pelayanan }}?')">
                            <i class="fa-solid fa-trash-can me-1"></i> Hapus
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="alert alert-info text-center">Belum ada data jenis pelayanan.</div>
        @endforelse
    </div>

</div>

{{-- MODAL TAMBAH DATA (RESPONSIVE) --}}
<div class="modal fade" id="tambahPelayananModal" tabindex="-1" aria-labelledby="tambahPelayananModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tambahPelayananModalLabel">
                    <i class="fa-solid fa-plus-circle me-2"></i>Tambah Jenis Pelayanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.pelayanan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="pelayanan" class="form-label fw-semibold">Nama Pelayanan</label>
                        <input type="text" class="form-control" id="pelayanan" name="pelayanan" required>
                    </div>
                    <div class="mb-3">
                        <label for="icon_pelayanan" class="form-label fw-semibold">Ikon Pelayanan (Opsional)</label>
                        <input type="file" class="form-control" id="icon_pelayanan" name="icon_pelayanan" accept="image/*">
                        <small class="text-muted">Max 2MB (jpeg, png, jpg, gif, svg)</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT HANYA DIPERLUKAN UNTUK LOGIKA ERROR MODAL TAMBAH DATA SAJA --}}
@if ($errors->any() && !session('showEditModal'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tampilkan modal Tambah Data jika ada error validasi saat submit Tambah Data
    var tambahModal = new bootstrap.Modal(document.getElementById('tambahPelayananModal'));
    tambahModal.show();
});
</script>
@endpush
@endif
@endsection
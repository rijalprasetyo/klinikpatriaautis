@extends('layouts.admin-sidebar')

@section('title', 'Data Master Jenis Pelayanan')

@section('content')
<div class="container-fluid py-3">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Data Master Jenis Pelayanan</h1>
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

    {{-- CARD TABEL --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 fw-semibold"><i class="fa-solid fa-list me-2"></i>Daftar Jenis Pelayanan</h6>
            <button class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahPelayananModal">
                <i class="fas fa-plus fa-sm me-1 text-primary"></i> Tambah Data
            </button>
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="dataTable" width="100%">
                    <thead class="table-primary text-center align-middle">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Nama Pelayanan</th>
                            <th style="width: 180px;">Ikon</th>
                            <th style="width: 120px;">Aksi</th> {{-- Lebar Aksi dikurangi karena hanya ada 1 tombol --}}
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
                                    {{-- Tombol Edit Dihapus --}}
                                    <form action="{{ route('admin.pelayanan.destroy', $data->id) }}"
                                          method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus data ini?')">
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
</div>

{{-- MODAL TAMBAH DATA (TETAP) --}}
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

{{-- MODAL EDIT DATA DIHAPUS TOTAL --}}

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
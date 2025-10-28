@extends('layouts.admin-sidebar')

@section('title', 'Data Master Jam Pelayanan')

@section('content')
<div class="container-fluid py-3">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Data Master Jam Pelayanan</h1>
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
            <h6 class="m-0 fw-semibold"><i class="fa-solid fa-clock me-2"></i>Daftar Slot Jam Pelayanan</h6>
            <button class="btn btn-light btn-sm fw-semibold" data-bs-toggle="modal" data-bs-target="#tambahJamModal">
                <i class="fas fa-plus fa-sm me-1 text-primary"></i> Tambah Slot Jam
            </button>
        </div>
        <div class="card-body bg-light">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="dataTable" width="100%">
                    <thead class="table-primary text-center align-middle">
                        <tr>
                            <th style="width: 60px;">No</th>
                            <th>Jam Mulai</th>
                            <th>Jam Selesai</th>
                            <th style="width: 120px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($jamPelayanan as $index => $data)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                <td>{{ $data->jam_mulai }}</td>
                                <td>{{ $data->jam_selesai }}</td>
                                <td class="text-center">
                                    <form action="{{ route('admin.jam-pelayanan.destroy', $data->id) }}"
                                          method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm"
                                            onclick="return confirm('Yakin ingin menghapus slot jam {{ $data->jam_mulai }} - {{ $data->jam_selesai }}?')">
                                            <i class="fa-solid fa-trash-can me-1"></i>Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Belum ada slot jam pelayanan yang tersedia.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH DATA --}}
<div class="modal fade" id="tambahJamModal" tabindex="-1" aria-labelledby="tambahJamModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="tambahJamModalLabel">
                    <i class="fa-solid fa-plus-circle me-2"></i>Tambah Slot Jam Pelayanan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('admin.jam-pelayanan.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="jam_mulai" class="form-label fw-semibold">Jam Mulai (HH:MM)</label>
                        {{-- type="time" akan memicu dialog box jam 24 jam di banyak browser/sistem --}}
                        <input type="time" class="form-control" id="jam_mulai" name="jam_mulai" value="{{ old('jam_mulai') }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="jam_selesai" class="form-label fw-semibold">Jam Selesai (HH:MM)</label>
                        <input type="time" class="form-control" id="jam_selesai" name="jam_selesai" value="{{ old('jam_selesai') }}" required>
                    </div>
                    <small class="text-muted">Gunakan format 24 jam, misal: 08:00. Jam selesai harus setelah jam mulai.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT: Untuk menampilkan kembali modal jika ada error validasi saat submit --}}
@if ($errors->any() && session('showTambahModal'))
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Tampilkan modal Tambah Data jika ada error validasi saat submit Tambah Data
    var tambahModal = new bootstrap.Modal(document.getElementById('tambahJamModal'));
    tambahModal.show();
});
</script>
@endpush
@endif
@endsection
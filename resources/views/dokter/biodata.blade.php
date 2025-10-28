@extends('layouts.dokter-sidebar')

@section('content')

<div class="container-fluid py-4">
    
    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-check me-3 fs-5"></i>
                <div>
                    <strong>Berhasil!</strong> {{ session('success') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="fa-solid fa-circle-xmark me-3 fs-5"></i>
                <div>
                    <strong>Gagal!</strong> {{ session('error') }}
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <div class="d-flex align-items-start">
                <i class="fa-solid fa-triangle-exclamation me-3 fs-5 mt-1"></i>
                <div class="flex-grow-1">
                    <strong>Gagal Validasi!</strong> Mohon periksa kembali inputan Anda.
                    <ul class="mb-0 mt-2 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold mb-1">Biodata Dokter</h2>
            <p class="text-muted mb-0">Kelola informasi profil dan keamanan akun Anda</p>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="row g-4">
        
        {{-- Card Informasi Profil --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fa-solid fa-user-md text-primary fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Informasi Profil</h5>
                            <small class="text-muted">Data diri dan kontak Anda</small>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">Nama Lengkap</small>
                                <strong class="text-dark">{{ $dokter->nama_dokter }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">Username</small>
                                <strong class="text-dark">{{ $dokter->username }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">Email</small>
                                <strong class="text-dark">{{ $dokter->email }}</strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">No. HP</small>
                                <strong class="text-dark">{{ $dokter->no_hp ?? '-' }}</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-light rounded">
                                <small class="text-muted d-block mb-1">Alamat</small>
                                <strong class="text-dark">{{ $dokter->alamat ?? '-' }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#editBiodataModal">
                            <i class="fa-solid fa-pen-to-square me-2"></i> Ubah Profil
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card Keamanan --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fa-solid fa-shield-halved text-danger fs-4"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">Keamanan</h5>
                            <small class="text-muted">Kelola password akun</small>
                        </div>
                    </div>

                    <div class="alert alert-info border-0 bg-info bg-opacity-10 mb-4">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <small>Ubah password secara berkala untuk keamanan akun Anda</small>
                    </div>

                    <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#editPasswordModal">
                        <i class="fa-solid fa-key me-2"></i> Ubah Password
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL UBAH BIODATA --}}
<div class="modal fade" id="editBiodataModal" tabindex="-1" aria-labelledby="editBiodataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-primary text-white">
                <h5 class="modal-title fw-bold" id="editBiodataModalLabel">
                    <i class="fa-solid fa-pen-to-square me-2"></i> Ubah Profil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dokter.biodata.update') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="nama_dokter" class="form-label fw-semibold">
                                Nama Lengkap <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('nama_dokter') is-invalid @enderror" id="nama_dokter" name="nama_dokter" value="{{ old('nama_dokter', $dokter->nama_dokter) }}" required>
                            @error('nama_dokter')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="username" class="form-label fw-semibold">
                                Username <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $dokter->username) }}" required>
                            @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label fw-semibold">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $dokter->email) }}" required>
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
                            <input type="text" class="form-control @error('no_hp') is-invalid @enderror" id="no_hp" name="no_hp" value="{{ old('no_hp', $dokter->no_hp) }}" placeholder="08xxxxxxxxxx">
                            @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label for="alamat" class="form-label fw-semibold">Alamat</label>
                            <textarea class="form-control @error('alamat') is-invalid @enderror" id="alamat" name="alamat" rows="3" placeholder="Masukkan alamat lengkap">{{ old('alamat', $dokter->alamat) }}</textarea>
                            @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save me-2"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL UBAH PASSWORD --}}
<div class="modal fade" id="editPasswordModal" tabindex="-1" aria-labelledby="editPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0 bg-danger text-white">
                <h5 class="modal-title fw-bold" id="editPasswordModalLabel">
                    <i class="fa-solid fa-lock me-2"></i> Ubah Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('dokter.password.update') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-warning border-0 bg-warning bg-opacity-10 mb-4">
                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                        <small>Konfirmasi username dan email untuk verifikasi identitas Anda</small>
                    </div>

                    {{-- Konfirmasi Akun --}}
                    <div class="mb-3">
                        <label for="username_konfirmasi" class="form-label fw-semibold">
                            Username <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-user text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 @error('username_konfirmasi') is-invalid @enderror" id="username_konfirmasi" name="username_konfirmasi" value="{{ old('username_konfirmasi') }}" required>
                            @error('username_konfirmasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-4">
                        <label for="email_konfirmasi" class="form-label fw-semibold">
                            Email <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-envelope text-muted"></i>
                            </span>
                            <input type="email" class="form-control border-start-0 @error('email_konfirmasi') is-invalid @enderror" id="email_konfirmasi" name="email_konfirmasi" value="{{ old('email_konfirmasi') }}" required>
                            @error('email_konfirmasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    {{-- Password Baru --}}
                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            Password Baru <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0 @error('password') is-invalid @enderror" id="password" name="password" required>
                            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <small class="text-muted">Minimal 8 karakter</small>
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label fw-semibold">
                            Konfirmasi Password <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-lock text-muted"></i>
                            </span>
                            <input type="password" class="form-control border-start-0" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fa-solid fa-key me-2"></i> Ubah Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    
    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
    }

    .input-group-text {
        border-right: 0;
    }

    .input-group .form-control {
        border-left: 0;
    }

    .input-group .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .input-group .form-control:focus + .input-group-text,
    .input-group-text + .form-control:focus {
        border-color: #86b7fe;
    }

    .bg-light {
        background-color: #f8f9fa !important;
    }

    .modal-content {
        border-radius: 0.75rem;
        overflow: hidden;
    }

    .alert {
        border-radius: 0.5rem;
    }
</style>
@endpush

@push('scripts')
{{-- Script untuk menampilkan modal error secara otomatis setelah validasi gagal --}}
@if ($errors->hasAny(['nama_dokter', 'username', 'email', 'no_hp', 'alamat']))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editBiodataModal = new bootstrap.Modal(document.getElementById('editBiodataModal'));
        editBiodataModal.show();
    });
</script>
@endif

@if ($errors->hasAny(['username_konfirmasi', 'email_konfirmasi', 'password'])) 
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editPasswordModal = new bootstrap.Modal(document.getElementById('editPasswordModal'));
        editPasswordModal.show();
    });
</script>
@endif
@endpush
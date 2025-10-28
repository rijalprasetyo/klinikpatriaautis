@extends('layouts.user-sidebar') 
{{-- Pastikan 'layouts.user-sidebar' adalah nama file layout utama Anda --}}

@section('content')

{{-- CSS KHUSUS UNTUK TAMPILAN BIODATA MODERN (TETAP) --}}
<style>
    :root {
        --primary-color: #007bff; /* Biru Profesional */
        --background-light: #f8f9fa;
        --text-dark: #343a40;
        --border-color: #e9ecef;
    }

    /* Gaya Card Modern */
    .card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .card-header {
        border-radius: 12px 12px 0 0 !important;
        background-color: var(--primary-color) !important;
        padding: 15px 20px;
    }
    
    .card-header h4 {
        font-weight: 600;
    }

    .card-body {
        padding: 30px;
    }

    /* Gaya Tabel */
    .table-bordered th, .table-bordered td {
        border-color: var(--border-color) !important;
    }

    .table-striped > tbody > tr:nth-of-type(odd) > * {
        background-color: #f4f6f8;
    }

    .table th {
        font-weight: 500;
        color: var(--text-dark);
    }
    
    /* Avatar dan Info User */
    .rounded-circle {
        border: 4px solid var(--border-color);
    }
    
    /* Tombol Edit */
    .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
        color: var(--text-dark);
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s;
    }
    
    .btn-warning:hover {
        background-color: #e0a800;
        border-color: #e0a800;
        color: white;
    }

    /* Modal */
    .modal-content {
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    }
    
    /* Responsif (MODIFIKASI: Memastikan elemen menumpuk dan responsif) */
    @media (max-width: 768px) {
        .card-body {
            padding: 15px;
        }
        
        /* Memastikan kolom gambar di tengah pada HP */
        .col-md-3 {
            text-align: center !important;
            margin-bottom: 20px !important;
        }

        /* Memastikan kolom data (tabel) mengambil lebar penuh */
        .col-md-9 {
             padding: 0 10px;
        }

        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        
        /* Membuat baris tabel lebih kompak */
        .table th, .table td {
            font-size: 0.9rem;
            padding: 0.5rem;
        }
    }
</style>

<div class="container-fluid mt-4">

    {{-- Alert Success/Error (TETAP) --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <strong>Gagal!</strong> Periksa kembali input Anda.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    {{-- End Alert --}}

    <div class="card p-4 shadow-lg rounded-3">
        <div class="card-header bg-primary text-white rounded-top-3">
            <h4 class="mb-0"><i class="fa-solid fa-user me-2"></i> Biodata Pengguna</h4>
        </div>
        <div class="card-body">
            <div class="row">
                {{-- Kolom Avatar: Gunakan 'text-center' untuk HP agar rata tengah --}}
                <div class="col-md-3 text-center mb-4">
                    {{-- Tampilkan Avatar --}}
                    @if ($user->avatar)
                        <img src="{{ $user->avatar }}" alt="Avatar" class="img-fluid rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <i class="fa-solid fa-circle-user" style="font-size: 150px; color: #ccc;"></i>
                    @endif
                    <h5 class="mt-3">{{ $user->name }}</h5>
                </div>
                
                {{-- Kolom Data: Pastikan kolom data menumpuk di HP --}}
                <div class="col-md-9">
                    @if($isGoogleUser)
                        <div class="alert alert-warning py-2 mb-3">
                            <i class="fa-brands fa-google me-1"></i> Anda login menggunakan Google. **Email tidak dapat diubah.**
                        </div>
                    @endif
                    
                    {{-- Tambahkan table-responsive untuk tampilan HP --}}
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <tbody>
                                <tr>
                                    <th style="width: 30%;">Nama Lengkap</th>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $user->email }}</td>
                                </tr>
                                <tr>
                                    <th>Nomor HP / WA</th>
                                    <td>{{ $user->no_hp ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Tanggal Lahir</th>
                                    <td>{{ $user->tgl_lahir ? \Carbon\Carbon::parse($user->tgl_lahir)->isoFormat('D MMMM YYYY') : '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $user->alamat ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <th>Bergabung Sejak</th>
                                    <td>{{ $user->created_at->isoFormat('D MMMM YYYY') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    {{-- Tombol Edit dan Kembali (MODIFIKASI) --}}
                    <div class="mt-4 d-grid gap-2 d-md-block"> 
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#editBiodataModal">
                            <i class="fa-solid fa-edit me-1"></i> Edit Biodata
                        </button>
                        
                        {{-- TOMBOL KEMBALI BARU --}}
                        <a href="{{ route('home') }}" class="btn btn-secondary">
                            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- === MODAL EDIT BIODATA (TETAP) === --}}
<div class="modal fade" id="editBiodataModal" tabindex="-1" aria-labelledby="editBiodataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-white">
                <h5 class="modal-title" id="editBiodataModalLabel"><i class="fa-solid fa-edit me-2"></i> Edit Biodata</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('biodata.update') }}" method="POST">
                @csrf
                <div class="modal-body">
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nama Lengkap</label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" 
                               {{ $isGoogleUser ? 'disabled' : 'required' }}>
                        @if($isGoogleUser)
                            <small class="form-text text-muted">Email tidak bisa diubah karena Anda *login* via Google.</small>
                        @endif
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">Nomor HP / WA</label>
                        <input type="text" name="no_hp" id="no_hp" class="form-control @error('no_hp') is-invalid @enderror" value="{{ old('no_hp', $user->no_hp) }}">
                        @error('no_hp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="tgl_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" id="tgl_lahir" class="form-control @error('tgl_lahir') is-invalid @enderror" value="{{ old('tgl_lahir', $user->tgl_lahir) }}">
                        @error('tgl_lahir')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="alamat" class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" id="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="2">{{ old('alamat', $user->alamat) }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
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

{{-- SCRIPT: Tampilkan modal otomatis jika ada error validasi (TETAP) --}}
@if ($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editBiodataModal = new bootstrap.Modal(document.getElementById('editBiodataModal'));
        editBiodataModal.show();
    });
</script>
@endif

@endsection
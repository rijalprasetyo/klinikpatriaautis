@extends('layouts.admin-sidebar')

@section('title', 'Data Master Pengguna')

@section('content')
<div class="container-fluid py-3">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800 fw-bold">Data Master Pengguna Sistem</h1>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- CARD TABEL --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="m-0 fw-semibold text-primary mb-3">Pilih Kategori:</h6>
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a href="{{ route('admin.master.users', ['type' => 'user']) }}"
                       class="btn {{ $activeTab == 'user' ? 'btn-primary' : 'btn-outline-primary' }} fw-semibold">
                        <i class="fa-solid fa-user-injured me-1"></i> User
                    </a>
                    <a href="{{ route('admin.master.users', ['type' => 'dokter']) }}"
                       class="btn {{ $activeTab == 'dokter' ? 'btn-primary' : 'btn-outline-primary' }} fw-semibold">
                        <i class="fa-solid fa-user-md me-1"></i> Dokter
                    </a>
                    <a href="{{ route('admin.master.users', ['type' => 'admin']) }}"
                       class="btn {{ $activeTab == 'admin' ? 'btn-primary' : 'btn-outline-primary' }} fw-semibold">
                        <i class="fa-solid fa-user-tie me-1"></i> Admin
                    </a>
                </div>
            </div>

            {{-- Tombol Tambah Data --}}
            @if ($activeTab == 'dokter')
                <button class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#addDokterModal">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Dokter
                </button>
            @elseif ($activeTab == 'admin')
                <button class="btn btn-success fw-semibold" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Admin
                </button>
            @endif
        </div>
        
        <div class="card-body bg-light">
            <h5 class="fw-bold mb-4 text-{{ $activeTab == 'user' ? 'primary' : ($activeTab == 'dokter' ? 'success' : 'dark') }}">
                Data {{ ucfirst($activeTab) }}
            </h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0" id="dataTable" width="100%">
                    <thead class="table-primary text-center align-middle">
                        <tr>
                            <th style="width: 50px;">No</th>
                            @if ($activeTab == 'user')
                                <th>Nama</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Alamat</th>
                                <th>Tgl. Lahir</th>
                                <th style="width: 120px;">Jenis Akun</th>
                                <th style="width: 120px;">Tgl. Gabung</th>
                            @elseif ($activeTab == 'dokter')
                                <th>Nama Dokter</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>No. HP</th>
                                <th>Alamat</th>
                                <th>Status</th>
                            @elseif ($activeTab == 'admin')
                                <th>Nama Admin</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>No. HP</th>
                            @endif
                            <th style="width: 200px;">Aksi</th> {{-- Lebar Aksi diperbesar --}}
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data as $index => $item)
                            <tr>
                                <td class="text-center">{{ $index + 1 }}</td>
                                
                                {{-- TABEL USER / PASIEN --}}
                                @if ($activeTab == 'user')
                                    <td class="fw-semibold">{{ $item->name }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->no_hp }}</td>
                                    <td>{{ $item->alamat }}</td>
                                    <td>{{ \Carbon\Carbon::parse($item->tgl_lahir)->format('d M Y') }}</td>
                                    <td class="text-center">
                                        @if ($item->google_id)
                                            <span class="badge bg-danger">Google</span>
                                        @else
                                            <span class="badge bg-secondary">Manual</span>
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</td>
                                
                                {{-- TABEL DOKTER --}}
                                @elseif ($activeTab == 'dokter')
                                    <td class="fw-semibold">{{ $item->nama_dokter }}</td>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->no_hp }}</td>
                                    <td>{{ $item->alamat }}</td>
                                    <td class="text-center">
                                        @if ($item->status == 1)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary">Tidak Aktif</span>
                                        @endif
                                    </td>

                                {{-- TABEL ADMIN --}}
                                @elseif ($activeTab == 'admin')
                                    <td class="fw-semibold">{{ $item->nama }}</td>
                                    <td>{{ $item->username }}</td>
                                    <td>{{ $item->email }}</td>
                                    <td>{{ $item->no_hp }}</td>
                                @endif
                                
                                <td class="text-center">
                                    @if ($activeTab == 'dokter')
                                        {{-- Tombol Ubah Status Dokter --}}
                                        <button class="btn btn-warning btn-sm" title="Ubah Status"
                                            data-bs-toggle="modal" data-bs-target="#ubahStatusModal{{ $item->id }}">
                                            <i class="fa-solid fa-rotate"></i>
                                        </button>
                                        
                                        {{-- Tombol RESET PASSWORD DOKTER --}}
                                        <button class="btn btn-info btn-sm text-white" title="Reset Password"
                                            data-bs-toggle="modal" data-bs-target="#resetPasswordModal{{ $item->id }}">
                                            <i class="fa-solid fa-lock-open"></i>
                                        </button>

                                        {{-- Tombol Hapus Dokter (Modal Bootstrap) --}}
                                        <button type="button" class="btn btn-danger btn-sm" title="Hapus Data"
                                            data-bs-toggle="modal" data-bs-target="#deleteDokterModal{{ $item->id }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>

                                        {{-- MODAL KONFIRMASI HAPUS DOKTER (Kode yang sudah ada) --}}
                                        <div class="modal fade" id="deleteDokterModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fa-solid fa-triangle-exclamation me-2"></i>Peringatan Penghapusan
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="fw-semibold text-danger mb-2">
                                                            ⚠️ Jika data dokter dihapus akan menyebabkan kerusakan integrasi data!
                                                        </p>
                                                        <p class="mb-1">
                                                            Pastikan data dokter yang akan dihapus <strong>belum pernah melakukan penanganan pasien sama sekali</strong>.
                                                        </p>
                                                        <p class="mb-0">
                                                            Lebih disarankan untuk <strong>mengganti statusnya menjadi nonaktif</strong> daripada menghapus permanen.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('admin.delete-dokter', $item->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fa-solid fa-trash"></i> Ya, Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        {{-- MODAL KONFIRMASI RESET PASSWORD DOKTER (MODIFIED) --}}
                                        <div class="modal fade" id="resetPasswordModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-info text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fa-solid fa-lock-open me-2"></i> Reset Password Dokter
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="text-danger fw-bold">PERINGATAN:</p>
                                                        <p>
                                                            Anda akan mereset password untuk dokter **{{ $item->nama_dokter }}** (**Username: {{ $item->username }}**).
                                                        </p>
                                                        <p class="mb-0">
                                                            Password dokter akan diatur ulang dan disamakan dengan nilai **Username** saat ini. Mohon informasikan perubahan ini kepada dokter yang bersangkutan.
                                                        </p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('admin.dokter.resetPassword', $item->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PUT')
                                                            <button type="submit" class="btn btn-info text-white">
                                                                <i class="fa-solid fa-check me-1"></i> Reset Password Sekarang
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @elseif ($activeTab == 'admin')
                                        {{-- Tombol Hapus Admin (Modal Bootstrap) --}}
                                        <button type="button" class="btn btn-danger btn-sm" title="Hapus Data"
                                            data-bs-toggle="modal" data-bs-target="#deleteAdminModal{{ $item->id }}">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>

                                        {{-- MODAL KONFIRMASI HAPUS ADMIN (Kode yang sudah ada) --}}
                                        <div class="modal fade" id="deleteAdminModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="fa-solid fa-triangle-exclamation me-2"></i>Konfirmasi Hapus Admin
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Apakah kamu yakin ingin menghapus data admin <strong>{{ $item->nama }}</strong>?
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                        <form action="{{ route('admin.delete-admin', $item->id) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fa-solid fa-trash"></i> Ya, Hapus
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>

                            {{-- MODAL UBAH STATUS DOKTER (Kode yang sudah ada) --}}
                            @if ($activeTab == 'dokter')
                            <div class="modal fade" id="ubahStatusModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning text-white">
                                            <h5 class="modal-title">Ubah Status Dokter</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form action="{{ route('admin.dokter.updateStatus', $item->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body text-center">
                                                <p>Ubah status dokter <b>{{ $item->nama_dokter }}</b>?</p>
                                                <select name="status" class="form-select">
                                                    <option value="1" {{ $item->status == 1 ? 'selected' : '' }}>Aktif</option>
                                                    <option value="0" {{ $item->status == 0 ? 'selected' : '' }}>Tidak Aktif</option>
                                                </select>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning text-white">Simpan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    Tidak ada data {{ $activeTab }} yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH DOKTER --}}
<div class="modal fade" id="addDokterModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">Tambah Dokter Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.dokter.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Nama Dokter</label>
                            <input type="text" name="nama_dokter" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. HP</label>
                            <input type="text" name="no_hp" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Alamat</label>
                            <input type="text" name="alamat" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1">Aktif</option>
                                <option value="0">Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH ADMIN --}}
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Tambah Admin Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.admin.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. HP</label>
                        <input type="text" name="no_hp" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

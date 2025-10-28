@extends('layouts.admin-sidebar')

@section('title', 'Data Backup')

@section('content')
<div class="container-fluid py-4">
    <h3 class="fw-bold mb-4"><i class="fa-solid fa-database"></i> Data Backup</h3>

    {{-- Alert sukses --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tombol Backup --}}
    <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#confirmBackupModal">
        <i class="fa-solid fa-cloud-arrow-up"></i> Buat Backup Baru
    </button>

    {{-- Modal Konfirmasi Backup --}}
    <div class="modal fade" id="confirmBackupModal" tabindex="-1">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.data-backup.store') }}" class="modal-content">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fa-solid fa-triangle-exclamation"></i> Konfirmasi Backup
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-center">
                        Apakah Anda yakin ingin membuat backup data sekarang?<br>
                        File akan disimpan secara otomatis di:
                    </p>
                    <div class="text-center">
                        <code>C:\Backupdata\</code>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-play"></i> Lanjutkan Backup
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Data Backup --}}
    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead class="table-primary text-center">
                    <tr>
                        <th>#</th>
                        <th>Nama File</th>
                        <th>Lokasi</th>
                        <th>Ukuran</th>
                        <th>Dibuat Oleh</th>
                        <th>Status</th>
                        <th>Tanggal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($backups as $key => $backup)
                        <tr>
                            <td class="text-center">{{ $key + 1 }}</td>
                            <td>{{ $backup->file_name }}</td>
                            <td><code>{{ $backup->file_path }}</code></td>
                            <td>{{ $backup->file_size }}</td>
                            <td>{{ $backup->created_by }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $backup->status == 'Sukses' ? 'success' : 'danger' }}">
                                    {{ $backup->status }}
                                </span>
                            </td>
                            <td>{{ $backup->created_at->format('d M Y, H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">Belum ada data backup.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

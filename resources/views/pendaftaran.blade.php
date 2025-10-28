@extends('layouts.user-sidebar')

@section('content')

<style>
    /* ... Tambahkan variabel CSS yang relevan dari file sebelumnya ... */
    :root {
        --primary: #2563eb;
        --primary-dark: #1e40af;
        --bg-card: #ffffff;
        --text-primary: #0f172a;
        --text-secondary: #64748b;
        --border: #e2e8f0;
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        /* Input Field Styling */
        --input-border: #cbd5e1; 
        --input-focus: #3b82f6;
    }
    .form-control, .form-select, textarea {
        border: 1px solid var(--input-border);
        border-radius: 8px;
        padding: 10px 15px;
        transition: border-color 0.3s, box-shadow 0.3s;
    }
    .form-control:focus, .form-select:focus, textarea:focus {
        border-color: var(--input-focus);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    .form-label {
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 5px;
        font-size: 14px;
    }
    .file-upload-section {
        border: 2px dashed var(--border);
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        background-color: var(--bg-main);
        margin-top: 15px;
    }
    .alert-info-category {
        background-color: var(--primary-light);
        color: var(--primary-dark);
        border-left: 5px solid var(--primary);
        padding: 1rem;
        border-radius: 8px;
        font-weight: 500;
    }
    .card-form {
        border: none;
        box-shadow: var(--shadow-md);
        padding: 2.5rem;
        border-radius: 12px;
    }
</style>

@php
    // Mendapatkan kategori dari URL query string (misalnya: ?kategori=Masyarakat%20Umum)
    $kategori = request()->get('kategori', 'Masyarakat Umum'); 
    $isSktmRequired = ($kategori == 'Disabilitas (SKTM)');

    $jam_operasional = [
        '07:00 - 08:00', '08:00 - 09:00', '09:00 - 10:00', '10:00 - 11:00', 
        '11:00 - 12:00', '16:00 - 17:00', '17:00 - 18:00', '18:00 - 19:00', 
        '19:00 - 20:00'
    ];
    $layanan = [
        'Autism Spectrum Disorder', 'Cerebral Palsy', 'Down Syndrome', 'Cedera Anak-Dewasa',
        'Gangguan Postural', 'Gross Delay Development', 'Gangguan Sensorik & Motorik', 'Layanan Pasca Operasi'
    ];
@endphp

<div class="container-fluid px-4 py-3">
    <div class="page-header">
        <h1><i class="fa-solid fa-user-plus"></i> Formulir Pendaftaran</h1>
        <p>Lengkapi data pasien untuk mendaftar pelayanan di Klinik Patria.</p>
    </div>

    <div class="alert-info-category mb-4">
        Kategori yang dipilih: <strong>{{ $kategori }}</strong>
    </div>

    <div class="card card-form bg-card">
        <form action="{{ route('pendaftaran.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="hidden" name="kategori_pendaftaran" value="{{ $kategori }}">

            <div class="row g-4">
                
                {{-- Data Pasien --}}
                <div class="col-12"><h3 class="section-title mb-0">Data Pasien</h3><hr class="mt-2"></div>
                
                <div class="col-md-6">
                    <label for="nama_lengkap" class="form-label">1. Nama Lengkap Pasien</label>
                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                </div>
                <div class="col-md-6">
                    <label for="tanggal_lahir" class="form-label">2. Tanggal Lahir</label>
                    <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                </div>
                <div class="col-md-6">
                    <label for="jenis_kelamin" class="form-label">3. Jenis Kelamin</label>
                    <select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required>
                        <option value="">Pilih Jenis Kelamin</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="nomor_hp" class="form-label">4. Nomor HP/WA</label>
                    <input type="tel" class="form-control" id="nomor_hp" name="nomor_hp" placeholder="Contoh: 081234567890" required>
                </div>
                <div class="col-12">
                    <label for="alamat_lengkap" class="form-label">5. Alamat Lengkap</label>
                    <textarea class="form-control" id="alamat_lengkap" name="alamat_lengkap" rows="3" required></textarea>
                </div>
                <div class="col-12">
                    <label for="nama_wali" class="form-label">6. Nama Wali/Pendamping</label>
                    <input type="text" class="form-control" id="nama_wali" name="nama_wali" required>
                </div>

                {{-- Detail Pelayanan & Keluhan --}}
                <div class="col-12"><h3 class="section-title mt-4 mb-0">Detail Pelayanan</h3><hr class="mt-2"></div>

                <div class="col-md-6">
                    <label for="jenis_layanan" class="form-label">7. Jenis Disabilitas / Layanan</label>
                    <select class="form-select" id="jenis_layanan" name="jenis_layanan" required>
                        <option value="">Pilih Jenis Layanan</option>
                        @foreach ($layanan as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="keluhan_utama" class="form-label">8. Keluhan Utama</label>
                    <textarea class="form-control" id="keluhan_utama" name="keluhan_utama" rows="1" required></textarea>
                </div>
                
                {{-- Jadwal Kunjungan --}}
                <div class="col-12"><h3 class="section-title mt-4 mb-0">9. Jadwal Kunjungan</h3><hr class="mt-2"></div>
                
                <div class="col-md-6">
                    <label for="tanggal_kunjungan" class="form-label">Tanggal Kunjungan</label>
                    <input type="date" class="form-control" id="tanggal_kunjungan" name="tanggal_kunjungan" min="{{ date('Y-m-d') }}" required>
                </div>
                <div class="col-md-6">
                    <label for="jam_kunjungan" class="form-label">Jam Kunjungan</label>
                    <select class="form-select" id="jam_kunjungan" name="jam_kunjungan" required>
                        <option value="">Pilih Jam Kunjungan</option>
                        @foreach ($jam_operasional as $jam)
                            <option value="{{ $jam }}">{{ $jam }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Upload File --}}
                <div class="col-12"><h3 class="section-title mt-4 mb-0">10. Dokumen Pendukung</h3><hr class="mt-2"></div>

                {{-- Upload SKTM (Dinamis) --}}
                <div class="col-md-6" id="sktm_upload_section" style="display: {{ $isSktmRequired ? 'block' : 'none' }};">
                    <label for="upload_sktm" class="form-label">Upload SKTM (Wajib)</label>
                    <div class="file-upload-section">
                        <i class="fa-solid fa-file-pdf fa-2x mb-2 text-primary"></i>
                        <p class="mb-0 small text-secondary">Seret & lepas atau klik untuk unggah file SKTM.</p>
                        <input type="file" class="form-control" id="upload_sktm" name="upload_sktm" {{ $isSktmRequired ? 'required' : '' }} accept="image/*, application/pdf">
                    </div>
                </div>

                {{-- Upload Bukti Pembayaran (Selalu Ada) --}}
                <div class="col-md-6">
                    <label for="upload_pembayaran" class="form-label">Upload Bukti Pembayaran (Wajib)</label>
                    <div class="file-upload-section">
                        <i class="fa-solid fa-file-image fa-2x mb-2 text-success"></i>
                        <p class="mb-0 small text-secondary">Seret & lepas atau klik untuk unggah bukti pembayaran.</p>
                        <input type="file" class="form-control" id="upload_pembayaran" name="upload_pembayaran" required accept="image/*, application/pdf">
                    </div>
                </div>

                <div class="col-12 mt-5 text-center">
                    <button type="submit" class="btn btn-primary btn-lg" style="background-color: var(--primary); border-color: var(--primary); padding: 12px 30px;">
                        <i class="fa-solid fa-paper-plane me-2"></i> Ajukan Pendaftaran
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
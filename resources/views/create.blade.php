@extends('layouts.user-sidebar')

@section('content')

@php
    $isSktm = ($kategori === 'Disabilitas (Dengan SKTM)');
    $isNonSktm = ($kategori === 'Disabilitas (Non-SKTM)');
    $isMasyarakatUmum = ($kategori === 'Masyarakat Umum');
    
    // Asumsi biaya tetap sesuai kategori
    $biaya = ($isSktm || $isNonSktm) ? 30000 : 80000;
    // Pengecekan biaya lama (Disabilitas SKTM: 30k, Non-SKTM/Umum: 80k)
    // Di kode Anda, SKTM 30k, lainnya 80k. Kita ikuti logika Anda yang awal:
    $biaya = $isSktm ? 30000 : 80000;
    $biayaFormatted = 'Rp ' . number_format($biaya, 0, ',', '.');
    $rekeningNumber = '3680578094';
    $rekeningBank = 'Bank BCA';
    $rekeningName = 'Dihanusa Tyasahita';
@endphp

<style>
    /* Variabel Warna (Palet Profesional) */
    :root {
        --primary: #0d6efd;
        --primary-dark: #0a58ca;
        --secondary: #6c757d;
        --success: #198754;
        --warning: #ffc107;
        --bg-main: #f8f9fa;
        --bg-card: #ffffff;
        --text-primary: #212529;
        --text-secondary: #495057;
        --border-color: #e9ecef;
        --input-border: #ced4da;
        --shadow-elevation: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    /* === Global Styling === */
    .container-fluid {
        background-color: var(--bg-main);
        padding-top: 20px;
        padding-bottom: 20px;
    }

    .card {
        border: none;
        box-shadow: var(--shadow-elevation);
        border-radius: 16px !important;
        overflow: hidden;
        margin-bottom: 30px;
    }

    /* === Form Header === */
    .form-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 20px 30px;
        border-bottom: 5px solid var(--primary-dark);
        border-radius: 16px 16px 0 0;
    }
    .form-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0;
    }
    .form-header p {
        color: rgba(255, 255, 255, 0.85);
        margin-top: 5px;
        margin-bottom: 0;
        font-size: 0.95rem;
    }
    .form-header .cost {
        font-size: 1.8rem;
        font-weight: 800;
        padding: 5px 15px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #ffc107;
    }

    /* === Section Title === */
    .form-section-title {
        font-size: 1.3rem;
        font-weight: 600;
        color: var(--primary);
        padding-bottom: 8px;
        border-bottom: 2px solid var(--border-color);
        margin-top: 30px !important;
        margin-bottom: 20px !important;
    }

    /* === Input & Select Styling === */
    .form-control, .form-select, textarea {
        border-radius: 8px;
        padding: 10px 15px;
        border: 1px solid var(--input-border);
        transition: all 0.2s ease-in-out;
        font-size: 0.95rem;
        color: var(--text-secondary);
    }
    .form-control:focus, .form-select:focus, textarea:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }
    .form-label {
        font-weight: 500;
        color: var(--text-primary);
        margin-bottom: 5px;
        font-size: 0.95rem;
    }

    /* === Alert Styling === */
    .alert-info {
        background-color: #e3f2fd;
        color: var(--text-primary);
        border: 1px solid #b6d4fe;
        border-radius: 8px;
        padding: 15px;
        font-size: 1rem;
    }

    /* === ENHANCED PAYMENT SECTION === */
    .payment-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 25px;
        color: white;
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .payment-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transition: transform 0.5s ease;
    }
    
    .payment-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.4);
    }
    
    .payment-card:hover::before {
        transform: translate(10%, 10%);
    }

    .payment-amount {
        font-size: 2.2rem;
        font-weight: 800;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
        margin: 10px 0;
    }

    .payment-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
    }

    .payment-status-badge.pending {
        background: rgba(255, 193, 7, 0.2);
        color: #ffc107;
        border: 2px solid #ffc107;
    }

    .payment-status-badge.completed {
        background: rgba(25, 135, 84, 0.2);
        color: #198754;
        border: 2px solid #198754;
        animation: successPulse 2s ease-in-out infinite;
    }

    @keyframes successPulse {
        0%, 100% { box-shadow: 0 0 0 0 rgba(25, 135, 84, 0.4); }
        50% { box-shadow: 0 0 0 10px rgba(25, 135, 84, 0); }
    }

    /* === FILE UPLOAD ENHANCED === */
    .file-upload-zone {
        border: 2px dashed var(--input-border);
        border-radius: 12px;
        padding: 25px;
        text-align: center;
        transition: all 0.3s ease;
        background: #fafbfc;
        cursor: pointer;
        position: relative;
    }

    .file-upload-zone:hover {
        border-color: var(--primary);
        background: #f0f7ff;
    }

    .file-upload-zone.dragging {
        border-color: var(--success);
        background: #d1f2eb;
    }

    .file-upload-zone.has-file {
        border-color: var(--success);
        background: #d1f2eb;
        border-style: solid;
    }

    .file-upload-icon {
        font-size: 3rem;
        color: var(--primary);
        margin-bottom: 10px;
        transition: transform 0.3s ease;
    }

    .file-upload-zone:hover .file-upload-icon {
        transform: scale(1.1);
    }

    .file-preview {
        display: none;
        margin-top: 15px;
        padding: 15px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .file-preview.show {
        display: block;
        animation: slideDown 0.3s ease;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .file-preview-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .file-icon {
        font-size: 2rem;
        color: var(--success);
    }

    .file-details {
        flex: 1;
    }

    .file-name {
        font-weight: 600;
        color: var(--text-primary);
        margin: 0;
    }

    .file-size {
        font-size: 0.85rem;
        color: var(--text-secondary);
    }

    .remove-file-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .remove-file-btn:hover {
        background: #bb2d3b;
        transform: scale(1.05);
    }

    /* === PROGRESS INDICATOR === */
    .payment-progress {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin: 25px 0;
        position: relative;
    }

    .progress-step {
        flex: 1;
        text-align: center;
        position: relative;
    }

    .progress-step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e9ecef;
        color: #6c757d;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }

    .progress-step.active .progress-step-circle {
        background: var(--primary);
        color: white;
        box-shadow: 0 0 0 4px rgba(13, 110, 253, 0.2);
    }

    .progress-step.completed .progress-step-circle {
        background: var(--success);
        color: white;
    }

    .progress-step-label {
        font-size: 0.85rem;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .progress-step.active .progress-step-label {
        color: var(--primary);
        font-weight: 600;
    }

    .progress-line {
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 1;
    }

    .progress-line-fill {
        height: 100%;
        background: var(--success);
        width: 0%;
        transition: width 0.5s ease;
    }

    /* === Button Styling === */
    .btn-primary, .btn-secondary, .btn-success {
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        padding: 12px 24px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }

    /* === Modal Enhancements === */
    .modal-content {
        border-radius: 16px;
        border: none;
    }

    .rekening-detail {
        padding: 20px;
        border: 2px solid var(--border-color);
        border-radius: 12px;
        margin-bottom: 15px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        transition: all 0.3s ease;
    }

    .rekening-detail:hover {
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.1);
    }

    .rekening-number {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--primary-dark);
        letter-spacing: 2px;
        user-select: all;
    }

    .copy-success {
        animation: copySuccess 0.5s ease;
    }

    @keyframes copySuccess {
        0% { transform: scale(1); }
        50% { transform: scale(1.1); }
        100% { transform: scale(1); }
    }

    /* === Responsive === */
    @media (max-width: 768px) {
        .card > form.p-4 {
            padding: 1.5rem !important;
        }

        .form-control, .form-select, textarea {
            padding: 9px 12px;
            font-size: 1rem;
        }

        .form-label {
            font-size: 1rem;
        }

        .form-header {
            flex-direction: row;
            align-items: center;
            padding: 15px 20px;
        }

        .form-header h2 {
            font-size: 1.3rem;
        }

        .form-header .cost {
            font-size: 1.4rem;
            padding: 3px 10px;
        }

        .payment-amount {
            font-size: 1.8rem;
        }

        .payment-card {
            padding: 20px;
        }

        .progress-step-circle {
            width: 35px;
            height: 35px;
            font-size: 0.9rem;
        }

        .progress-step-label {
            font-size: 0.75rem;
        }

        .btn-lg {
            padding: 12px 16px;
            font-size: 1rem;
        }
    }
</style>

<div class="container-fluid mt-4">

    {{-- Alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
            <strong>Berhasil!</strong> {{ session('success') }}
            <a href="{{ route('pendaftaran.success', ['antrian' => session('nomor_antrian')]) }}" class="btn btn-sm btn-success ms-2">Lihat Bukti & Unduh PDF</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <strong>Gagal!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
            <strong>Gagal Validasi!</strong> Mohon periksa kembali inputan Anda.
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card p-0 shadow-lg rounded-3">
        <div class="form-header d-flex justify-content-between align-items-center">
            <div>
                <h2>Formulir Pendaftaran Pasien</h2>
                <p>Kategori: <strong>{{ $kategori }}</strong></p>
            </div>
            <div class="cost h5">{{ $biayaFormatted }}</div>
        </div>

        <form id="pendaftaranForm" action="{{ route('pendaftaran.store') }}" method="POST" enctype="multipart/form-data" class="p-4">
            @csrf
            <input type="hidden" name="kategori" value="{{ $kategori }}">
            <input type="hidden" name="biaya_pendaftaran" value="{{ $biaya }}">
            <input type="file" name="bukti_pembayaran" id="bukti_pembayaran_input" class="d-none" required accept=".jpg,.jpeg,.png,.pdf">
            @if ($isSktm)
                <input type="file" name="sktm" id="sktm_input" class="d-none" required accept=".jpg,.jpeg,.png,.pdf">
            @endif

            {{-- DATA PASIEN --}}
            <h4 class="form-section-title">Data Pasien & Pendamping</h4>
            
            <div class="mb-3">
                <label class="form-label">Nama Lengkap Pasien</label>
                <input type="text" name="nama_pasien" class="form-control" value="{{ old('nama_pasien') }}" required>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Lahir</label>
                    <input type="date" name="tgl_lahir" class="form-control" value="{{ old('tgl_lahir') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Kelamin</label>
                    <select name="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih --</option>
                        <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Nomor HP / WA</label>
                <input type="text" name="nomor_hp" class="form-control" value="{{ old('nomor_hp') }}">
            </div>

            <div class="mb-3">
                <label class="form-label">Alamat Lengkap</label>
                <textarea name="alamat" class="form-control" rows="2" required>{{ old('alamat') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Wali / Pendamping</label>
                <input type="text" name="pendamping" class="form-control" value="{{ old('pendamping') }}" required>
            </div>

            {{-- DETAIL LAYANAN (MODIFIED) --}}
            <h4 class="form-section-title">Detail Layanan & Kunjungan</h4>

            @if (!$isMasyarakatUmum)
            {{-- KATEGORI DISABILITAS (SKTM / NON-SKTM) --}}
            <div class="mb-3">
                <label class="form-label">Jenis Disabilitas / Layanan</label>
                <select id="layanan_select" name="layanan_select" class="form-select" required>
                    <option value="">-- Pilih Layanan --</option>
                    @foreach ($jenisPelayanan as $layanan)
                        {{-- Menggunakan nama layanan sebagai value, bukan ID --}}
                        <option value="{{ $layanan->pelayanan }}" {{ old('layanan_select') == $layanan->pelayanan ? 'selected' : '' }}>{{ $layanan->pelayanan }}</option>
                    @endforeach
                    <option value="Lainnya" {{ old('layanan_select') == 'Lainnya' ? 'selected' : '' }}>Lainnya (Tulis Manual)</option>
                </select>
            </div>
            
            <div class="mb-3" id="layanan_manual_group" style="display: {{ old('layanan_select') == 'Lainnya' ? 'block' : 'none' }};">
                <label class="form-label">Jenis Layanan Lainnya (Tulis Manual)</label>
                {{-- Input tersembunyi yang akan dikirim ke controller untuk layanan_id --}}
                <input type="text" id="layanan_manual_input" name="layanan_id" class="form-control" value="{{ old('layanan_id') }}">
            </div>

            @else
            {{-- KATEGORI MASYARAKAT UMUM --}}
            <div class="mb-3">
                <label class="form-label">Jenis Layanan (Tulis Manual)</label>
                {{-- Input utama untuk Masyarakat Umum --}}
                <input type="text" id="layanan_manual_input_umum" name="layanan_id" class="form-control" value="{{ old('layanan_id') }}" placeholder="Contoh: Fisioterapi Nyeri Lutut / Umum" required>
            </div>
            @endif


            <div class="mb-3">
                <label class="form-label">Keluhan Utama</label>
                <textarea name="keluhan" class="form-control" rows="2" required>{{ old('keluhan') }}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Kunjungan</label>
                    <input type="date" id="tgl_kunjungan" name="tgl_kunjungan" class="form-control" value="{{ old('tgl_kunjungan') }}" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Jam Kunjungan</label>
                    <select id="waktu_id" name="waktu_id" class="form-select" required>
                        <option value="">-- Pilih Jam --</option>
                        @foreach ($jamPelayanan as $jam)
                            <option value="{{ $jam->id }}" 
                                data-jam="{{ $jam->jam_mulai }} - {{ $jam->jam_selesai }}"
                                {{ old('waktu_id') == $jam->id ? 'selected' : '' }}
                            >
                                {{ $jam->jam_mulai }} - {{ $jam->jam_selesai }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            @if ($isSktm)
            <div class="mb-3">
                <label class="form-label">Upload SKTM (Wajib, max 1MB)</label>
                <div class="file-upload-zone" id="sktm_upload_zone">
                    <i class="fa-solid fa-cloud-arrow-up file-upload-icon"></i>
                    <p class="mb-2"><strong>Klik atau seret file ke sini</strong></p>
                    <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Max: 1MB)</p>
                </div>
                <div class="file-preview" id="sktm_preview">
                    <div class="file-preview-item">
                        <div class="file-info">
                            <i class="fa-solid fa-file-circle-check file-icon"></i>
                            <div class="file-details">
                                <p class="file-name" id="sktm_file_name"></p>
                                <p class="file-size" id="sktm_file_size"></p>
                            </div>
                        </div>
                        <button type="button" class="remove-file-btn" onclick="removeSktmFile()">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- PEMBAYARAN SECTION - ENHANCED --}}
            <h4 class="form-section-title">
                <i class="fa-solid fa-credit-card me-2"></i>Pembayaran
            </h4>

            {{-- Payment Progress Indicator --}}
            <div class="payment-progress">
                <div class="progress-line">
                    <div class="progress-line-fill" id="progress_fill"></div>
                </div>
                <div class="progress-step" id="step1">
                    <div class="progress-step-circle">1</div>
                    <div class="progress-step-label">Lihat Detail</div>
                </div>
                <div class="progress-step" id="step2">
                    <div class="progress-step-circle">2</div>
                    <div class="progress-step-label">Transfer</div>
                </div>
                <div class="progress-step" id="step3">
                    <div class="progress-step-circle">3</div>
                    <div class="progress-step-label">Upload Bukti</div>
                </div>
            </div>

            {{-- Payment Card --}}
            <div class="payment-card mb-4" data-bs-toggle="modal" data-bs-target="#paymentModal">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-75">Total Biaya Pendaftaran</p>
                        <div class="payment-amount">{{ $biayaFormatted }}</div>
                        <div class="payment-status-badge pending" id="payment_status_badge">
                            <i class="fa-solid fa-clock"></i>
                            <span>Belum Dibayar</span>
                        </div>
                    </div>
                    <div>
                        <i class="fa-solid fa-arrow-right" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>

            {{-- File Upload Zone for Bukti Pembayaran --}}
            <div class="mb-4">
                <label class="form-label">
                    <i class="fa-solid fa-receipt me-2"></i>Bukti Pembayaran <span class="text-danger">*</span>
                </label>
                <div class="file-upload-zone" id="bukti_upload_zone">
                    <i class="fa-solid fa-cloud-arrow-up file-upload-icon"></i>
                    <p class="mb-2"><strong>Klik atau seret file ke sini</strong></p>
                    <p class="text-muted small mb-0">Format: JPG, PNG, PDF (Max: 1MB)</p>
                </div>
                <div class="file-preview" id="bukti_preview">
                    <div class="file-preview-item">
                        <div class="file-info">
                            <i class="fa-solid fa-file-circle-check file-icon"></i>
                            <div class="file-details">
                                <p class="file-name" id="bukti_file_name"></p>
                                <p class="file-size" id="bukti_file_size"></p>
                            </div>
                        </div>
                        <button type="button" class="remove-file-btn" onclick="removeBuktiFile()">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2">
                <button type="button" id="btnConfirm" class="btn btn-primary w-100 btn-lg">
                    <i class="fa-solid fa-paper-plane me-2"></i> Kirim Pendaftaran
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-secondary btn-lg" style="min-width: 150px;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
            </div>
        </form>
    </div>
</div>

{{-- MODAL PEMBAYARAN (TIDAK BERUBAH) --}}
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="paymentModalLabel">
            <i class="fa-solid fa-sack-dollar me-2"></i> Detail Pembayaran
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-center mb-3">Silahkan transfer biaya pendaftaran sebesar <strong class="text-danger">{{ $biayaFormatted }}</strong> ke rekening berikut:</p>
        
        <div class="rekening-detail text-center">
            <small class="text-muted">BANK</small>
            <p class="h6 mb-2"><strong>{{ $rekeningBank }}</strong></p>
            <small class="text-muted d-block">NOMOR REKENING</small>
            <p class="rekening-number mb-2" id="rekeningNumberDisplay" data-number="{{ $rekeningNumber }}">{{ $rekeningNumber }}</p>
            <small class="text-muted">ATAS NAMA</small>
            <p class="h6 mb-0"><strong>{{ $rekeningName }}</strong></p>
        </div>
        
        <button type="button" class="btn btn-outline-primary w-100 mt-2" onclick="copyRekening()" id="copyBtn">
            <i class="fa-regular fa-copy me-2"></i> Salin Nomor Rekening
        </button>

        <div class="alert alert-warning mt-3 mb-0">
            <small>
                <i class="fa-solid fa-circle-info me-1"></i>
                Setelah transfer, kembali ke formulir dan upload bukti pembayaran Anda.
            </small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary w-100" data-bs-dismiss="modal">Mengerti</button>
      </div>
    </div>
  </div>
</div>

{{-- MODAL KONFIRMASI --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content rounded-3 shadow">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="confirmModalLabel">
            <i class="fa-solid fa-circle-check me-2"></i>Konfirmasi Data Pendaftaran
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted mb-3">Periksa kembali data di bawah ini sebelum dikirim:</p>
        <div class="table-responsive"> 
            <table class="table table-bordered">
              <tbody>
                <tr><th>Nama Pasien</th><td id="c_nama_pasien"></td></tr>
                <tr><th>Tanggal Lahir</th><td id="c_tgl_lahir"></td></tr>
                <tr><th>Jenis Kelamin</th><td id="c_jenis_kelamin"></td></tr>
                <tr><th>Nomor HP</th><td id="c_nomor_hp"></td></tr>
                <tr><th>Alamat</th><td id="c_alamat"></td></tr>
                <tr><th>Pendamping</th><td id="c_pendamping"></td></tr>
                {{-- LAYANAN TIDAK MENGGUNAKAN ID LAGI --}}
                <tr><th>Layanan</th><td id="c_layanan_id"></td></tr>
                <tr><th>Keluhan</th><td id="c_keluhan"></td></tr>
                <tr><th>Tanggal Kunjungan</th><td id="c_tgl_kunjungan"></td></tr>
                <tr><th>Jam Kunjungan</th><td id="c_waktu_id"></td></tr>
                <tr><th>Biaya</th><td>{{ $biayaFormatted }}</td></tr>
                <tr><th>Bukti Pembayaran</th><td id="c_bukti_pembayaran"></td></tr>
                @if ($isSktm)
                <tr><th>Dokumen SKTM</th><td id="c_sktm"></td></tr>
                @endif
              </tbody>
            </table>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
        <button type="button" id="btnSubmitConfirm" class="btn btn-success">
          <i class="fa-solid fa-paper-plane me-2"></i> Kirim Sekarang
        </button>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('pendaftaranForm');
    const confirmBtn = document.getElementById('btnConfirm');
    const submitBtn = document.getElementById('btnSubmitConfirm');
    const confirmModal = new bootstrap.Modal(document.getElementById('confirmModal')); 
    const paymentModal = new bootstrap.Modal(document.getElementById('paymentModal')); 
    const tglKunjunganInput = document.getElementById('tgl_kunjungan');
    const waktuSelect = document.getElementById('waktu_id');
    const isSktmCategory = @json($isSktm);
    const isMasyarakatUmum = @json($isMasyarakatUmum);

    // BARU: Elemen untuk Layanan (Jika bukan Masyarakat Umum)
    const layananSelect = document.getElementById('layanan_select');
    const layananManualGroup = document.getElementById('layanan_manual_group');
    const layananManualInput = document.getElementById('layanan_manual_input');
    // BARU: Elemen untuk Layanan (Jika Masyarakat Umum)
    const layananManualInputUmum = document.getElementById('layanan_manual_input_umum');

    // Input file tersembunyi
    const buktiPembayaranInput = document.getElementById('bukti_pembayaran_input');
    const sktmInput = isSktmCategory ? document.getElementById('sktm_input') : null;

    // Upload zones
    const buktiUploadZone = document.getElementById('bukti_upload_zone');
    const buktiPreview = document.getElementById('bukti_preview');
    const sktmUploadZone = isSktmCategory ? document.getElementById('sktm_upload_zone') : null;
    const sktmPreview = isSktmCategory ? document.getElementById('sktm_preview') : null;

    // Progress tracking
    const step1 = document.getElementById('step1');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');
    const progressFill = document.getElementById('progress_fill');
    const paymentStatusBadge = document.getElementById('payment_status_badge');

    let paymentModalOpened = false;

    // --- LOGIC LAYANAN (BARU/MODIFIKASI) ---
    if (!isMasyarakatUmum) {
        layananSelect.addEventListener('change', function() {
            if (this.value === 'Lainnya') {
                layananManualGroup.style.display = 'block';
                layananManualInput.setAttribute('required', 'required');
                layananManualInput.focus();
                // Kosongkan input manual jika pengguna kembali memilih dari daftar
                layananManualInput.value = ''; 
            } else {
                layananManualGroup.style.display = 'none';
                layananManualInput.removeAttribute('required');
                // Masukkan nilai select ke input tersembunyi layanan_id
                layananManualInput.value = this.value; 
            }
        });

        // Set initial value for hidden input on page load/old input
        if (layananSelect.value && layananSelect.value !== 'Lainnya') {
            layananManualInput.value = layananSelect.value;
        }

    } else {
        // Logika Masyarakat Umum: hanya input manual yang digunakan, sudah dinamai 'layanan_id'
        // Cukup memastikan input ini required jika kategori Masyarakat Umum (sudah dilakukan di HTML)
    }

    // --- FILE UPLOAD HANDLERS (TIDAK BERUBAH) ---
    
    function setupFileUpload(uploadZone, input, preview, fileNameEl, fileSizeEl) {
        // ... (fungsi setupFileUpload tidak berubah) ...
        // Click to upload
        uploadZone.addEventListener('click', () => input.click());

        // Drag and drop
        uploadZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadZone.classList.add('dragging');
        });

        uploadZone.addEventListener('dragleave', () => {
            uploadZone.classList.remove('dragging');
        });

        uploadZone.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadZone.classList.remove('dragging');
            
            if (e.dataTransfer.files.length > 0) {
                input.files = e.dataTransfer.files;
                handleFileChange(input, uploadZone, preview, fileNameEl, fileSizeEl);
            }
        });

        // File input change
        input.addEventListener('change', () => {
            handleFileChange(input, uploadZone, preview, fileNameEl, fileSizeEl);
        });
    }

    function handleFileChange(input, uploadZone, preview, fileNameEl, fileSizeEl) {
        if (input.files.length > 0) {
            const file = input.files[0];
            fileNameEl.textContent = file.name;
            fileSizeEl.textContent = formatFileSize(file.size);
            
            uploadZone.classList.add('has-file');
            preview.classList.add('show');

            // Update progress if bukti pembayaran
            if (input === buktiPembayaranInput) {
                updateProgress(3);
                paymentStatusBadge.className = 'payment-status-badge completed';
                paymentStatusBadge.innerHTML = '<i class="fa-solid fa-circle-check"></i><span>Bukti Terupload</span>';
            }
        }
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
    }

    // Setup file uploads
    setupFileUpload(
        buktiUploadZone, 
        buktiPembayaranInput, 
        buktiPreview, 
        document.getElementById('bukti_file_name'),
        document.getElementById('bukti_file_size')
    );

    if (isSktmCategory) {
        setupFileUpload(
            sktmUploadZone, 
            sktmInput, 
            sktmPreview, 
            document.getElementById('sktm_file_name'),
            document.getElementById('sktm_file_size')
        );
    }

    // Remove file functions (global scope for onclick)
    window.removeBuktiFile = function() {
        buktiPembayaranInput.value = '';
        buktiUploadZone.classList.remove('has-file');
        buktiPreview.classList.remove('show');
        updateProgress(paymentModalOpened ? 1 : 0);
        paymentStatusBadge.className = 'payment-status-badge pending';
        paymentStatusBadge.innerHTML = '<i class="fa-solid fa-clock"></i><span>Belum Dibayar</span>';
    };

    window.removeSktmFile = function() {
        if (sktmInput) {
            sktmInput.value = '';
            sktmUploadZone.classList.remove('has-file');
            sktmPreview.classList.remove('show');
        }
    };

    // --- PROGRESS TRACKING (TIDAK BERUBAH) ---
    
    function updateProgress(stepNumber) {
        // Remove all active/completed states
        [step1, step2, step3].forEach(step => {
            step.classList.remove('active', 'completed');
        });

        // Set completed states
        for (let i = 1; i < stepNumber; i++) {
            document.getElementById(`step${i}`).classList.add('completed');
        }

        // Set active state
        if (stepNumber > 0) {
            document.getElementById(`step${stepNumber}`).classList.add('active');
        }

        // Update progress bar
        const progressPercent = ((stepNumber - 1) / 2) * 100;
        progressFill.style.width = progressPercent + '%';
    }

    // Track payment modal opening
    document.getElementById('paymentModal').addEventListener('show.bs.modal', function () {
        if (!paymentModalOpened) {
            paymentModalOpened = true;
            updateProgress(1);
        }
    });

    // When modal closes, move to step 2 (assuming they've seen it and will transfer)
    document.getElementById('paymentModal').addEventListener('hidden.bs.modal', function () {
        if (buktiPembayaranInput.files.length === 0) {
            updateProgress(2);
        }
    });

    // --- COPY REKENING FUNCTION (TIDAK BERUBAH) ---
    
    window.copyRekening = function() {
        const rekeningNumber = document.getElementById('rekeningNumberDisplay').getAttribute('data-number');
        const copyBtn = document.getElementById('copyBtn');
        
        navigator.clipboard.writeText(rekeningNumber).then(() => {
            // Visual feedback
            copyBtn.innerHTML = '<i class="fa-solid fa-check me-2"></i> Berhasil Disalin!';
            copyBtn.classList.remove('btn-outline-primary');
            copyBtn.classList.add('btn-success');
            document.getElementById('rekeningNumberDisplay').classList.add('copy-success');
            
            // Show toast notification
            showToast('Nomor rekening berhasil disalin!', 'success');
            
            // Reset after 2 seconds
            setTimeout(() => {
                copyBtn.innerHTML = '<i class="fa-regular fa-copy me-2"></i> Salin Nomor Rekening';
                copyBtn.classList.remove('btn-success');
                copyBtn.classList.add('btn-outline-primary');
                document.getElementById('rekeningNumberDisplay').classList.remove('copy-success');
            }, 2000);
        }).catch(err => {
            console.error('Gagal menyalin: ', err);
            showToast('Gagal menyalin. Silakan salin manual.', 'error');
        });
    };

    // Toast notification function
    function showToast(message, type) {
        const toastContainer = document.createElement('div');
        toastContainer.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#198754' : '#dc3545'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 9999;
            animation: slideIn 0.3s ease;
        `;
        toastContainer.textContent = message;
        document.body.appendChild(toastContainer);

        setTimeout(() => {
            toastContainer.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toastContainer.remove(), 300);
        }, 3000);
    }

    // Add animations
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(400px); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(400px); opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // --- DATE & TIME VALIDATION (TIDAK BERUBAH) ---
    
    function checkWaktuAvailability() {
        const today = new Date();
        const selectedDate = new Date(tglKunjunganInput.value);
        
        const todayDateOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        const selectedDateOnly = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), selectedDate.getDate());

        const isToday = selectedDateOnly.getTime() === todayDateOnly.getTime();
        const currentHour = today.getHours(); 
        const cutoffHour = currentHour + 1;

        Array.from(waktuSelect.options).forEach(option => {
            if (option.value === "") {
                option.disabled = false;
                return;
            }

            const jamText = option.getAttribute('data-jam');
            const jamMulaiStr = jamText.split(' - ')[0];  
            const jamMulai = parseInt(jamMulaiStr.split(':')[0]); 

            if (isToday) {
                if (jamMulai < cutoffHour) {
                    option.disabled = true;
                    if (!option.textContent.includes(' (Lewat)')) {
                       option.textContent = `${jamText} (Lewat)`;
                    }
                } else {
                    option.disabled = false;
                    option.textContent = jamText.replace(' (Lewat)', '');
                }
            } else {
                option.disabled = false;
                option.textContent = jamText.replace(' (Lewat)', '');
            }
        });
        
        if (waktuSelect.options[waktuSelect.selectedIndex] && waktuSelect.options[waktuSelect.selectedIndex].disabled) {
            waktuSelect.selectedIndex = 0;
        }
    }

    function setTanggalKunjunganConstraint() {
        const today = new Date();
        const yyyy = today.getFullYear();
        const mm = String(today.getMonth() + 1).padStart(2, '0');
        const dd = String(today.getDate()).padStart(2, '0');
        const minDate = `${yyyy}-${mm}-${dd}`;
        tglKunjunganInput.setAttribute('min', minDate);

        tglKunjunganInput.addEventListener('input', checkDateAndValidate);
        tglKunjunganInput.addEventListener('change', checkDateAndValidate);

        function checkDateAndValidate() {
            const dateValue = tglKunjunganInput.value;
            if (!dateValue) return;

            const selectedDate = new Date(dateValue + 'T00:00:00');
            const dayOfWeek = selectedDate.getDay(); 

            if (dayOfWeek === 5) {
                showToast('Pendaftaran tidak tersedia pada hari Jumat!', 'error');
                tglKunjunganInput.value = ''; 
                waktuSelect.selectedIndex = 0;
            } else {
                checkWaktuAvailability();
            }
        }

        if (tglKunjunganInput.value) {
            checkDateAndValidate();
        }
    }

    setTanggalKunjunganConstraint();

    // --- UTILITY FUNCTIONS (MODIFIED) ---

    function getLayananText() {
        if (isMasyarakatUmum) {
            // Ambil langsung dari input manual Masyarakat Umum
            return layananManualInputUmum.value || 'N/A (Masyarakat Umum)'; 
        }
        
        // Logika Disabilitas (Select/Manual)
        if (layananSelect.value === 'Lainnya') {
            return layananManualInput.value || 'Lainnya (Belum diisi)';
        }
        return layananSelect.value || 'N/A';
    }

    // --- MODAL KONFIRMASI & SUBMIT (MODIFIED) ---
    
    confirmBtn.addEventListener('click', function() {
        // Logika validasi untuk layanan
        let layananOk = true;
        if (!isMasyarakatUmum) {
            // Kategori Disabilitas: cek select
            if (layananSelect.value === '') {
                layananOk = false;
                layananSelect.focus();
                showToast('Mohon pilih jenis Layanan!', 'error');
            } else if (layananSelect.value === 'Lainnya' && !layananManualInput.value.trim()) {
                // Jika pilih 'Lainnya' tapi input manual kosong
                layananOk = false;
                layananManualInput.focus();
                showToast('Mohon isi Layanan Manual!', 'error');
            }
        } else if (!layananManualInputUmum.value.trim()) {
            // Kategori Masyarakat Umum: cek input manual
            layananOk = false;
            layananManualInputUmum.focus();
            showToast('Mohon isi jenis Layanan!', 'error');
        }

        if (!layananOk) return;


        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            const invalidInput = form.querySelector(':invalid');
            if (invalidInput) {
                invalidInput.focus();
            }
            if (buktiPembayaranInput.files.length === 0) {
                showToast('Mohon unggah Bukti Pembayaran!', 'error');
            } else if (isSktmCategory && sktmInput.files.length === 0) {
                showToast('Mohon unggah Dokumen SKTM!', 'error');
            }
            return;
        }

        if (waktuSelect.options[waktuSelect.selectedIndex] && waktuSelect.options[waktuSelect.selectedIndex].disabled) {
            showToast('Jam kunjungan telah lewat. Pilih jam lain!', 'error');
            waktuSelect.focus();
            return;
        }

        if (buktiPembayaranInput.files.length === 0) {
            showToast('Mohon unggah Bukti Pembayaran!', 'error');
            return;
        }
        if (isSktmCategory && sktmInput.files.length === 0) {
            showToast('Mohon unggah Dokumen SKTM!', 'error');
            return;
        }

        const formData = new FormData(form);
        const selectedWaktuOption = waktuSelect.options[waktuSelect.selectedIndex];

        // LOGIKA BARU UNTUK MENGISI NILAI LAYANAN_ID DI FORM SEBELUM SUBMIT
        if (!isMasyarakatUmum) {
            // Kategori Disabilitas
            if (layananSelect.value !== 'Lainnya') {
                // Jika bukan 'Lainnya', set nilai input hidden layanan_id = nilai select
                layananManualInput.value = layananSelect.value;
            }
            // Jika 'Lainnya', nilai layanan_id sudah diambil dari layananManualInput
        } else {
            // Kategori Masyarakat Umum
            // Nilai layanan_id sudah ada di layananManualInputUmum.value
        }


        document.getElementById('c_nama_pasien').textContent = formData.get('nama_pasien');
        document.getElementById('c_tgl_lahir').textContent = formData.get('tgl_lahir');
        document.getElementById('c_jenis_kelamin').textContent = formData.get('jenis_kelamin');
        document.getElementById('c_nomor_hp').textContent = formData.get('nomor_hp');
        document.getElementById('c_alamat').textContent = formData.get('alamat');
        document.getElementById('c_pendamping').textContent = formData.get('pendamping');
        
        // Gunakan fungsi getLayananText yang sudah diperbarui
        document.getElementById('c_layanan_id').textContent = getLayananText(); 
        
        document.getElementById('c_keluhan').textContent = formData.get('keluhan');
        document.getElementById('c_tgl_kunjungan').textContent = formData.get('tgl_kunjungan');
        document.getElementById('c_waktu_id').textContent = selectedWaktuOption.getAttribute('data-jam');
        document.getElementById('c_bukti_pembayaran').textContent = buktiPembayaranInput.files[0].name;
        
        if (isSktmCategory) {
            document.getElementById('c_sktm').textContent = sktmInput.files[0].name;
        }

        confirmModal.show();
    });

    submitBtn.addEventListener('click', function() {
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Mengirim...';
        submitBtn.disabled = true;
        confirmModal.hide();

        // Final check untuk memastikan layanan_id terisi dengan string yang benar
        if (!isMasyarakatUmum) {
             if (layananSelect.value !== 'Lainnya') {
                document.getElementById('layanan_manual_input').value = layananSelect.value;
            }
        } else {
            // Tidak perlu aksi tambahan, karena nama input sudah benar: layanan_id
        }

        form.submit();
    });
});
</script>
@endsection
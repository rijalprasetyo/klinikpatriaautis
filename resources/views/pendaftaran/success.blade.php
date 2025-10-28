<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Berhasil - Klinik Patria</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    
    <style>
        :root {
            --primary: #0d6efd;
            --success: #198754;
            --bg-light: #f8f9fa;
            --shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --secondary: #6c757d; /* Definisi warna sekunder */
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        /* Kartu Utama */
        .card-success {
            max-width: 480px;
            border: none;
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            padding: 40px;
            background-color: white;
            position: relative;
        }

        /* Ikon Sukses */
        .icon-success {
            font-size: 3rem;
            color: var(--success);
            margin-bottom: 15px;
        }

        /* Nomor Antrian yang Ditegaskan */
        .antrian-box {
            background-color: #f0f8ff;
            padding: 15px 25px;
            margin: 15px 0;
            border-radius: 10px;
            border: 1px dashed var(--primary);
        }
        .antrian-box h2 {
            font-size: 3rem;
            font-weight: 800;
            color: var(--primary);
            margin: 5px 0 0;
            letter-spacing: 2px;
        }

        /* Pesan Keterangan */
        .info-message {
            text-align: left;
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e9ecef;
            font-size: 0.9rem;
            color: var(--secondary);
        }

        /* Tombol */
        .btn {
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .btn-download {
            background-color: var(--success);
            border-color: var(--success);
            color: white;
        }
        .btn-download:hover {
            background-color: #157347;
            border-color: #157347;
        }

        /* Container Toast */
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1080; /* Pastikan di atas elemen lain */
        }
    </style>
</head>
<body class="d-flex align-items-center justify-content-center vh-100">
    
    <div class="card card-success text-center">
        
        <i class="fas fa-check-circle icon-success"></i>
        <h4 class="text-success fw-bold">Pendaftaran Berhasil!</h4>
        
        <p class="mt-3 mb-0 text-muted">Nomor antrian Anda adalah:</p>
        <div class="antrian-box">
            <h2>{{ $antrian }}</h2>
        </div>

        <div class="d-grid gap-2 mt-4">
            {{-- Cek apakah variabel $pasienId tersedia dari Controller/URL --}}
            @if(isset($pasienId))
                {{-- Tambahkan ID 'downloadBtn' --}}
                <a href="{{ route('pendaftaran.download', ['id' => $pasienId]) }}" class="btn btn-download btn-lg" id="downloadBtn">
                    <i class="fas fa-file-pdf me-2"></i> Unduh Tiket Pendaftaran (PDF)
                </a>
            @endif

            <a href="{{ route('home') }}" class="btn btn-primary btn-lg mt-2">
                Kembali ke Beranda
            </a>
        </div>

        {{-- Tambahan Teks Profesional dan Elegan --}}
        <div class="info-message">
            <p class="fw-bold mb-1">Terima kasih telah mendaftar di layanan fisioterapi Klinik Patria.</p>
            <p class="mb-2">Silakan bawa atau tunjukkan **tiket pendaftaran ini** saat berkunjung ke Klinik Patria pada jadwal yang telah Anda pilih.</p>
            <p class="text-secondary mb-0">Apabila terdapat perubahan jadwal atau informasi tambahan, tim kami akan menghubungi Anda melalui WhatsApp atau email yang terdaftar.</p>
        </div>
        
    </div>

    <div class="toast-container">
        <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" id="downloadToast">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check me-2"></i> **Unduhan Dimulai!** Tiket PDF sedang diproses.
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const downloadBtn = document.getElementById('downloadBtn');
            const downloadToastEl = document.getElementById('downloadToast');
            
            // Inisialisasi Toast Bootstrap
            const downloadToast = new bootstrap.Toast(downloadToastEl, {
                delay: 5000 // Toast akan hilang setelah 5 detik
            });

            if (downloadBtn) {
                downloadBtn.addEventListener('click', function(event) {
                    // Tampilkan notifikasi Toast saat tombol diklik
                    downloadToast.show();
                    
                    // Karena ini adalah tautan langsung untuk download (tanpa AJAX), kita biarkan
                    // aksi default (pengunduhan) tetap berjalan.
                });
            }
        });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pendaftaran Pasien - Klinik Patria</title>
    <style>
        /* Menggunakan font yang aman untuk PDF/Cetak dan mendukung Unicode */
        body { 
            font-family: DejaVu Sans, sans-serif; 
            margin: 0; 
            padding: 40px; 
            color: #333;
        }

        /* Variabel Warna */
        :root {
            --primary-color: #0d6efd; /* Biru Profesional */
            --secondary-color: #6c757d;
            --success-color: #198754;
            --border-color: #ddd;
            --header-bg: #f8f9fa;
        }

        /* Header Dokumen */
        .header {
            text-align: center;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--primary-color);
            margin-bottom: 25px;
        }
        .header h1 {
            color: var(--primary-color);
            font-size: 24px;
            margin: 0;
            font-weight: bold;
        }
        .header p {
            color: var(--secondary-color);
            font-size: 14px;
            margin: 5px 0 0;
        }

        /* Kotak Nomor Antrian (Highlight) */
        .antrian-box {
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #e6f2ff; /* Warna terang dari primary */
            border: 2px dashed var(--primary-color);
            border-radius: 8px;
        }
        .antrian-box strong {
            display: block;
            font-size: 16px;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        .antrian-box span {
            font-size: 36px;
            font-weight: 900;
            color: var(--primary-color);
            letter-spacing: 2px;
        }

        /* Gaya Tabel Data */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .data-table th, .data-table td {
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            font-size: 14px;
        }
        .data-table th {
            width: 35%; /* Memberi ruang lebih untuk label */
            background: var(--header-bg);
            color: var(--text-primary);
            font-weight: 600;
        }
        .data-table td {
            font-weight: normal;
        }

        /* Catatan Kaki (Pesan Penting) */
        .footer-note {
            margin-top: 30px;
            padding: 15px;
            border-top: 1px solid var(--border-color);
            font-size: 13px;
            color: var(--secondary-color);
            line-height: 1.6;
        }
        .footer-note strong {
            color: #333;
            font-weight: 700;
        }
        .highlight {
            color: var(--success-color);
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>TIKET PENDAFTARAN PASIEN</h1>
        <p>Layanan Fisioterapi Klinik Patria</p>
    </div>

    <div class="antrian-box">
        <strong>NOMOR ANTRIAN ANDA</strong>
        <span>{{ $pasien->nomor_antrian }}</span>
    </div>

    <h2>Detail Kunjungan</h2>
    <table class="data-table">
        <tbody>
            <tr><th>Tanggal Kunjungan</th><td class="highlight">{{ $pasien->tgl_kunjungan }}</td></tr>
            {{-- Asumsi $pasien->jam_kunjungan tersedia dan berisi teks jam (misal: 09:00 - 10:00) --}}
            {{-- Jika tidak ada, ganti $pasien->jam_kunjungan dengan variabel jam yang benar --}}
            <tr><th>Jam Kunjungan</th><td>{{ $pasien->waktu ? $pasien->waktu->jam_mulai . ' - ' . $pasien->waktu->jam_selesai : 'Tidak diketahui' }}</td></tr>
            <tr><th>Jenis Layanan</th><td>{{ $pasien->layanan_id ?? 'N/A' }}</td></tr>
            <tr><th>Keluhan Utama</th><td>{{ $pasien->keluhan }}</td></tr>
        </tbody>
    </table>

    <h2>Data Pasien</h2>
    <table class="data-table">
        <tbody>
            <tr><th>Nama Lengkap</th><td>{{ $pasien->nama_pasien }}</td></tr>
            <tr><th>Kategori Pasien</th><td>{{ $pasien->kategori_pendaftaran }}</td></tr>
            <tr><th>Tanggal Lahir</th><td>{{ $pasien->tgl_lahir }}</td></tr>
            <tr><th>Jenis Kelamin</th><td>{{ $pasien->jenis_kelamin }}</td></tr>
            <tr><th>Pendamping/Wali</th><td>{{ $pasien->pendamping }}</td></tr>
            <tr><th>Nomor HP/WA</th><td>{{ $pasien->nomor_hp }}</td></tr>
            <tr><th>Alamat</th><td>{{ $pasien->alamat }}</td></tr>
        </tbody>
    </table>

    <div class="footer-note">
        <strong>Perhatian Penting:</strong>
        <p>Terima kasih telah mendaftar. Silakan bawa atau tunjukkan tiket pendaftaran ini saat berkunjung ke Klinik Patria pada jadwal yang telah Anda pilih.</p>
        <p>Apabila terdapat perubahan jadwal atau informasi tambahan, tim kami akan menghubungi Anda melalui WhatsApp atau email yang terdaftar.</p>
    </div>
</body>
</html>
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DataPasien; // pastikan model ini sesuai
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

use Symfony\Component\HttpFoundation\StreamedResponse;

class DokterController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.dokter-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        // Cek dulu apakah username ada di tabel dokters
        $dokter = \App\Models\Dokter::where('username', $credentials['username'])->first();

        if (!$dokter) {
            return back()->with('error', 'Username tidak ditemukan.');
        }

        // Jika status dokter 0 (nonaktif), tolak login
        if ($dokter->status == 0) {
            return back()->with('error', 'Akun sudah tidak bisa digunakan');
        }

        // Jika status aktif (1), baru coba login
        if (Auth::guard('dokter')->attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->route('dokter.dashboard');
        }

        return back()->with('error', 'Password salah.');
    }


    public function dashboard()
    {
        // tanggal hari ini
        $today = Carbon::today();

        // total pasien hari ini
        $totalPasienHariIni = DataPasien::whereDate('created_at', $today)->count();

        // total pasien belum diperiksa
        $totalBelumDiperiksa = DataPasien::where('status_pemeriksaan', 'Belum Diperiksa')->count();

        // total pasien selesai diperiksa
        $totalSelesaiDiperiksa = DataPasien::where('status_pemeriksaan', 'Selesai Diperiksa')->count();

        return view('dokter.dashboard', compact(
            'totalPasienHariIni',
            'totalBelumDiperiksa',
            'totalSelesaiDiperiksa'
        ));
    }

    public function dataPasienScheduled(Request $request)
    {
        $dokter = Auth::guard('dokter')->user();
        
        $today = Carbon::today()->toDateString();
        
        // Ambil nilai filter dari request
        $filterDate = $request->query('date');
        $filterStatusBerkas = $request->query('status_berkas');
        $filterStatusPemeriksaan = $request->query('status_pemeriksaan'); // Filter baru
        $filterNamaPasien = $request->query('nama_pasien'); // Filter baru

        // =======================================================
        // 1. Data Pasien HARI INI
        // =======================================================
        $queryHariIni = DataPasien::with(['layanan', 'waktu'])
            ->whereDate('tgl_kunjungan', $today)
            ->orderBy('waktu_id', 'asc');

        // Filter Status Pemeriksaan (Hari Ini)
        if ($filterStatusPemeriksaan && in_array($filterStatusPemeriksaan, ['Belum Diperiksa', 'Sudah Diperiksa'])) {
            $queryHariIni->where('status_pemeriksaan', $filterStatusPemeriksaan);
        }

        // Filter Nama Pasien (Hari Ini)
        if ($filterNamaPasien) {
            $queryHariIni->where('nama_pasien', 'like', '%' . $filterNamaPasien . '%');
        }

        $pasienHariIni = $queryHariIni->get();

        // =======================================================
        // 2. Data Pasien MENDATANG
        // =======================================================
        $queryMendatang = DataPasien::with(['layanan', 'waktu'])
            ->where('tgl_kunjungan', '>', $today) // Ambil semua data setelah hari ini
            ->orderBy('tgl_kunjungan', 'asc')
            ->orderBy('waktu_id', 'asc');
            
        // --- FILTER TANGGAL KHUSUS (Jika diminta) ---
        if ($filterDate && $filterDate > $today) {
            $queryMendatang->whereDate('tgl_kunjungan', $filterDate);
        }
        
        // --- FILTER STATUS BERKAS ---
        if ($filterStatusBerkas && in_array($filterStatusBerkas, ['Belum Diverifikasi', 'Sudah Diverifikasi'])) {
            $queryMendatang->where('status_berkas', $filterStatusBerkas);
        }
        
        // Filter Status Pemeriksaan (Mendatang)
        if ($filterStatusPemeriksaan && in_array($filterStatusPemeriksaan, ['Belum Diperiksa', 'Sudah Diperiksa'])) {
            $queryMendatang->where('status_pemeriksaan', $filterStatusPemeriksaan);
        }

        // Filter Nama Pasien (Mendatang)
        if ($filterNamaPasien) {
            $queryMendatang->where('nama_pasien', 'like', '%' . $filterNamaPasien . '%');
        }

        $pasienMendatang = $queryMendatang->get();
        
        // Ambil daftar unik tanggal mendatang untuk dropdown filter
        $availableDates = DataPasien::select('tgl_kunjungan')
                                ->where('tgl_kunjungan', '>', $today)
                                // Hapus filter status_pemeriksaan di sini agar menampilkan semua tanggal yang tersedia
                                ->distinct()
                                ->orderBy('tgl_kunjungan', 'asc')
                                ->pluck('tgl_kunjungan');
        
        // Definisikan filter saat ini
        $currentFilterDate = $filterDate;
        $currentFilterStatusBerkas = $filterStatusBerkas;
        $currentFilterStatusPemeriksaan = $filterStatusPemeriksaan;
        $currentFilterNamaPasien = $filterNamaPasien;

        

        return view('dokter.data_pasien', compact(
            'dokter', 
            'pasienHariIni', 
            'pasienMendatang', 
            'today', 
            'availableDates',
            'currentFilterDate',
            'currentFilterStatusBerkas',
            'currentFilterStatusPemeriksaan', // Kirim ke view
            'currentFilterNamaPasien' // Kirim ke view
        ));
    }

    public function riwayatPasien(Request $request)
    {
        $dokter = Auth::guard('dokter')->user();
        
        // Ambil nilai filter dari request
        $filterStartDate = $request->query('start_date');
        $filterEndDate = $request->query('end_date');
        $filterStatusPemeriksaan = $request->query('status_pemeriksaan');
        $filterNamaPasien = $request->query('nama_pasien');
        
        // Query dasar
        $query = DataPasien::with(['layanan', 'waktu'])
            ->orderBy('tgl_kunjungan', 'desc')
            ->orderBy('waktu_id', 'asc');
        
        // Filter berdasarkan rentang tanggal
        if ($filterStartDate) {
            $query->whereDate('tgl_kunjungan', '>=', $filterStartDate);
        }
        
        if ($filterEndDate) {
            $query->whereDate('tgl_kunjungan', '<=', $filterEndDate);
        }
        
        // Filter Status Pemeriksaan
        if ($filterStatusPemeriksaan && in_array($filterStatusPemeriksaan, ['Belum Diperiksa', 'Sedang Diperiksa', 'Selesai Diperiksa'])) {
            $query->where('status_pemeriksaan', $filterStatusPemeriksaan);
        }
        
        // Filter Nama Pasien
        if ($filterNamaPasien) {
            $query->where('nama_pasien', 'like', '%' . $filterNamaPasien . '%');
        }
        
        $dataPasien = $query->get();
        
        return view('dokter.riwayat_pasien', compact(
            'dokter',
            'dataPasien',
            'filterStartDate',
            'filterEndDate',
            'filterStatusPemeriksaan',
            'filterNamaPasien'
        ));
    }

    public function getPasienDetail($id)
    {
        $pasien = DataPasien::with(['layanan', 'waktu'])->findOrFail($id);
        $dokterPenanggungJawab = $pasien->dokter->nama_dokter ?? 'Belum Ditentukan';
        
        // Format data yang akan dikirim ke JavaScript
        return response()->json([
            'status' => 'success',
            'data' => [
                'nomor_antrian' => $pasien->nomor_antrian,
                'nama_pasien' => $pasien->nama_pasien,
                'tgl_lahir' => Carbon::parse($pasien->tgl_lahir)->isoFormat('D MMMM YYYY'),
                'jenis_kelamin' => $pasien->jenis_kelamin,
                'nomor_hp' => $pasien->nomor_hp ?? '-',
                'alamat' => $pasien->alamat,
                'pendamping' => $pasien->pendamping ?? '-',
                'layanan' => $pasien->layanan->pelayanan ?? 'N/A',
                'waktu_kunjungan' => $pasien->waktu->jam_mulai . ' - ' . $pasien->waktu->jam_selesai,
                'keluhan' => $pasien->keluhan,
                'kategori_pendaftaran' => $pasien->kategori_pendaftaran,
                'tgl_kunjungan' => Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMMM YYYY'),
                'status_pemeriksaan' => $pasien->status_pemeriksaan,
                'status_berkas' => $pasien->status_berkas,
                'dokter_penanggung_jawab' => $dokterPenanggungJawab,
            ]
        ]);
    }

    /**
     * Memperbarui status berkas pasien.
     */
    public function updateStatusPemeriksaan(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'status_pemeriksaan' => 'required|in:Belum Diperiksa,Sedang Diperiksa,Selesai Diperiksa',
        ]);

        // 2. Ambil ID Dokter yang sedang Login
        // Ini akan mengambil ID dari Model Dokter yang sedang login (ID di tabel 'dokters')
        $dokterId = Auth::id(); 

        // 3. Cari Data Pasien
        $pasien = DataPasien::findOrFail($id);
        $newStatus = $request->status_pemeriksaan;

        // 4. Logika Penetapan/Penghapusan Dokter
        
        $statusChangeMessage = "Status pemeriksaan pasien {$pasien->nama_pasien} berhasil diperbarui menjadi '{$newStatus}'.";

        if ($newStatus === 'Belum Diperiksa') {
            // PERMINTAAN: Hapus (set null) dokter_id ketika status direset ke Belum Diperiksa
            $pasien->dokter_id = null;
            $statusChangeMessage .= " Dokter penanggung jawab telah direset.";

        } elseif ($newStatus === 'Sedang Diperiksa' || $newStatus === 'Selesai Diperiksa') {
            // PERMINTAAN: Tetapkan dokter_id saat status diubah ke Sedang Diperiksa/Selesai Diperiksa.
            
            // HANYA SET jika dokter_id saat ini NULL.
            // Jika sudah ada dokter yang menangani (dokter_id sudah terisi),
            // kita tidak menimpanya, kecuali ada logika khusus untuk transfer.
            
            if (is_null($pasien->dokter_id)) {
                $pasien->dokter_id = $dokterId;
                $statusChangeMessage .= " dan sekarang ditangani oleh Anda.";
            }
            // Jika status Selesai Diperiksa, dokter_id akan tetap dipertahankan
        }

        // 5. Update Status dan Simpan
        $pasien->status_pemeriksaan = $newStatus;
        $pasien->save();

        return back()->with('success', $statusChangeMessage);
    }

    public function uploadVideos(Request $request, $id)
    {
        $request->validate([
            'video_before' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-flv|max:5120', // Maks 5MB = 5120 KB
            'video_after' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-flv|max:5120', // Maks 5MB
        ], [
            'video_before.max' => 'Ukuran Video Sebelum Pemeriksaan maksimal 5 MB.',
            'video_after.max' => 'Ukuran Video Sesudah Pemeriksaan maksimal 5 MB.',
            'video_before.mimetypes' => 'Format video tidak didukung. Harap unggah MP4 atau MOV.',
            'video_after.mimetypes' => 'Format video tidak didukung. Harap unggah MP4 atau MOV.',
        ]);

        $pasien = DataPasien::findOrFail($id);
        $updateData = [];

        // 1. Proses Unggah Video Before
        if ($request->hasFile('video_before')) {
            // Hapus video lama jika ada
            if ($pasien->video_before) {
                \Storage::disk('public')->delete($pasien->video_before);
            }
            $path = $request->file('video_before')->store('video_before', 'public');
            $updateData['video_before'] = $path;
        }

        // 2. Proses Unggah Video After
        if ($request->hasFile('video_after')) {
            // Hapus video lama jika ada
            if ($pasien->video_after) {
                \Storage::disk('public')->delete($pasien->video_after);
            }
            $path = $request->file('video_after')->store('video_after', 'public');
            $updateData['video_after'] = $path;
        }

        if (!empty($updateData)) {
            $pasien->update($updateData);
            return back()->with('success', 'Video pasien berhasil diunggah/diperbarui.');
        }

        return back()->with('warning', 'Tidak ada file video yang diunggah atau diperbarui.');
    }

    public function deleteVideo(Request $request, $id)
    {
        $request->validate([
            'video_type' => 'required|in:video_before,video_after',
        ]);

        $pasien = DataPasien::findOrFail($id);
        $videoType = $request->video_type;
        $videoPath = $pasien->$videoType;

        if ($videoPath) {
            // Hapus file dari storage
            Storage::disk('public')->delete($videoPath);

            // Update path di database menjadi NULL
            $pasien->$videoType = null;
            $pasien->save();

            $videoLabel = ($videoType == 'video_before') ? 'Sebelum Pemeriksaan' : 'Sesudah Pemeriksaan';
            return back()->with('success', "Video {$videoLabel} pasien berhasil dihapus.");
        }

        return back()->with('warning', 'Video tidak ditemukan.');
    }

    // ... di dalam DataPasienController

/**
 * Mendapatkan catatan pemeriksaan/obat (digunakan AJAX untuk mengisi modal).
 */
    public function getCatatan(Request $request, $id)
    {
        $pasien = DataPasien::findOrFail($id);
        
        // Asumsikan data yang dikirim via AJAX hanya butuh ID pasien
        return response()->json([
            'status' => 'success',
            'data' => [
                'catatan_pemeriksaan' => $pasien->catatan_pemeriksaan ?? '',
                'catatan_obat' => $pasien->catatan_obat ?? '',
                'id' => $pasien->id
            ]
        ]);
    }

    /**
     * Menyimpan atau Memperbarui Catatan Pemeriksaan/Obat (Menggunakan AJAX).
     */
    public function updateCatatan(Request $request, $id)
    {
        $pasien = DataPasien::findOrFail($id);

        $request->validate([
            'catatan_pemeriksaan' => 'nullable|string',
            'catatan_obat' => 'nullable|string',
        ]);

        $pasien->catatan_pemeriksaan = $request->catatan_pemeriksaan;
        $pasien->catatan_obat = $request->catatan_obat;
        $pasien->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Catatan berhasil diperbarui!',
            'data' => [
                'catatan_pemeriksaan' => $pasien->catatan_pemeriksaan,
                'catatan_obat' => $pasien->catatan_obat,
            ]
        ]);
    }

    /**
     * Menghapus Catatan Pemeriksaan/Obat (Menggunakan AJAX).
     */
    public function deleteCatatan(Request $request, $id)
    {
        $pasien = DataPasien::findOrFail($id);

        $request->validate([
            'field' => 'required|in:catatan_pemeriksaan,catatan_obat',
        ]);
        
        $fieldName = ($request->field == 'catatan_pemeriksaan') ? 'Pemeriksaan' : 'Obat';

        $pasien->{$request->field} = null;
        $pasien->save();

        return response()->json([
            'status' => 'success',
            'message' => "Catatan {$fieldName} berhasil dihapus.",
            'field' => $request->field,
        ]);
    }


    public function logout(Request $request)
    {
        auth()->guard('dokter')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('dokter.login')->with('success', 'Anda berhasil logout.');
    }

}

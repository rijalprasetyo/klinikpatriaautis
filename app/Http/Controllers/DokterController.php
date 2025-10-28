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
        // $dokterId = $dokter->id; // DIHAPUS karena permintaan menampilkan semua data
        
        $today = Carbon::today()->toDateString();
        
        // Ambil nilai filter dari request
        $filterDate = $request->query('date');
        $filterStatusBerkas = $request->query('status_berkas');
        $filterStatusPemeriksaan = $request->query('status_pemeriksaan'); 
        $filterNamaPasien = $request->query('nama_pasien'); 

        // =======================================================
        // 1. Data Pasien HARI INI
        // =======================================================
        // KOREKSI: Hapus 'layanan'. Hapus filter dokter_id.
        $queryHariIni = DataPasien::with(['waktu'])
            // Hapus: ->where('dokter_id', $dokterId)
            ->whereDate('tgl_kunjungan', $today)
            ->orderBy('waktu_id', 'asc');

        // Filter Status Pemeriksaan (Hari Ini)
        if ($filterStatusPemeriksaan && in_array($filterStatusPemeriksaan, ['Belum Diperiksa', 'Sedang Diperiksa', 'Selesai Diperiksa'])) {
            $queryHariIni->where('status_pemeriksaan', $filterStatusPemeriksaan);
        } else {
            // Default: Tampilkan Belum Diperiksa dan Sedang Diperiksa untuk hari ini
            $queryHariIni->whereIn('status_pemeriksaan', ['Belum Diperiksa', 'Sedang Diperiksa']);
        }

        // Filter Nama Pasien (Hari Ini)
        if ($filterNamaPasien) {
            $queryHariIni->where('nama_pasien', 'like', '%' . $filterNamaPasien . '%');
        }

        $pasienHariIni = $queryHariIni->get();

        // =======================================================
        // 2. Data Pasien MENDATANG
        // =======================================================
        // KOREKSI: Hapus 'layanan'. Hapus filter dokter_id.
        $queryMendatang = DataPasien::with(['waktu'])
            // Hapus: ->where('dokter_id', $dokterId)
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
        if ($filterStatusPemeriksaan && in_array($filterStatusPemeriksaan, ['Belum Diperiksa', 'Sedang Diperiksa', 'Selesai Diperiksa'])) {
            $queryMendatang->where('status_pemeriksaan', $filterStatusPemeriksaan);
        }

        // Filter Nama Pasien (Mendatang)
        if ($filterNamaPasien) {
            $queryMendatang->where('nama_pasien', 'like', '%' . $filterNamaPasien . '%');
        }

        $pasienMendatang = $queryMendatang->get();
        
        // Ambil daftar unik tanggal mendatang untuk dropdown filter
        $availableDates = DataPasien::select('tgl_kunjungan')
            // Hapus: ->where('dokter_id', $dokterId)
            ->where('tgl_kunjungan', '>', $today)
            ->distinct()
            ->orderBy('tgl_kunjungan', 'asc')
            ->pluck('tgl_kunjungan');
        
        // Definisikan filter saat ini
        $currentFilterDate = $filterDate;
        $currentFilterStatusBerkas = $filterStatusBerkas;
        $currentFilterStatusPemeriksaan = $filterStatusPemeriksaan;
        $currentFilterNamaPasien = $filterNamaPasien;

        if (!$filterStatusPemeriksaan) {
            $currentFilterStatusPemeriksaan = 'Belum Diperiksa/Sedang Diperiksa';
        }
        

        return view('dokter.data_pasien', compact(
            'dokter', 
            'pasienHariIni', 
            'pasienMendatang', 
            'today', 
            'availableDates',
            'currentFilterDate',
            'currentFilterStatusBerkas',
            'currentFilterStatusPemeriksaan',
            'currentFilterNamaPasien'
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
        
        // KOREKSI: Hapus 'layanan' dari with(). Relasi waktu tetap.
        $query = DataPasien::with(['waktu'])
            ->orderBy('tgl_kunjungan', 'desc')
            ->orderBy('waktu_id', 'asc');
        
        // Filter berdasarkan rentang tanggal
        if ($filterStartDate) {
            $query->whereDate('tgl_kunjungan', '>=', $filterStartDate);
        }
        
        if ($filterEndDate) {
            $query->whereDate('tgl_kunjungan', '<=', $filterEndDate);
        }
        
        // Filter Status Pemeriksaan (KOREKSI LOGIKA)
        // Jika $filterStatusPemeriksaan tidak kosong, terapkan filter tersebut.
        if ($filterStatusPemeriksaan && in_array($filterStatusPemeriksaan, ['Belum Diperiksa', 'Sedang Diperiksa', 'Selesai Diperiksa'])) {
            $query->where('status_pemeriksaan', $filterStatusPemeriksaan);
        } else {
            // Jika filter status kosong, TAMPILKAN SEMUA STATUS.
            // Kita tidak menambahkan klausa where status_pemeriksaan di sini.
            // Cukup pastikan variabel filter yang dikirim ke view adalah null/kosong.
            $filterStatusPemeriksaan = null; // Reset filter yang dikirim ke view jika tujuannya SEMUA
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
            'filterStatusPemeriksaan', // Nilai ini kini bisa null, yang berarti 'Semua Status'
            'filterNamaPasien'
        ));
    }

    // FUNGSI INI ADALAH SUMBER DARI ERROR 500 & JSON PARSE ERROR
    public function getPasienDetail($id)
    {
        // KOREKSI 1: Hapus relasi 'layanan'. Tambahkan relasi 'dokter'.
        // Jika relasi 'dokter' sudah ada di model, ini akan memuatnya.
        $pasien = DataPasien::with(['waktu', 'dokter'])->findOrFail($id); 
        
        // KOREKSI 2: Menggunakan Null Coalescing Operator (?) untuk Dokter
        // Mencegah Error 500 jika pasien belum memiliki dokter_id
        $dokterPenanggungJawab = $pasien->dokter?->nama_dokter ?? 'Belum Ditentukan';
        
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
                
                // KOREKSI 3: Mengambil nama layanan dari kolom layanan_id (string)
                'layanan' => $pasien->layanan_id ?? 'N/A', 
                
                'waktu_kunjungan' => $pasien->waktu->jam_mulai . ' - ' . $pasien->waktu->jam_selesai,
                'keluhan' => $pasien->keluhan,
                'kategori_pendaftaran' => $pasien->kategori_pendaftaran,
                'tgl_kunjungan' => Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMMM YYYY'),
                'status_pemeriksaan' => $pasien->status_pemeriksaan,
                'status_berkas' => $pasien->status_berkas,
                'dokter_nama' => $dokterPenanggungJawab, // Digunakan di JS sebagai d.dokter_nama
            ]
        ]);
    }

    // --- FUNGSI UPDATE STATUS PEMERIKSAAN ---
    public function updateStatusPemeriksaan(Request $request, $id)
    {
        // 1. Validasi Input
        $request->validate([
            'status_pemeriksaan' => 'required|in:Belum Diperiksa,Sedang Diperiksa,Selesai Diperiksa',
        ]);

        // 2. Ambil ID Dokter yang sedang Login
        $dokterId = Auth::id(); 

        // 3. Cari Data Pasien
        $pasien = DataPasien::findOrFail($id);
        $newStatus = $request->status_pemeriksaan;

        // 4. Logika Penetapan/Penghapusan Dokter
        $statusChangeMessage = "Status pemeriksaan pasien {$pasien->nama_pasien} berhasil diperbarui menjadi '{$newStatus}'.";

        if ($newStatus === 'Belum Diperiksa') {
            // Hapus (set null) dokter_id ketika status direset ke Belum Diperiksa
            $pasien->dokter_id = null;
            $statusChangeMessage .= " Dokter penanggung jawab telah direset.";

        } elseif ($newStatus === 'Sedang Diperiksa' || $newStatus === 'Selesai Diperiksa') {
            
            // HANYA SET jika dokter_id saat ini NULL.
            if (is_null($pasien->dokter_id)) {
                $pasien->dokter_id = $dokterId;
                $statusChangeMessage .= " dan sekarang ditangani oleh Anda.";
            }
            // Jika sudah ada dokter yang menangani, dokter_id dipertahankan
        }

        // 5. Update Status dan Simpan
        $pasien->status_pemeriksaan = $newStatus;
        $pasien->save();

        // KARENA INI DIPANGGIL OLEH FORM SUBMIT BIASA, RETURN BACK.
        return back()->with('success', $statusChangeMessage);
    }

    public function uploadVideos(Request $request, $id)
    {
        $request->validate([
            'video_before' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-flv|max:25600', // Maks 5MB = 5120 KB
            'video_after' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-flv|max:25600', // Maks 5MB
        ], [
            'video_before.max' => 'Ukuran Video Sebelum Pemeriksaan maksimal 25 MB.',
            'video_after.max' => 'Ukuran Video Sesudah Pemeriksaan maksimal 25 MB.',
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

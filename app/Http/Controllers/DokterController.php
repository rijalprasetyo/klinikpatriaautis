<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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
        $filterStatusPemeriksaan = $request->query('status_pemeriksaan'); 
        $filterNamaPasien = $request->query('nama_pasien'); 

        // Daftar status berkas yang HANYA diizinkan untuk ditampilkan
        $allowedStatusBerkas = ['Belum Diverifikasi', 'Sudah Diverifikasi'];

        // =======================================================
        // 1. Data Pasien HARI INI
        // =======================================================
        $queryHariIni = DataPasien::with(['waktu'])
            ->whereDate('tgl_kunjungan', $today)
            // HANYA tampilkan data dengan status berkas yang diizinkan
            ->whereIn('status_berkas', $allowedStatusBerkas) 
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
        $queryMendatang = DataPasien::with(['waktu'])
            ->where('tgl_kunjungan', '>', $today) // Ambil semua data setelah hari ini
            // HANYA tampilkan data dengan status berkas yang diizinkan
            ->whereIn('status_berkas', $allowedStatusBerkas)
            ->orderBy('tgl_kunjungan', 'asc')
            ->orderBy('waktu_id', 'asc');
            
        // --- FILTER TANGGAL KHUSUS (Jika diminta) ---
        if ($filterDate && $filterDate > $today) {
            $queryMendatang->whereDate('tgl_kunjungan', $filterDate);
        }
        
        // --- FILTER STATUS BERKAS ---
        if ($filterStatusBerkas && in_array($filterStatusBerkas, $allowedStatusBerkas)) {
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
        // Tambahkan filter status berkas ke sini juga
        $availableDates = DataPasien::select('tgl_kunjungan')
            ->where('tgl_kunjungan', '>', $today)
            ->whereIn('status_berkas', $allowedStatusBerkas) // Memastikan tanggal yang tersedia juga disaring
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
        
        // Status Berkas yang Diizinkan untuk Tampil (Wajib)
        $allowedStatusBerkas = ['Belum Diverifikasi', 'Sudah Diverifikasi'];

        // KOREKSI: Hapus 'layanan' dari with(). Relasi waktu tetap.
        $query = DataPasien::with(['waktu'])
            // WAJIB: Hanya tampilkan pasien dengan status berkas yang diverifikasi
            ->whereIn('status_berkas', $allowedStatusBerkas) 
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
        } else {
            // Jika filter status kosong, TAMPILKAN SEMUA STATUS PEMERIKSAAN.
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
            'video_before' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-flv,image/jpeg,image/png,image/gif,image/heic,image/heif|max:25600',
            'video_after' => 'nullable|file|mimetypes:video/mp4,video/quicktime,video/x-flv,image/jpeg,image/png,image/gif,image/heic,image/heif|max:25600',
        ], [
            'video_before.max' => 'Ukuran File (Video/Foto) Sebelum Pemeriksaan maksimal 25 MB. (Pastikan foto tidak melebihi 2.5MB).',
            'video_after.max' => 'Ukuran File (Video/Foto) Sesudah Pemeriksaan maksimal 25 MB. (Pastikan foto tidak melebihi 2.5MB).',
            'video_before.mimetypes' => 'Format file tidak didukung. Harap unggah MP4/MOV atau JPEG/PNG/HEIC.',
            'video_after.mimetypes' => 'Format file tidak didukung. Harap unggah MP4/MOV atau JPEG/PNG/HEIC.',
        ]);

        $pasien = DataPasien::findOrFail($id);
        $updateData = [];
        $destination = public_path('storage');
        $handleUpload = function ($file, $type) use ($pasien, $destination, &$updateData) {
            // Ambil tipe MIME file yang diupload untuk penyesuaian ukuran
            $mimeType = $file->getMimeType();
            $fileSizeKB = $file->getSize() / 1024;
            $isVideo = str_starts_with($mimeType, 'video/');

            // Lakukan validasi ukuran spesifik untuk foto di sini (karena validasi Laravel 'max' global)
            if (!$isVideo && $fileSizeKB > 2500) { // Jika itu foto dan > 2.5MB (2500KB)
                throw new \Illuminate\Validation\ValidationException(\Illuminate\Validation\ValidationFactory::make(
                    [],
                    [$type => 'max:2500'],
                    [$type . '.max' => 'Ukuran file foto maksimal 2.5 MB.']
                ), null, $type);
            }

            // Hapus file lama (foto/video)
            $oldFile = $pasien->$type;
            if ($oldFile) {
                @unlink(public_path('storage/' . $oldFile));
            }

            $folderName = ($type == 'video_before') ? 'video_before' : 'video_after';
            $filename = uniqid() . '.' . $file->getClientOriginalExtension();

            // Pindahkan file
            $file->move($destination . '/' . $folderName, $filename);

            $updateData[$type] = $folderName . '/' . $filename;
        };


        // === Video/Photo Before ===
        if ($request->hasFile('video_before')) {
            try {
                $handleUpload($request->file('video_before'), 'video_before');
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()->withInput()->withErrors($e->errors());
            }
        }

        // === Video/Photo After ===
        if ($request->hasFile('video_after')) {
            try {
                $handleUpload($request->file('video_after'), 'video_after');
            } catch (\Illuminate\Validation\ValidationException $e) {
                return back()->withInput()->withErrors($e->errors());
            }
        }


        if (!empty($updateData)) {
            $pasien->update($updateData);
            return back()->with('success', 'File (Video/Foto) pasien berhasil diunggah/diperbarui.');
        }

        return back()->with('warning', 'Tidak ada file yang diunggah atau diperbarui.');
    }

    // Fungsi deleteVideo tidak perlu diubah karena sudah menggunakan Storage::disk('public')->delete.
    public function deleteVideo(Request $request, $id)
    {
        // ... (kode fungsi deleteVideo tetap sama) ...
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
            return back()->with('success', "File {$videoLabel} pasien berhasil dihapus.");
        }

        return back()->with('warning', 'File tidak ditemukan.');
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

    public function biodata()
    {
        $dokter = Auth::guard('dokter')->user();
        return view('dokter.biodata', compact('dokter'));
    }

    // Fungsi Update Biodata (TIDAK BERUBAH)
    public function updateBiodata(Request $request)
    {
        $dokter = Auth::guard('dokter')->user();

        $request->validate([
            'nama_dokter' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:dokters,username,' . $dokter->id,
            'email' => 'required|string|email|max:255|unique:dokters,email,' . $dokter->id,
            'no_hp' => 'nullable|string|max:15',
            'alamat' => 'nullable|string',
        ], [
            'username.unique' => 'Username ini sudah digunakan oleh dokter lain.',
            'email.unique' => 'Email ini sudah digunakan oleh dokter lain.',
        ]);

        $dokter->update([
            'nama_dokter' => $request->nama_dokter,
            'username' => $request->username,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'alamat' => $request->alamat,
        ]);

        return redirect()->route('dokter.biodata')->with('success', 'Biodata berhasil diperbarui!');
    }

    /**
     * Mengupdate password dokter (TANPA PASSWORD LAMA).
     */
    public function updatePassword(Request $request)
    {
        $dokter = Auth::guard('dokter')->user();

        // 1. Validasi Input (current_password dihapus)
        $request->validate([
            'username_konfirmasi' => 'required|string',
            'email_konfirmasi' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // 2. Verifikasi Username dan Email
        // Ini berfungsi sebagai lapisan konfirmasi identitas tanpa menggunakan password lama
        if ($request->username_konfirmasi !== $dokter->username || $request->email_konfirmasi !== $dokter->email) {
            return back()->withErrors(['username_konfirmasi' => 'Username atau Email konfirmasi tidak cocok dengan akun Anda.'])->withInput()->with('error', 'Gagal memperbarui password. Verifikasi akun gagal.');
        }

        // 3. Update Password
        $dokter->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('dokter.biodata')->with('success', 'Password berhasil diperbarui!');
    }

}

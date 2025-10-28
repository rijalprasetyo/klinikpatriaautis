<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\DataPasien;
use App\Models\JenisPelayanan;
use App\Models\User;
use App\Models\JamPelayanan;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class UserController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.user-login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            return redirect()->route('home');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function home()
    {
        $user = Auth::user(); 

        $jadwal = DataPasien::with(['waktu', 'layanan'])
            ->where('user_id', $user->id)
            ->whereIn('status_pemeriksaan', ['Belum Diperiksa', 'Sedang Diperiksa'])
            ->orderBy('tgl_kunjungan', 'asc')
            ->get();

        $jam_operasional = JamPelayanan::all();
        $layanan = JenisPelayanan::all();

        return view('home', compact('user', 'jadwal', 'jam_operasional', 'layanan'));
    }

    public function success($antrian, Request $request)
    {
        // Ambil ID pasien dari URL query parameter (misal: .../success/001?id=5)
        $pasienId = $request->query('id'); 
        
        // Kita meneruskan $pasienId ke view
        return view('pendaftaran.success', compact('antrian', 'pasienId'));
    }

    // ... di dalam class PendaftaranController extends Controller
    
    public function create(Request $request)
    {
        $kategori = $request->query('kategori', 'Masyarakat Umum');
        
        // Ambil semua jenis pelayanan
        $semuaJenisPelayanan = JenisPelayanan::all();
        $jamPelayanan = JamPelayanan::all();

        // Filter jenis pelayanan jika kategori BUKAN Masyarakat Umum
        if ($kategori !== 'Masyarakat Umum') {
            // Hapus layanan dengan ID 1 ("Non-Disabilitas")
            $jenisPelayanan = $semuaJenisPelayanan->filter(function ($layanan) {
                return $layanan->id != 1;
            });
        } else {
            // Jika Masyarakat Umum, kita tidak peduli karena select-nya tersembunyi
            $jenisPelayanan = $semuaJenisPelayanan;
        }

        return view('create', compact('kategori', 'jenisPelayanan', 'jamPelayanan'));
    }

    public function store(Request $request)
    {
        // Tentukan aturan validasi dasar
        $rules = [
            'nama_pasien' => 'required|string|max:255',
            'tgl_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'nomor_hp' => 'nullable|string|max:20',
            'alamat' => 'required|string',
            'pendamping' => 'required|string|max:255',
            'keluhan' => 'required|string',
            'tgl_kunjungan' => 'required|date|after_or_equal:today',
            'waktu_id' => 'required|integer',
            'bukti_pembayaran' => 'required|file|mimes:jpg,png,pdf|max:1024',
            'kategori' => 'required|string|max:255',
        ];
        
        // Validasi kondisional berdasarkan kategori
        if ($request->kategori === 'Disabilitas (Dengan SKTM)') {
            // Wajib untuk SKTM
            $rules['layanan_id'] = 'required|integer';
            $rules['sktm'] = 'required|file|mimes:jpg,png,pdf|max:1024';
        } elseif ($request->kategori === 'Disabilitas (Non-SKTM)') {
            // Wajib untuk Non-SKTM (meski SKTM tidak wajib)
            $rules['layanan_id'] = 'required|integer';
            $rules['sktm'] = 'nullable|file|mimes:jpg,png,pdf|max:1024';
        } else {
            // Untuk Masyarakat Umum
            $rules['layanan_id'] = 'nullable|integer';
            $rules['sktm'] = 'nullable|file|mimes:jpg,png,pdf|max:1024';
        }

        try {
            // Jalankan validasi
            $request->validate($rules);

            $tglKunjungan = Carbon::parse($request->tgl_kunjungan);
            
            // --- 1. Validasi Hari Jumat ---
            if ($tglKunjungan->dayOfWeek === Carbon::FRIDAY) {
                return back()->with('error', 'Pendaftaran gagal! Kunjungan tidak tersedia pada hari Jumat.')->withInput();
            }

            // --- 2. Validasi Kuota Pasien (Max 3 per Jam) ---
            $kuotaMax = 3;
            $countKuotabias = DataPasien::whereDate('tgl_kunjungan', $tglKunjungan->toDateString())
                ->where('waktu_id', $request->waktu_id)
                ->count();

            if ($countKuotabias >= $kuotaMax) {
                return back()->with('error', 'Pendaftaran gagal! Kuota pasien untuk jam tersebut pada tanggal ini sudah penuh (Max ' . $kuotaMax . ' orang). Mohon pilih jam atau tanggal lain.')->withInput();
            }

            // --- 3. Tentukan Layanan ID ---
            $layananId = $request->layanan_id;
            if ($request->kategori === 'Masyarakat Umum') {
                // HANYA UNTUK MASYARAKAT UMUM: Set ID Layanan secara otomatis (ASUMSI: ID 1)
                $layananId = 1; 
            }

            // --- 4. Proses Penyimpanan Data ---
            
            // Perhitungan Nomor Antrian Harian Berdasarkan TGL KUNJUNGAN
            $countPadaTglKunjungan = DataPasien::whereDate('tgl_kunjungan', $tglKunjungan->toDateString())->count();
            $nomorAntrianHarian = str_pad($countPadaTglKunjungan + 1, 3, '0', STR_PAD_LEFT);

            // Upload File
            $buktiPath = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');
            $sktmPath = ($request->kategori === 'Disabilitas (Dengan SKTM)' && $request->hasFile('sktm'))
                ? $request->file('sktm')->store('sktm', 'public')
                : null;

            // Simpan ke Database
            $pasien = DataPasien::create([
                'nomor_antrian' => $nomorAntrianHarian,
                'nama_pasien' => $request->nama_pasien,
                'tgl_lahir' => $request->tgl_lahir,
                'jenis_kelamin' => $request->jenis_kelamin,
                'nomor_hp' => $request->nomor_hp,
                'alamat' => $request->alamat,
                'pendamping' => $request->pendamping,
                'kategori_pendaftaran' => $request->kategori,
                'layanan_id' => $layananId,
                'waktu_id' => $request->waktu_id,
                'keluhan' => $request->keluhan,
                'tgl_kunjungan' => $request->tgl_kunjungan,
                'bukti_pembayaran' => $buktiPath,
                'sktm' => $sktmPath,
                'status_pemeriksaan' => 'Belum Diperiksa',
                'status_berkas' => 'Belum Diverifikasi',
                'user_id' => Auth::id(),
            ]);

            // Redirect ke Halaman Sukses
            return redirect()->route('pendaftaran.success', [
                'antrian' => $nomorAntrianHarian,
                'id' => $pasien->id,
            ])->with([
                'success' => 'Pendaftaran berhasil! Nomor antrian Anda: ' . $nomorAntrianHarian,
                'nomor_antrian' => $nomorAntrianHarian,
            ]);

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Validation\ValidationException) {
                 return back()->withErrors($e->errors())->withInput();
            }
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage())->withInput();
        }
    }
// ...

    public function downloadPdf($id)
    {
        $pasien = DataPasien::with(['layanan', 'waktu'])->findOrFail($id);
        
        // Buat ID unik untuk nama file
        $tglKunjungan = Carbon::parse($pasien->tgl_kunjungan);
        $datePrefix = $tglKunjungan->format('ymd');
        $uniquePendaftaranId = $datePrefix . $pasien->nomor_antrian;

        // Generate PDF on the fly (TIDAK DISIMPAN DI SERVER)
        $pdf = Pdf::loadView('pdf.pendaftaran', compact('pasien'));
        
        $fileName = 'Tiket_Antrian_' . $uniquePendaftaranId . '.pdf';

        // PDF langsung di-download
        return $pdf->download($fileName);
    }
    public function biodata()
    {
        $user = Auth::user(); 
        // Kirim status apakah pengguna login via Google
        $isGoogleUser = !is_null($user->google_id); 
        
        return view('biodata', compact('user', 'isGoogleUser'));
    }

    public function updateBiodata(Request $request)
    {
        $user = Auth::user();
        
        // Tentukan aturan validasi
        $rules = [
            'name' => 'required|string|max:255',
            'alamat' => 'nullable|string|max:255',
            'tgl_lahir' => 'nullable|date',
            'no_hp' => 'nullable|string|max:20',
        ];

        // Jika pengguna TIDAK login via Google (yaitu login email/password biasa)
        if (is_null($user->google_id)) {
            // Izinkan email diganti, pastikan unik kecuali untuk email saat ini
            $rules['email'] = 'required|email|max:255|unique:users,email,' . $user->id;
        } 
        // Jika login via Google, email akan diabaikan/tetap pada nilai lama karena tidak ada di $request

        $validatedData = $request->validate($rules);
        
        // Update data pengguna
        $user->name = $validatedData['name'];
        $user->alamat = $validatedData['alamat'] ?? $user->alamat; // Gunakan nilai lama jika kosong
        $user->tgl_lahir = $validatedData['tgl_lahir'] ?? $user->tgl_lahir;
        $user->no_hp = $validatedData['no_hp'] ?? $user->no_hp;

        // Hanya update email jika bukan Google user dan ada di request
        if (is_null($user->google_id) && $request->has('email')) {
            $user->email = $validatedData['email'];
        }

        $user->save();

        return back()->with('success', 'Biodata berhasil diperbarui.');
    }

    public function riwayatPasien()
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return redirect('/login'); // Arahkan ke halaman login jika belum
        }

        $userId = Auth::id();

        // 1. Ambil data pasien yang user_id-nya sesuai dan statusnya 'Selesai Diperiksa'
        $dataPasien = DataPasien::where('user_id', $userId)
            ->where('status_pemeriksaan', 'Selesai Diperiksa') // HANYA STATUS SELESAI
            ->with(['layanan', 'dokter']) 
            ->orderBy('tgl_kunjungan', 'desc')
            ->get();

        return view('riwayat_pasien', compact('dataPasien'));
    }

    /**
     * Mengambil detail data pasien untuk modal.
     */
    public function getDetailPasien($id)
    {
        $userId = Auth::id();

        $pasien = DataPasien::where('id', $id)
            ->where('user_id', $userId) // Pasien hanya bisa melihat data miliknya
            ->with(['layanan', 'waktu', 'dokter'])
            ->first();

        if (!$pasien) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        // Format data untuk ditampilkan di modal
        $data = [
            'nomor_antrian' => $pasien->nomor_antrian,
            'nama_pasien' => $pasien->nama_pasien, // Ditambahkan kembali untuk detail
            'tgl_lahir' => Carbon::parse($pasien->tgl_lahir)->isoFormat('D MMMM YYYY'),
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'nomor_hp' => $pasien->nomor_hp ?? '-',
            'alamat' => $pasien->alamat,
            'pendamping' => $pasien->pendamping ?? '-',
            'kategori_pendaftaran' => $pasien->kategori_pendaftaran,
            'layanan' => $pasien->layanan->pelayanan ?? 'N/A',
            // Tambahkan nama dokter yang menangani
            'dokter_nama' => $pasien->dokter->nama_dokter ?? 'Belum Ditentukan', 
            'tgl_kunjungan' => Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMMM YYYY'),
            'waktu_kunjungan' => ($pasien->waktu->jam_mulai ?? '-') . ' - ' . ($pasien->waktu->jam_selesai ?? '-'),
            'keluhan' => $pasien->keluhan,
            'status_pemeriksaan' => $pasien->status_pemeriksaan,
            'status_berkas' => $pasien->status_berkas,
        ];

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * Mengambil catatan pemeriksaan untuk modal (Hanya Lihat).
     */
    public function getCatatan($id)
    {
        $userId = Auth::id();
        $pasien = DataPasien::where('id', $id)
            ->where('user_id', $userId)
            ->select('catatan_pemeriksaan', 'catatan_obat')
            ->first();

        if (!$pasien) {
            return response()->json(['message' => 'Data tidak ditemukan.'], 404);
        }

        return response()->json(['status' => 'success', 'data' => $pasien]);
    }

    /**
     * Menyimpan feedback dari Pasien.
     */
    public function submitFeedback(Request $request, $id)
    {
        $userId = Auth::id();
        $pasien = DataPasien::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$pasien) {
            return response()->json(['status' => 'error', 'message' => 'Data pasien tidak ditemukan.'], 404);
        }

        $request->validate([
            'feedback' => 'required|string|max:1000',
        ]);

        try {
            $pasien->feedback = $request->feedback;
            $pasien->save();

            return response()->json(['status' => 'success', 'message' => 'Feedback berhasil disimpan. Terima kasih!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan feedback.'], 500);
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('user.login');
    }
}
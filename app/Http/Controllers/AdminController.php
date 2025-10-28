<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\DataPasien; 
use Carbon\Carbon; 
use App\Models\User;   
use App\Models\Dokter; 
use App\Models\Admin;
use App\Models\JenisPelayanan; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\JamPelayanan;

class AdminController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::guard('admin')->attempt($request->only('username', 'password'))) {
            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['login' => 'Username atau password salah.']);
    }

    public function dashboard()
    {
        $admin = Auth::guard('admin')->user();
        
        $today = Carbon::today()->toDateString();

        // 1. Pasien Hari Ini (Status: Belum Diperiksa)
        $belumDiperiksaHariIni = DataPasien::whereDate('tgl_kunjungan', $today)
                                           ->where('status_pemeriksaan', 'Belum Diperiksa')
                                           ->count();

        // 2. Berkas Belum Diverifikasi (Status: Belum Diverifikasi, TIDAK TERBATAS HARI INI)
        $berkasBelumDiverifikasi = DataPasien::where('status_berkas', 'Belum Diverifikasi')
                                             ->count();

        // 3. Pasien Hari Ini (Status: Selesai Diperiksa)
        $selesaiDiperiksaHariIni = DataPasien::whereDate('tgl_kunjungan', $today)
                                             ->where('status_pemeriksaan', 'Selesai Diperiksa')
                                             ->count();

        return view('admin.dashboard', compact('admin', 'belumDiperiksaHariIni', 'berkasBelumDiverifikasi', 'selesaiDiperiksaHariIni'));
    }

    public function dataPasienScheduled(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        $today = Carbon::today()->toDateString();
        
        // Ambil nilai filter dari request
        $filterDate = $request->query('date');
        $filterStatusBerkas = $request->query('status_berkas');
        $filterStatusPemeriksaan = $request->query('status_pemeriksaan'); 
        $filterNamaPasien = $request->query('nama_pasien'); 

        // =======================================================
        // 1. Data Pasien HARI INI
        // =======================================================
        // PERBAIKAN: Menghapus 'layanan' dari with()
        $queryHariIni = DataPasien::with(['waktu']) 
            ->whereDate('tgl_kunjungan', $today)
            ->orderBy('waktu_id', 'asc');

        // Filter Status Pemeriksaan (Hari Ini)
        if ($filterStatusPemeriksaan && in_array($filterStatusPemeriksaan, ['Belum Diperiksa', 'Sudah Diperiksa', 'Sedang Diperiksa'])) {
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
        // PERBAIKAN: Menghapus 'layanan' dari with()
        $queryMendatang = DataPasien::with(['waktu']) 
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
        if ($filterStatusPemeriksaan && in_array($filterStatusPemeriksaan, ['Belum Diperiksa', 'Sudah Diperiksa', 'Sedang Diperiksa'])) {
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
                                    ->distinct()
                                    ->orderBy('tgl_kunjungan', 'asc')
                                    ->pluck('tgl_kunjungan');
        
        // Definisikan filter saat ini
        $currentFilterDate = $filterDate;
        $currentFilterStatusBerkas = $filterStatusBerkas;
        $currentFilterStatusPemeriksaan = $filterStatusPemeriksaan;
        $currentFilterNamaPasien = $filterNamaPasien;


        return view('admin.data_pasien', compact(
            'admin', 
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

    public function verifikasiBerkas(Request $request)
    {
        $admin = Auth::guard('admin')->user();
        
        // PERBAIKAN 1: Hapus 'layanan' dari with().
        // Query Dasar: Ambil semua pasien yang status_berkas = 'Belum Diverifikasi' (Filter default jika tidak ada filter dari request)
        $queryPasien = DataPasien::with(['waktu'])
            ->orderBy('tgl_kunjungan', 'asc')
            ->orderBy('waktu_id', 'asc');
            
        // --- FILTER STATUS BERKAS ---
        $filterStatusBerkas = $request->query('status_berkas');
        
        // Jika tidak ada filter dari request, set default 'Belum Diverifikasi'.
        // Jika ada filter, terapkan filter tersebut.
        if ($filterStatusBerkas && in_array($filterStatusBerkas, ['Belum Diverifikasi', 'Sudah Diverifikasi'])) {
            $queryPasien->where('status_berkas', $filterStatusBerkas);
        } else {
            // Jika tidak ada filter yang diberikan oleh pengguna, tampilkan yang 'Belum Diverifikasi' secara default
            $queryPasien->where('status_berkas', 'Belum Diverifikasi');
            $filterStatusBerkas = 'Belum Diverifikasi'; // Set nilai current filter
        }

        // --- FILTER KATEGORI PENDAFTARAN ---
        $filterKategori = $request->query('kategori');
        if ($filterKategori) {
            $queryPasien->where('kategori_pendaftaran', $filterKategori);
        }

        $pasienVerifikasi = $queryPasien->get();
        
        // PERBAIKAN 2: Mengambil daftar status UNIK dari SEMUA data (tanpa membatasi ke 'Belum Diverifikasi')
        // agar filter dropdown tetap menampilkan 'Sudah Diverifikasi' jika itu adalah status yang mungkin dicari.
        $availableStatus = DataPasien::select('status_berkas')
            ->distinct()
            ->pluck('status_berkas');

        // PERBAIKAN 3: Mengambil daftar KATEGORI UNIK dari SEMUA data (tanpa membatasi status)
        $availableKategori = DataPasien::select('kategori_pendaftaran')
            // Tampilkan semua kategori yang ada di sistem, terlepas dari status berkas saat ini
            ->distinct()
            ->pluck('kategori_pendaftaran');
        
        // Definisikan nilai filter saat ini
        // Nilai $filterStatusBerkas sudah diatur di logika if/else di atas.
        $currentStatusBerkas = $filterStatusBerkas;
        $currentKategori = $filterKategori;

        return view('admin.verifikasi_berkas', compact(
            'admin', 
            'pasienVerifikasi', 
            'availableStatus',
            'availableKategori',
            'currentStatusBerkas',
            'currentKategori'
        ));
    }
    
    public function getPasienDetail($id)
    {
        // PERBAIKAN: Hapus 'layanan' dari with(). Relasi ini sudah tidak ada di model DataPasien.
        $pasien = DataPasien::with(['waktu'])->findOrFail($id); 
        
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
                
                // Mengambil nama layanan langsung dari kolom layanan_id
                'layanan_id' => $pasien->layanan_id, 
                
                // Mengambil waktu dari relasi waktu (yang masih ada)
                'waktu_kunjungan' => $pasien->waktu->jam_mulai . ' - ' . $pasien->waktu->jam_selesai,
                
                'keluhan' => $pasien->keluhan,
                'kategori_pendaftaran' => $pasien->kategori_pendaftaran,
                'tgl_kunjungan' => Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMMM YYYY'),
                'status_pemeriksaan' => $pasien->status_pemeriksaan,
                'status_berkas' => $pasien->status_berkas,
            ]
        ]);
    }

    /**
     * Memperbarui status berkas pasien.
     */
    public function updateStatusBerkas(Request $request, $id)
    {
        $request->validate(['status_berkas' => 'required|in:Belum Diverifikasi,Sudah Diverifikasi']);

        $pasien = DataPasien::findOrFail($id);
        $pasien->status_berkas = $request->status_berkas;
        $pasien->save();

        return back()->with('success', "Status berkas pasien {$pasien->nama_pasien} berhasil diperbarui menjadi '{$request->status_berkas}'.");
    }

    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
    
    public function dataMasterPelayanan()
    {
        $jenisPelayanan = JenisPelayanan::all();
        return view('admin.jenis_pelayanan', compact('jenisPelayanan'));
    }

    /**
     * Menyimpan data Jenis Pelayanan baru.
     */
    public function storePelayanan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pelayanan' => 'required|string|max:255|unique:jenis_pelayanan,pelayanan',
            'icon_pelayanan' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $iconPath = null;

        if ($request->hasFile('icon_pelayanan')) {
            $file = $request->file('icon_pelayanan');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets'), $filename); // simpan langsung ke public/assets
            $iconPath = $filename;
        }

        JenisPelayanan::create([
            'pelayanan' => $request->pelayanan,
            'icon_pelayanan' => $iconPath,
        ]);

        return redirect()->route('admin.pelayanan')->with('success', 'Jenis pelayanan baru berhasil ditambahkan.');
    }

    public function destroyPelayanan($id)
    {
        $pelayanan = JenisPelayanan::findOrFail($id);

        // Hapus file ikon jika ada
        if ($pelayanan->icon_pelayanan && file_exists(public_path('assets/' . $pelayanan->icon_pelayanan))) {
            unlink(public_path('assets/' . $pelayanan->icon_pelayanan));
        }

        $pelayanan->delete();

        return redirect()->route('admin.pelayanan')->with('success', 'Jenis pelayanan berhasil dihapus.');
    }

    public function dataMasterJamPelayanan()
    {
        // Urutkan berdasarkan jam mulai untuk tampilan yang rapi
        $jamPelayanan = JamPelayanan::orderBy('jam_mulai')->get();
        return view('admin.jam_pelayanan', compact('jamPelayanan'));
    }

    /**
     * Menyimpan data Jam Pelayanan baru.
     */
    public function storeJamPelayanan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Validasi format waktu HH:MM dan pastikan jam mulai unik
            'jam_mulai' => 'required|date_format:H:i|unique:jam_pelayanan,jam_mulai',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
        ], [
            'jam_mulai.unique' => 'Jam mulai ini sudah terdaftar. Harap masukkan jam yang berbeda.',
            'jam_selesai.after' => 'Jam selesai harus lebih lambat dari jam mulai.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('showTambahModal', true);
        }

        JamPelayanan::create([
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
        ]);

        return redirect()->route('admin.jam-pelayanan')->with('success', 'Slot jam pelayanan berhasil ditambahkan.');
    }

    /**
     * Menghapus data Jam Pelayanan.
     */
    public function destroyJamPelayanan($id)
    {
        $jam = JamPelayanan::findOrFail($id);
        $jam->delete();

        return redirect()->route('admin.jam-pelayanan')->with('success', 'Slot jam pelayanan berhasil dihapus.');
    }
    public function dataMasterUser(Request $request, $type = 'user')
    {
        $activeTab = $type;
        $data = collect(); // Inisialisasi koleksi kosong

        switch ($activeTab) {
            case 'dokter':
                $data = Dokter::all();
                break;
            case 'admin':
                $data = Admin::all();
                break;
            case 'user':
            default:
                $data = User::all();
                $activeTab = 'user'; // Pastikan tab default adalah user
                break;
        }

        return view('admin.user', compact('data', 'activeTab'));
    }
    public function storeDokter(Request $request)
    {
        $request->validate([
            'nama_dokter' => 'required|string|max:255',
            'email' => 'required|email|unique:dokters,email',
            'alamat' => 'nullable|string|max:255',
            'no_hp' => 'nullable|string|max:20',
            'username' => 'required|string|unique:dokters,username',
            'status' => 'required|boolean',
        ]);

        Dokter::create([
            'nama_dokter' => $request->nama_dokter,
            'email' => $request->email,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'username' => $request->username,
            'password' => Hash::make($request->username), // password = username
            'status' => $request->status,
        ]);

        return redirect()->route('admin.master.users', ['type' => 'dokter'])
            ->with('success', 'Data dokter baru berhasil ditambahkan.');
    }

    public function storeAdmin(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'username' => 'required|string|unique:admins,username',
            'email' => 'required|email|unique:admins,email',
            'no_hp' => 'nullable|string|max:20',
        ]);

        Admin::create([
            'nama' => $request->nama,
            'username' => $request->username,
            'email' => $request->email,
            'no_hp' => $request->no_hp,
            'password' => Hash::make($request->username), // password = username
        ]);

        return redirect()->route('admin.master.users', ['type' => 'admin'])
            ->with('success', 'Admin baru berhasil ditambahkan.');
    }

    public function updateStatusDokter(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|boolean',
        ]);

        $dokter = Dokter::findOrFail($id);
        $dokter->status = $request->status;
        $dokter->save();

        return redirect()->route('admin.master.users', ['type' => 'dokter'])
            ->with('success', 'Status dokter berhasil diperbarui.');
    }

    public function deleteDokter($id)
    {
        $dokter = Dokter::findOrFail($id);
        $dokter->delete();

        return redirect()->back()->with('success', 'Data dokter berhasil dihapus.');
    }
    public function deleteAdmin($id)
    {
        $admin = Admin::findOrFail($id);
        $admin->delete();

        return redirect()->back()->with('success', 'Data admin berhasil dihapus.');
    }

    public function riwayatPasien(Request $request)
    {
        // PERBAIKAN: Hapus relasi 'layanan' dari with().
        $query = DataPasien::with(['dokter']);

        // Filter Tanggal Mulai
        if ($request->filled('start_date')) {
            $query->whereDate('tgl_kunjungan', '>=', $request->start_date);
        }

        // Filter Tanggal Akhir
        if ($request->filled('end_date')) {
            $query->whereDate('tgl_kunjungan', '<=', $request->end_date);
        }

        // Filter Status Pemeriksaan
        if ($request->filled('status_pemeriksaan')) {
            $query->where('status_pemeriksaan', $request->status_pemeriksaan);
        }

        // Filter Nama Pasien
        if ($request->filled('nama_pasien')) {
            $query->where('nama_pasien', 'like', '%' . $request->nama_pasien . '%');
        }

        $dataPasien = $query->orderBy('tgl_kunjungan', 'desc')->get();

        return view('admin.riwayat_pasien', [
            'dataPasien' => $dataPasien,
            'filterStartDate' => $request->start_date,
            'filterEndDate' => $request->end_date,
            'filterStatusPemeriksaan' => $request->status_pemeriksaan,
            'filterNamaPasien' => $request->nama_pasien,
        ]);
    }

    /**
     * Mengambil detail data pasien untuk modal (Hanya Baca).
     */
    public function getDetailPasien($id)
    {
        // PERBAIKAN 1: Hapus 'layanan' dari with() karena layanan_id kini menyimpan STRING.
        // Relasi yang dipanggil: 'waktu' dan 'dokter'.
        $pasien = DataPasien::with(['waktu', 'dokter'])->findOrFail($id);

        $data = [
            'nomor_antrian' => $pasien->nomor_antrian,
            'nama_pasien' => $pasien->nama_pasien,
            'tgl_lahir' => Carbon::parse($pasien->tgl_lahir)->isoFormat('D MMMM YYYY'),
            'jenis_kelamin' => $pasien->jenis_kelamin,
            'nomor_hp' => $pasien->nomor_hp ?? '-',
            'alamat' => $pasien->alamat,
            'pendamping' => $pasien->pendamping ?? '-',
            'kategori_pendaftaran' => $pasien->kategori_pendaftaran,
            
            // PERBAIKAN 2: Ambil nama layanan langsung dari kolom layanan_id
            'layanan' => $pasien->layanan_id ?? 'N/A', 
            
            // Menggunakan properti dokter_nama dari objek $data (agar bisa diakses di JS)
            'dokter_nama' => $pasien->dokter->nama_dokter ?? 'Belum Ditentukan', 
            
            'tgl_kunjungan' => Carbon::parse($pasien->tgl_kunjungan)->isoFormat('D MMM YYYY'),
            
            // Mengambil jam kunjungan dari relasi waktu
            'waktu_kunjungan' => ($pasien->waktu->jam_mulai ?? '-') . ' - ' . ($pasien->waktu->jam_selesai ?? '-'),
            
            'keluhan' => $pasien->keluhan,
            'status_pemeriksaan' => $pasien->status_pemeriksaan,
            'status_berkas' => $pasien->status_berkas,
        ];

        return response()->json(['status' => 'success', 'data' => $data]);
    }

    /**
     * Mengambil catatan pemeriksaan untuk modal (Hanya Baca).
     */
    public function getCatatan($id)
    {
        $pasien = DataPasien::select('catatan_pemeriksaan', 'catatan_obat')->findOrFail($id);

        return response()->json(['status' => 'success', 'data' => $pasien]);
    }
    


}
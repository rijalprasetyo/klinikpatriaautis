<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\JamPelayanan;

class LandingController extends Controller
{
    public function index()
    {
        // Ambil data layanan
        $jenis_pelayanan = DB::table('jenis_pelayanan')->get();

        // Ambil jam operasional dari database
        $jam_operasional = JamPelayanan::all();

        // Kirim ke view
        return view('index', compact('jenis_pelayanan', 'jam_operasional'));
    }
}

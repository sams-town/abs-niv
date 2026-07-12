<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jadwal;
use App\Models\User;
use Carbon\Carbon;

class JadwalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Admin or HRD can see all schedules, Dosen can only see their own
        if ($user->hasRole('admin') || $user->is_admin == 'admin' || $user->hasRole('hrd')) {
            $jadwals = Jadwal::with(['dosen', 'sesiDarings.laporanMengajars'])->orderBy('waktu_mulai', 'desc')->get();
        } else {
            $jadwals = Jadwal::with(['dosen', 'sesiDarings.laporanMengajars'])
                ->where('dosen_id', $user->id)
                ->orderBy('waktu_mulai', 'desc')
                ->get();
        }

        return view('jadwal.index', [
            'title' => 'Jadwal & Sesi Daring',
            'jadwals' => $jadwals
        ]);
    }

    public function create()
    {
        $dosens = User::dosen()->orderBy('name')->get();
        return view('jadwal.create', [
            'title' => 'Buat Jadwal Mengajar Baru',
            'dosens' => $dosens
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'dosen_id'       => 'required|exists:users,id',
            'nama_kelas'     => 'required|string|max:255',
            'mata_kuliah'    => 'required|string|max:255',
            'waktu_mulai'    => 'required|date_format:Y-m-d\TH:i',
            'waktu_selesai'  => 'required|date_format:Y-m-d\TH:i|after:waktu_mulai',
        ], [
            'waktu_mulai.required'       => 'Waktu Mulai wajib diisi.',
            'waktu_mulai.date_format'    => 'Format Waktu Mulai tidak valid.',
            'waktu_selesai.required'     => 'Waktu Selesai wajib diisi.',
            'waktu_selesai.date_format'  => 'Format Waktu Selesai tidak valid.',
            'waktu_selesai.after'        => 'Waktu Selesai harus setelah Waktu Mulai.',
        ]);

        // Convert datetime-local format (Y-m-d\TH:i) to MySQL datetime (Y-m-d H:i:s)
        $waktuMulai   = str_replace('T', ' ', $request->waktu_mulai) . ':00';
        $waktuSelesai = str_replace('T', ' ', $request->waktu_selesai) . ':00';

        Jadwal::create([
            'dosen_id'      => $request->dosen_id,
            'nama_kelas'    => $request->nama_kelas,
            'mata_kuliah'   => $request->mata_kuliah,
            'waktu_mulai'   => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
        ]);

        return redirect('/jadwal')->with('success', 'Jadwal Mengajar berhasil ditambahkan.');
    }
}

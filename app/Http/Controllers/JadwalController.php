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
            'dosen_id' => 'required|exists:users,id',
            'nama_kelas' => 'required|string|max:255',
            'mata_kuliah' => 'required|string|max:255',
            'waktu_mulai' => 'required|date',
            'waktu_selesai' => 'required|date|after:waktu_mulai',
        ]);

        Jadwal::create([
            'dosen_id' => $request->dosen_id,
            'nama_kelas' => $request->nama_kelas,
            'mata_kuliah' => $request->mata_kuliah,
            'waktu_mulai' => $request->waktu_mulai,
            'waktu_selesai' => $request->waktu_selesai,
        ]);

        return redirect('/jadwal')->with('success', 'Jadwal Mengajar berhasil ditambahkan.');
    }
}

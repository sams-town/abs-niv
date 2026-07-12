<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SesiDaring;
use App\Models\LaporanMengajar;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DosenTokenController extends Controller
{
    /**
     * Tampilkan halaman input token untuk dosen.
     * Menampilkan sesi daring yang sudah ended dan berstatus 'pending' milik dosen yang sedang login.
     */
    public function index()
    {
        $dosenId = auth()->id();

        // Ambil semua sesi daring dengan status 'ended' dan laporan 'pending' milik dosen ini
        $sesiPending = SesiDaring::with('jadwal')
            ->whereHas('jadwal', function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            })
            ->where('status_sesi', 'ended')
            ->whereHas('laporanMengajars', function ($q) {
                $q->where('status_pembayaran', 'pending');
            })
            ->orderBy('end_time', 'desc')
            ->get();

        // Sesi yang sudah berhasil diverifikasi (untuk riwayat)
        $sesiValid = SesiDaring::with('jadwal')
            ->whereHas('jadwal', function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            })
            ->where('status_sesi', 'ended')
            ->whereHas('laporanMengajars', function ($q) {
                $q->where('status_pembayaran', 'valid');
            })
            ->orderBy('end_time', 'desc')
            ->limit(10)
            ->get();

        return view('dosen.token.index', [
            'title'        => 'Input Token Daring',
            'sesiPending'  => $sesiPending,
            'sesiValid'    => $sesiValid,
        ]);
    }

    /**
     * Verifikasi token yang dimasukkan oleh dosen.
     * Jika token cocok, hitung gaji dan buat log mengajar.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'sesi_daring_id' => 'required|exists:sesi_darings,id',
            'token_input'    => 'required|string|max:20',
        ]);

        $dosenId = auth()->id();
        $sesiId  = $request->sesi_daring_id;

        // Cari sesi — pastikan sesi milik dosen yang sedang login
        $sesi = SesiDaring::with('jadwal.dosen.masterSkemaHonorarium')
            ->where('id', $sesiId)
            ->where('status_sesi', 'ended')
            ->whereHas('jadwal', function ($q) use ($dosenId) {
                $q->where('dosen_id', $dosenId);
            })
            ->first();

        if (!$sesi) {
            return redirect()->back()->with('error', 'Sesi tidak ditemukan atau Anda tidak berhak mengakses sesi ini.');
        }

        // Cek laporan mengajar masih pending
        $laporan = LaporanMengajar::where('sesi_daring_id', $sesi->id)
            ->where('status_pembayaran', 'pending')
            ->first();

        if (!$laporan) {
            return redirect()->back()->with('warning', 'Sesi ini sudah diverifikasi sebelumnya atau tidak memiliki laporan pending.');
        }

        // Bandingkan token (case-insensitive)
        if (strtoupper(trim($request->token_input)) !== strtoupper(trim($sesi->token_daring))) {
            return redirect()->back()->with('error', 'Token yang Anda masukkan TIDAK SESUAI dengan token sistem. Silakan periksa kembali.');
        }

        // Token cocok — hitung gaji dan buat catatan log
        DB::transaction(function () use ($sesi, $laporan, $dosenId) {
            $dosen = $sesi->jadwal->dosen;
            $endTime = $sesi->end_time ?? Carbon::now();

            // Ambil tarif
            $rate = 0;
            if ($dosen->masterSkemaHonorarium) {
                $rate = $dosen->masterSkemaHonorarium->nominal_per_unit;
            } else {
                $rate = $dosen->nominal_honor ?? 0;
            }

            $totalGaji   = 1.00 * $rate;
            $catatanBaru = "Token terverifikasi pada " . now()->format('d/m/Y H:i') . ". Tarif: Rp " . number_format($rate, 0, ',', '.') . " per sesi.";

            // Update laporan mengajar
            $laporan->update([
                'total_gaji'         => $totalGaji,
                'status_pembayaran'  => 'valid',
                'catatan_sistem'     => $catatanBaru,
            ]);

            // Buat LogMengajar
            \App\Models\LogMengajar::create([
                'dosen_id'    => $dosen->id,
                'kelas_id'    => $sesi->jadwal->nama_kelas,
                'tanggal'     => Carbon::parse($endTime)->toDateString(),
                'jumlah_unit' => 1.00,
            ]);

            // Buat TransaksiMengajar
            \App\Models\TransaksiMengajar::create([
                'dosen_id'         => $dosen->id,
                'kelas_id'         => $sesi->jadwal->nama_kelas,
                'tanggal'          => Carbon::parse($endTime)->toDateString(),
                'jumlah_sesi_token' => 1.00,
                'nominal_honor'    => $rate,
            ]);
        });

        return redirect()->back()->with('success', 'Token berhasil diverifikasi! Penggajian sesi daring telah dihitung dan dicatat ke sistem.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Events\NotifApproval;
use App\Models\PegawaiKeluar;

class PegawaiKeluarController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $title = 'Pegawai Keluar';
        $nama = request()->input('nama');
        $mulai = request()->input('mulai');
        $akhir = request()->input('akhir');
        $tahun = request()->input('tahun', date('Y'));
        $bulan = request()->input('bulan', date('m'));

        $pegawai_keluars = PegawaiKeluar::when($mulai && $akhir, function ($query) use ($mulai, $akhir) {
                                $query->whereBetween('tanggal', [$mulai, $akhir]);
                            })
                            ->when($nama, function ($query) use ($nama) {
                                $query->whereHas('user', function ($q) use ($nama) {
                                    $q->where('name', 'LIKE', '%' . $nama . '%');
                                });
                            })
                            ->when(auth()->user() && auth()->user()->Jabatan && auth()->user()->Jabatan->manager != auth()->user()->id && auth()->user()->is_admin == 'user', function ($query) {
                                $query->where('user_id', auth()->user()->id);
                            })
                            ->when(auth()->user() && auth()->user()->Jabatan && auth()->user()->Jabatan->manager == auth()->user()->id && auth()->user()->is_admin == 'user', function ($query) {
                                $query->whereHas('user', function ($q) {
                                    $q->where('jabatan_id', auth()->user()->jabatan_id);
                                });
                            })
                            ->orderBy('tanggal', 'DESC')
                            ->paginate(10)
                            ->withQueryString();

        // ============================================================
        // STATISTIK TURNOVER — Sesuai Ketentuan Indonesia
        // Rumus: Turnover Rate = (Jumlah Keluar / Rata-rata Karyawan) × 100%
        // Referensi: Pedoman BPS & Depnakertrans RI
        // ============================================================

        // Total statistik keseluruhan
        $totalResignasi   = PegawaiKeluar::count();
        $pendingApproval  = PegawaiKeluar::where('status', 'PENDING')->count();
        $totalApproved    = PegawaiKeluar::where('status', 'APPROVED')->count();
        $totalRejected    = PegawaiKeluar::where('status', 'REJECTED')->count();

        // Breakdown per Jenis Keberhentian (APPROVED saja)
        $breakdownJenis = PegawaiKeluar::where('status', 'APPROVED')
            ->selectRaw('jenis, COUNT(*) as total')
            ->groupBy('jenis')
            ->pluck('total', 'jenis')
            ->toArray();

        // --- TURNOVER RATE BULAN INI ---
        $bulanIniMulai = date('Y-m-01');
        $bulanIniAkhir = date('Y-m-t');

        // Jumlah karyawan keluar (APPROVED) bulan ini
        $keluarBulanIni = PegawaiKeluar::where('status', 'APPROVED')
            ->whereBetween('tanggal', [$bulanIniMulai, $bulanIniAkhir])
            ->count();

        // Rata-rata karyawan aktif (awal bulan + akhir bulan) / 2
        // Karyawan aktif = tidak punya masa_berlaku atau masa_berlaku > hari ini
        $totalKaryawanAktif = User::where('is_admin', '!=', 'superadmin')
            ->where(function($q) {
                $q->whereNull('masa_berlaku')
                  ->orWhere('masa_berlaku', '>', date('Y-m-d'));
            })->count();

        // Karyawan yang keluar bulan ini dihitung sebagai "karyawan awal bulan"
        $totalKaryawanAwalBulan = $totalKaryawanAktif + $keluarBulanIni;
        $rataRataKaryawanBulan  = ($totalKaryawanAwalBulan + $totalKaryawanAktif) / 2;

        $turnoverRateBulan = $rataRataKaryawanBulan > 0
            ? round(($keluarBulanIni / $rataRataKaryawanBulan) * 100, 2)
            : 0;

        // --- TURNOVER RATE TAHUN INI (Annualized) ---
        $tahunIniMulai = date('Y') . '-01-01';
        $tahunIniAkhir = date('Y') . '-12-31';

        $keluarTahunIni = PegawaiKeluar::where('status', 'APPROVED')
            ->whereBetween('tanggal', [$tahunIniMulai, $tahunIniAkhir])
            ->count();

        $turnoverRateTahunan = $rataRataKaryawanBulan > 0
            ? round(($keluarTahunIni / $rataRataKaryawanBulan) * 100, 2)
            : 0;

        // --- TREND BULANAN (12 bulan terakhir) ---
        $trendBulanan = [];
        for ($i = 11; $i >= 0; $i--) {
            $tglMulai = date('Y-m-01', strtotime("-$i months"));
            $tglAkhir = date('Y-m-t', strtotime("-$i months"));
            $label    = date('M Y', strtotime("-$i months"));
            $jumlah   = PegawaiKeluar::where('status', 'APPROVED')
                ->whereBetween('tanggal', [$tglMulai, $tglAkhir])
                ->count();
            $trendBulanan[] = ['label' => $label, 'jumlah' => $jumlah];
        }

        // --- MASA KERJA RATA-RATA KARYAWAN KELUAR ---
        $karyawanKeluarApproved = PegawaiKeluar::where('status', 'APPROVED')
            ->with('user')
            ->get();

        $totalMasaKerjaHari = 0;
        $countMasaKerja     = 0;
        foreach ($karyawanKeluarApproved as $pk) {
            if ($pk->user && $pk->user->tgl_join && $pk->tanggal) {
                $join   = \Carbon\Carbon::parse($pk->user->tgl_join);
                $keluar = \Carbon\Carbon::parse($pk->tanggal);
                $totalMasaKerjaHari += $join->diffInDays($keluar);
                $countMasaKerja++;
            }
        }
        $rataRataMasaKerjaBulan = $countMasaKerja > 0
            ? round($totalMasaKerjaHari / $countMasaKerja / 30.44, 1)
            : 0;

        if (auth()->user()->is_admin == 'admin') {
            return view('pegawai-keluar.index', compact(
                'title',
                'pegawai_keluars',
                'totalResignasi',
                'pendingApproval',
                'totalApproved',
                'totalRejected',
                'breakdownJenis',
                'keluarBulanIni',
                'keluarTahunIni',
                'turnoverRateBulan',
                'turnoverRateTahunan',
                'totalKaryawanAktif',
                'rataRataKaryawanBulan',
                'trendBulanan',
                'rataRataMasaKerjaBulan',
            ));
        } else {
            return view('pegawai-keluar.indexUser', compact(
                'title',
                'pegawai_keluars'
            ));
        }

    }


    public function tambah()
    {
        $title = 'Pegawai Keluar';
        $users = User::orderBy('name')->get();

        if (auth()->user()->is_admin == 'admin') {
            return view('pegawai-keluar.tambah', compact(
                'title',
                'users',
            ));
        } else {
            return view('pegawai-keluar.tambahUser', compact(
                'title',
                'users',
            ));
        }

    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'jenis' => 'required',
            'alasan' => 'required',
            'tanggal' => 'required',
            'pegawai_keluar_file_path' => 'nullable',
        ]);

        if ($request->file('pegawai_keluar_file_path')) {
            $validated['pegawai_keluar_file_path'] = $request->file('pegawai_keluar_file_path')->store('pegawai_keluar_file_path');
            $validated['pegawai_keluar_file_name'] = $request->file('pegawai_keluar_file_path')->getClientOriginalName();
        }

        $validated['status'] = 'PENDING';

        $pegawai_keluar = PegawaiKeluar::create($validated);

        $user = $pegawai_keluar->user->Jabatan->man ?? null;
        if ($user) {
            $type = 'Approval';
            $notif = 'Pengajuan Pegawai Keluar Dari ' . auth()->user()->name . ' Butuh Approval Anda';
            $url = url('/exit?nama='.$pegawai_keluar->user->name.'&mulai='.$request->tanggal.'&akhir='.$request->tanggal);

            $user->messages = [
                'user_id'   =>  auth()->user()->id,
                'from'   =>  auth()->user()->name,
                'message'   =>  $notif,
                'action'   => '/exit?nama='.$pegawai_keluar->user->name.'&mulai='.$request->tanggal.'&akhir='.$request->tanggal
            ];
            $user->notify(new \App\Notifications\UserNotification);

            NotifApproval::dispatch($type, $user->id, $notif, $url);
        }

        return redirect('/exit')->with('success', 'Data Berhasil Disimpan');
    }

    public function edit($id)
    {
        $title = 'Pegawai Keluar';
        $users = User::orderBy('name')->get();
        $pegawai_keluar = PegawaiKeluar::find($id);

        if (auth()->user()->is_admin == 'admin') {
            return view('pegawai-keluar.edit', compact(
                'title',
                'users',
                'pegawai_keluar',
            ));
        } else {
            return view('pegawai-keluar.editUser', compact(
                'title',
                'users',
                'pegawai_keluar',
            ));
        }

    }

    public function update(Request $request, $id)
    {
        $pegawai_keluar = PegawaiKeluar::find($id);

        $validated = $request->validate([
            'user_id' => 'required',
            'jenis' => 'required',
            'alasan' => 'required',
            'tanggal' => 'required',
            'pegawai_keluar_file_path' => 'nullable',
        ]);

        if ($request->file('pegawai_keluar_file_path')) {
            $validated['pegawai_keluar_file_path'] = $request->file('pegawai_keluar_file_path')->store('pegawai_keluar_file_path');
            $validated['pegawai_keluar_file_name'] = $request->file('pegawai_keluar_file_path')->getClientOriginalName();
        }

        $pegawai_keluar->update($validated);

        $user = $pegawai_keluar->user->Jabatan->man ?? null;
        if ($user) {
            $type = 'Approval';
            $notif = 'Pengajuan Pegawai Keluar Dari ' . auth()->user()->name . ' Butuh Approval Anda';
            $url = url('/exit?nama='.$pegawai_keluar->user->name.'&mulai='.$request->tanggal.'&akhir='.$request->tanggal);

            $user->messages = [
                'user_id'   =>  auth()->user()->id,
                'from'   =>  auth()->user()->name,
                'message'   =>  $notif,
                'action'   => '/exit?nama='.$pegawai_keluar->user->name.'&mulai='.$request->tanggal.'&akhir='.$request->tanggal
            ];
            $user->notify(new \App\Notifications\UserNotification);

            NotifApproval::dispatch($type, $user->id, $notif, $url);
        }

        return redirect('/exit')->with('success', 'Data Berhasil Diupdate');
    }

    public function approval(Request $request, $id)
    {
        $pegawai_keluar = PegawaiKeluar::find($id);

        $validated = $request->validate([
            'status' => 'required',
            'notes' => 'nullable',
            'approved_by' => 'required',
        ]);

        $pegawai_keluar->update($validated);

        if ($pegawai_keluar->status == 'APPROVED') {
            $pegawai_keluar->user->update([
                'masa_berlaku' => $pegawai_keluar->tanggal
            ]);
        }

        return redirect('/exit')->with('success', 'Data Berhasil Diupdate');
    }

    public function delete($id)
    {
        $pegawai_keluar = PegawaiKeluar::find($id);
        $pegawai_keluar->delete();
        return redirect('/exit')->with('success', 'Data Berhasil Didelete');
    }
}

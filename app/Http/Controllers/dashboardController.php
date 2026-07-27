<?php

namespace App\Http\Controllers;
use App\Models\Cuti;
use App\Models\User;
use App\Models\Berita;
use App\Models\Kasbon;
use App\Models\Lembur;
use App\Models\Payroll;
use App\Models\ResetCuti;
use App\Models\MappingShift;
use App\Models\Kontrak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Reimbursement;

class dashboardController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $tgl_skrg = date("Y-m-d");
        $tahun_skrg = date('Y');
        $bulan_skrg = date('m');
        $jmlh_bulan = cal_days_in_month(CAL_GREGORIAN,$bulan_skrg,$tahun_skrg);
        $tgl_mulai = date('Y-m-01');
        $tgl_akhir = date('Y-m-'.$jmlh_bulan);

        if(auth()->user()->is_admin == "admin"){
            // Deteksi Dini Kontrak Kerja (habis dalam 3 bulan)
            $tgl_3_bulan = Carbon::now()->addMonths(3)->format('Y-m-d');
            $tgl_lampau = Carbon::now()->subMonths(6)->format('Y-m-d');
            
            $expiring_users = collect();
            $users_exp = User::pegawaiDanDosen()
                ->with(['Jabatan', 'Divisi'])
                ->whereNotNull('masa_berlaku')
                ->whereBetween('masa_berlaku', [$tgl_lampau, $tgl_3_bulan])
                ->get();
                
            foreach ($users_exp as $u) {
                $expiring_users->push([
                    'id' => $u->id,
                    'name' => $u->name,
                    'tipe_user' => $u->tipe_user,
                    'jabatan' => $u->Jabatan ? $u->Jabatan->nama_jabatan : ($u->Divisi ? $u->Divisi->nama_divisi : '-'),
                    'tanggal_selesai' => $u->masa_berlaku
                ]);
            }
            
            $kontraks_exp = Kontrak::with('user.Jabatan', 'user.Divisi')
                ->whereNotNull('tanggal_selesai')
                ->whereBetween('tanggal_selesai', [$tgl_lampau, $tgl_3_bulan])
                ->whereHas('user', function($q) {
                    $q->whereIn('tipe_user', ['pegawai', 'dosen']);
                })
                ->get();
                
            foreach ($kontraks_exp as $k) {
                if ($k->user && !$expiring_users->contains('id', $k->user->id)) {
                    $expiring_users->push([
                        'id' => $k->user->id,
                        'name' => $k->user->name,
                        'tipe_user' => $k->user->tipe_user,
                        'jabatan' => $k->user->Jabatan ? $k->user->Jabatan->nama_jabatan : ($k->user->Divisi ? $k->user->Divisi->nama_divisi : '-'),
                        'tanggal_selesai' => $k->tanggal_selesai
                    ]);
                }
            }
            
            $expiring_users = $expiring_users->sortBy('tanggal_selesai')->values()->all();

            return view('dashboard.index', [
                'title' => 'Dashboard',
                'kontrak_expiring' => $expiring_users,
                'jumlah_user' => User::pegawaiDanDosen()->count(),
                'jumlah_masuk' => MappingShift::where('tanggal', $tgl_skrg)->where('status_absen', 'Masuk')->count(),
                'jumlah_libur' => MappingShift::where('tanggal', $tgl_skrg)->where('status_absen', 'Libur')->count(),
                'jumlah_cuti' => MappingShift::where('tanggal', $tgl_skrg)->where('status_absen', 'Cuti')->count(),
                'jumlah_sakit' => MappingShift::where('tanggal', $tgl_skrg)->where('status_absen', 'Sakit')->count(),
                'jumlah_izin_masuk' => MappingShift::where('tanggal', $tgl_skrg)->where('status_absen', 'Izin Masuk')->count(),
                'jumlah_izin_telat' => MappingShift::where('tanggal', $tgl_skrg)->where('status_absen', 'Izin Telat')->count(),
                'jumlah_izin_pulang_cepat' => MappingShift::where('tanggal', $tgl_skrg)->where('status_absen', 'Izin Pulang Cepat')->count(),
                'jumlah_karyawan_lembur' => Lembur::where('tanggal', $tgl_skrg)->count(),
                'payroll' => Payroll::where('bulan', date('m'))->where('tahun', date('Y'))->sum('grand_total'),
                'kasbon' => Kasbon::whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->where('status', 'Acc')->sum('nominal'),
                'reimbursement' => Reimbursement::whereBetween('tanggal', [$tgl_mulai, $tgl_akhir])->where('status', 'Approved')->sum('total')
            ]);
        } else {
            $user_login = auth()->user()->id;
            $tanggal = "";
            $tglskrg = date('Y-m-d');
            $tglkmrn = date('Y-m-d', strtotime('-1 days'));
            $mapping_shift = MappingShift::where('user_id', $user_login)->where('tanggal', $tglkmrn)->get();
            if($mapping_shift->count() > 0) {
                foreach($mapping_shift as $mp) {
                    $jam_absen = $mp->jam_absen;
                    $jam_pulang = $mp->jam_pulang;
                }
            } else {
                $jam_absen = "-";
                $jam_pulang = "-";
            }
            if($jam_absen != null && $jam_pulang == null) {
                $tanggal = $tglkmrn;
            } else {
                $tanggal = $tglskrg;
            }

            $berita = Berita::where('tipe', 'Berita')->orderBy('id', 'DESC')->limit(4)->get();
            $informasi = Berita::where('tipe', 'Informasi')->orderBy('id', 'DESC')->limit(4)->get();
            return view('dashboard.indexUser', [
                'title' => 'Dashboard',
                'berita' => $berita,
                'informasi' => $informasi,
                'shift_karyawan' => MappingShift::where('user_id', $user_login)->where('tanggal', $tanggal)->first()
            ]);
        }
    }

    public function menu()
    {
        return view('dashboard.menu', [
            'title' => 'All Menu',
        ]);
    }
}

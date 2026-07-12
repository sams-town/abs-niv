<?php

namespace App\Http\Controllers;

use App\Helpers\PivotBuilder;
use App\Exports\AbsenPivotExport;
use App\Models\Lokasi;
use App\Models\MappingShift;
use App\Models\User;
use App\Models\settings;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AbsenPivotController extends Controller
{
    public function index()
    {
        $lokasi = Lokasi::where('status', 'approved')->orderBy('nama_lokasi')->get();
        return view('laporan-pivot.index', [
            'title' => 'Laporan Absensi Pivot',
            'lokasi' => $lokasi,
        ]);
    }

    public function generate(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
        ]);

        $mulai     = $request->tanggal_mulai;
        $akhir     = $request->tanggal_akhir;
        $lokasi_id = $request->lokasi_id;
        $tipe_user = $request->tipe_user ?? 'semua';

        // Build date array
        $dates = [];
        $begin = new \DateTime($mulai);
        $end   = (new \DateTime($akhir))->modify('+1 day');
        $period = new \DatePeriod($begin, new \DateInterval('P1D'), $end);
        foreach ($period as $d) {
            $dates[] = $d->format('Y-m-d');
        }

        // Query users
        $usersQuery = User::with(['MappingShift' => function ($q) use ($mulai, $akhir) {
            $q->whereBetween('tanggal', [$mulai, $akhir]);
        }])
        ->when($lokasi_id, fn($q) => $q->where('lokasi_id', $lokasi_id))
        ->when($tipe_user === 'dosen',   fn($q) => $q->dosen())
        ->when($tipe_user === 'pegawai', fn($q) => $q->pegawai())
        ->orderBy('name');

        $users = $usersQuery->with(['Jabatan', 'Lokasi'])->get();

        // Build pivot rows
        $rows = [];
        foreach ($users as $user) {
            $shiftsByDate = $user->MappingShift->pluck('status_absen', 'tanggal')->toArray();
            $codes   = PivotBuilder::buildCodes($shiftsByDate, $dates);
            $summary = PivotBuilder::buildSummary($codes);
            $rows[]  = compact('user', 'codes', 'summary');
        }

        $chartData = PivotBuilder::buildChartData($rows, $dates);

        if (count($rows) === 0) {
            $chartData = null;
        }

        return view('laporan-pivot.result', [
            'title'      => 'Laporan Absensi Pivot',
            'dates'      => $dates,
            'rows'       => $rows,
            'mulai'      => $mulai,
            'akhir'      => $akhir,
            'lokasi_id'  => $lokasi_id,
            'tipe_user'  => $tipe_user,
            'chartData'  => $chartData,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
        ]);

        $mulai     = $request->tanggal_mulai;
        $akhir     = $request->tanggal_akhir;
        $lokasi_id = $request->lokasi_id;
        $tipe_user = $request->tipe_user ?? 'semua';

        $dates = [];
        $begin = new \DateTime($mulai);
        $end   = (new \DateTime($akhir))->modify('+1 day');
        $period = new \DatePeriod($begin, new \DateInterval('P1D'), $end);
        foreach ($period as $d) {
            $dates[] = $d->format('Y-m-d');
        }

        $users = User::with(['MappingShift' => function ($q) use ($mulai, $akhir) {
            $q->whereBetween('tanggal', [$mulai, $akhir]);
        }])
        ->when($lokasi_id, fn($q) => $q->where('lokasi_id', $lokasi_id))
        ->when($tipe_user === 'dosen',   fn($q) => $q->dosen())
        ->when($tipe_user === 'pegawai', fn($q) => $q->pegawai())
        ->orderBy('name')->get();

        $rows = [];
        foreach ($users as $user) {
            $shiftsByDate = $user->MappingShift->pluck('status_absen', 'tanggal')->toArray();
            $codes   = PivotBuilder::buildCodes($shiftsByDate, $dates);
            $summary = PivotBuilder::buildSummary($codes);
            $rows[]  = compact('user', 'codes', 'summary');
        }

        $fileName = "Laporan_Absensi_Pivot_{$mulai}_{$akhir}.xlsx";
        return Excel::download(new AbsenPivotExport($rows, $dates, $mulai, $akhir), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'tanggal_mulai' => 'required|date_format:Y-m-d',
            'tanggal_akhir' => 'required|date_format:Y-m-d|after_or_equal:tanggal_mulai',
        ]);

        $mulai     = $request->tanggal_mulai;
        $akhir     = $request->tanggal_akhir;
        $lokasi_id = $request->lokasi_id;
        $tipe_user = $request->tipe_user ?? 'semua';

        $dates = [];
        $begin = new \DateTime($mulai);
        $end   = (new \DateTime($akhir))->modify('+1 day');
        $period = new \DatePeriod($begin, new \DateInterval('P1D'), $end);
        foreach ($period as $d) {
            $dates[] = $d->format('Y-m-d');
        }

        $users = User::with(['MappingShift' => function ($q) use ($mulai, $akhir) {
            $q->whereBetween('tanggal', [$mulai, $akhir]);
        }])
        ->when($lokasi_id, fn($q) => $q->where('lokasi_id', $lokasi_id))
        ->when($tipe_user === 'dosen',   fn($q) => $q->dosen())
        ->when($tipe_user === 'pegawai', fn($q) => $q->pegawai())
        ->orderBy('name')->get();

        $rows = [];
        foreach ($users as $user) {
            $shiftsByDate = $user->MappingShift->pluck('status_absen', 'tanggal')->toArray();
            $codes   = PivotBuilder::buildCodes($shiftsByDate, $dates);
            $summary = PivotBuilder::buildSummary($codes);
            $rows[]  = compact('user', 'codes', 'summary');
        }

        $pages    = PivotBuilder::paginateByDates($dates);
        $settings = settings::first();
        $lokasi   = $lokasi_id ? \App\Models\Lokasi::find($lokasi_id) : null;

        $pdf = Pdf::loadView('laporan-pivot.pdf', compact(
            'pages', 'rows', 'mulai', 'akhir', 'settings', 'lokasi'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream("Laporan_Absensi_{$mulai}_{$akhir}.pdf");
    }

    public function rekapBulanan(Request $request)
    {
        $tahun     = $request->tahun ?? date('Y');
        $lokasi_id = $request->lokasi_id;
        $tipe_user = $request->tipe_user ?? 'semua';
        $bulan_list = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

        $users = User::with(['MappingShift' => function ($q) use ($tahun) {
            $q->whereYear('tanggal', $tahun)
              ->whereIn('status_absen', ['Masuk', 'Izin Telat', 'Izin Pulang Cepat']);
        }, 'Jabatan', 'Lokasi'])
        ->when($lokasi_id, fn($q) => $q->where('lokasi_id', $lokasi_id))
        ->when($tipe_user === 'dosen',   fn($q) => $q->dosen())
        ->when($tipe_user === 'pegawai', fn($q) => $q->pegawai())
        ->orderBy('name')->get();

        $lokasi = Lokasi::where('status', 'approved')->orderBy('nama_lokasi')->get();

        $rows = [];
        foreach ($users as $user) {
            $monthly = [];
            $total = 0;
            for ($m = 1; $m <= 12; $m++) {
                $count = $user->MappingShift->filter(fn($ms) => (int) date('m', strtotime($ms->tanggal)) === $m)->count();
                $monthly[$m] = $count;
                $total += $count;
            }
            $rows[] = ['user' => $user, 'monthly' => $monthly, 'total' => $total];
        }

        return view('laporan-pivot.rekap-bulanan', [
            'title'      => 'Rekap Bulanan Absensi',
            'rows'       => $rows,
            'tahun'      => $tahun,
            'bulan_list' => $bulan_list,
            'lokasi'     => $lokasi,
            'lokasi_id'  => $lokasi_id,
            'tipe_user'  => $tipe_user,
        ]);
    }
}

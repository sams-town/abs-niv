<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Exports\PajakExport;
use Illuminate\Http\Request;

class PajakController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Pajak';
        $bulan = request()->input('bulan');
        $tahun = request()->input('tahun');
        $payroll = Payroll::when($bulan, function ($query) use ($bulan) {
            $query->where('bulan', $bulan);
        })
        ->when($tahun, function ($query) use ($tahun) {
            $query->where('tahun', $tahun);
        })
        ->orderBy('no_gaji', 'DESC')
        ->paginate(10)
        ->withQueryString();

        $months = [
            '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
            '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
            '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
        ];

        return view('pajak.index', compact(
            'title',
            'payroll',
            'months',
        ));
    }

    public function export()
    {
        return (new PajakExport($_GET))->download('List Pajak.xlsx');
    }
}

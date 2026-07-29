<?php

namespace App\Http\Controllers;

use App\Models\Lokasi;
use App\Models\Patroli;
use Illuminate\Http\Request;
use App\Exports\PatroliExport;
use RealRashid\SweetAlert\Facades\Alert;
use Barryvdh\DomPDF\Facade\Pdf;

class PatroliController extends Controller
{
    public function index()
    {
        if (auth()->user()->is_admin == 'admin') {
            return view('patroli.index', [
                "title" => "Patroli",
            ]);
        } else {
            return view('patroli.indexUser', [
                "title" => "Patroli",
            ]);
        }

    }

    public function store(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $lokasi = Lokasi::where('nama_lokasi', $request->nama_lokasi)->first();
        if ($lokasi) {
            $lat_kantor = $lokasi->lat_kantor;
            $long_kantor = $lokasi->long_kantor;
            $jarak = $this->distance($request->lat, $request->long, $lat_kantor, $long_kantor, "K") * 1000;

            Patroli::create([
                'user_id' => auth()->user()->id,
                'lokasi_id' => $lokasi->id,
                'tanggal' => date('Y-m-d'),
                'jam' => date('H:i'),
                'lat' => $request->lat,
                'long' => $request->long,
                'jarak' => $jarak,
            ]);

            return response()->json('success');
        } else {
            return response()->json('failed');
        }
    }

    public function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function data()
    {
        $title = 'Data Patroli';
        $nama = request()->input('nama');
        $mulai = request()->input('mulai');
        $akhir = request()->input('akhir');

        $patroli = Patroli::when($nama, function ($query) use ($nama) {
                                    $query->whereHas('User', function ($q) use ($nama) {
                                        $q->where('name', 'LIKE', '%'.$nama.'%');
                                    });
                                })
                                ->when($mulai && $akhir, function ($query) use ($mulai, $akhir) {
                                    $query->whereBetween('tanggal', [$mulai, $akhir]);
                                })
                                ->when(auth()->user()->is_admin !== 'admin', function ($query) {
                                    $query->where('user_id', auth()->user()->id);
                                })
                                ->orderBy('id', 'DESC')
                                ->paginate(10)
                                ->withQueryString();

        return view(auth()->user()->is_admin == 'admin' ? 'patroli.dataPatroli' : 'patroli.dataPatroliUser', compact(
            'title',
            'patroli'
        ));
    }

    public function delete($id)
    {
        $patroli = Patroli::find($id);
        $patroli->delete();
        return back()->with('success', 'Data Berhasil Didelete');
    }

    public function excel()
    {
        return (new PatroliExport($_GET))->download('List Patroli.xlsx');
    }

    public function pdf()
    {
        $nama = request()->input('nama');
        $mulai = request()->input('mulai');
        $akhir = request()->input('akhir');

        $patroli = Patroli::when($nama, function ($query) use ($nama) {
                                    $query->whereHas('User', function ($q) use ($nama) {
                                        $q->where('name', 'LIKE', '%'.$nama.'%');
                                    });
                                })
                                ->when($mulai && $akhir, function ($query) use ($mulai, $akhir) {
                                    $query->whereBetween('tanggal', [$mulai, $akhir]);
                                })
                                ->when(auth()->user()->is_admin !== 'admin', function ($query) {
                                    $query->where('user_id', auth()->user()->id);
                                })
                                ->orderBy('id', 'DESC')
                                ->get();

        $pdf = Pdf::loadView('patroli.pdf', [
            'title' => 'List Patroli',
            'patroli' => $patroli
        ]);

        return $pdf->stream();
    }

    public function maps($lat, $long, $lokasi_id)
    {
        date_default_timezone_set('Asia/Jakarta');
        if (auth()->user()->is_admin == 'admin') {
            return view('patroli.maps', [
                'title' => 'Maps',
                'lat' => $lat,
                'long' => $long,
                'lokasi' => Lokasi::findOrFail($lokasi_id)
            ]);
        } else {
            return view('patroli.mapsUser', [
                'title' => 'Maps',
                'lat' => $lat,
                'long' => $long,
                'lokasi' => Lokasi::findOrFail($lokasi_id)
            ]);
        }
    }
    public function scanQrcode(Request $request, $lokasi_id, $token)
    {
        date_default_timezone_set('Asia/Jakarta');
        
        $lokasi = Lokasi::where('id', $lokasi_id)
                        ->where('qr_token', $token)
                        ->where('keterangan', 'Patroli')
                        ->first();

        if (!$lokasi) {
            abort(404, 'QR Code tidak valid atau lokasi bukan untuk patroli.');
        }

        $user = auth()->user();
        $now = now();

        // Cek apakah sudah scan di lokasi yang sama dalam 5 menit terakhir (anti duplikat)
        $lastScan = Patroli::where('user_id', $user->id)
                           ->where('lokasi_id', $lokasi->id)
                           ->where('tanggal', $now->format('Y-m-d'))
                           ->where('jam', '>=', $now->copy()->subMinutes(5)->format('H:i:s'))
                           ->first();

        if ($lastScan) {
            return view('patroli.scan', [
                'title'     => 'Patroli Security',
                'lokasi'    => $lokasi,
                'status'    => 'duplicate',
                'message'   => 'Anda sudah melakukan patroli di lokasi ini pukul ' . $lastScan->jam . '. Silakan patroli lagi setelah 5 menit.',
                'lastScan'  => $lastScan,
            ]);
        }

        // Simpan record patroli
        $patroli = Patroli::create([
            'user_id'    => $user->id,
            'lokasi_id'  => $lokasi->id,
            'tanggal'    => $now->format('Y-m-d'),
            'jam'        => $now->format('H:i:s'),
            'lat'        => $lokasi->lat_kantor,
            'long'       => $lokasi->long_kantor,
            'jarak'      => 0, // Karena scan di lokasi (asumsi di tempat), jarak set 0, atau biarkan frontend kirim koordinat aktual
        ]);

        return view('patroli.scan', [
            'title'   => 'Patroli Security',
            'lokasi'  => $lokasi,
            'status'  => 'success',
            'message' => 'Patroli berhasil dicatat!',
            'patroli' => $patroli,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Lembur;
use App\Models\Lokasi;
use App\Models\settings;
use Illuminate\Http\Request;
use App\Events\NotifApproval;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class LemburController extends Controller
{
    public function index()
    {
        date_default_timezone_set('Asia/Jakarta');
        $user_login = auth()->user()->id;
        $tanggal = "";
        $tglskrg = date("Y-m-d");
        $tglkmrn = date('Y-m-d', strtotime('-1 days'));
        $lembur = Lembur::where('user_id', $user_login)->where('tanggal', $tglkmrn)->get();
        if($lembur->count() > 0) {
            foreach($lembur as $l) {
                $jam_keluar = $l->jam_keluar;
            }
        } else {
            $jam_keluar = "-";
        }
        if($jam_keluar == null){
            $tanggal = $tglkmrn;
        } else {
            $tanggal = $tglskrg;
        }

        if (auth()->user()->is_admin == 'admin') {
            return view('lembur.index', [
                'title' => 'Absen Lembur',
                'lembur' => Lembur::where('user_id', $user_login)->where('tanggal', $tanggal)->get()
            ]);
        } else {
            return view('lembur.indexuser', [
                'title' => 'Absen Lembur',
                'lembur' => Lembur::where('user_id', $user_login)->where('tanggal', $tanggal)->first()
            ]);
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

    public function masuk(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $lat_kantor = auth()->user()->Lokasi->lat_kantor;
        $long_kantor = auth()->user()->Lokasi->long_kantor;
        $radius = auth()->user()->Lokasi->radius;
        $nama_lokasi = auth()->user()->Lokasi->nama_lokasi;

        $request["jarak_masuk"] = $this->distance($request["lat_masuk"], $request["long_masuk"], $lat_kantor, $long_kantor, "K") * 1000;

        if($request["jarak_masuk"] > $radius) {
            Alert::error('Diluar Jangkauan', 'Lokasi Anda Diluar Radius ' . $nama_lokasi);
            return redirect('/lembur');
        } else {
            $foto_jam_masuk = $request["foto_jam_masuk"];

            $image_parts = explode(";base64,", $foto_jam_masuk);

            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'foto_jam_masuk_lembur/' . uniqid() . '.png';

            Storage::disk('public')->put($fileName, $image_base64);

            $request["foto_jam_masuk"] = $fileName;

            $validatedData = $request->validate([
                'user_id' => 'required',
                'tanggal' => 'required',
                'jam_masuk' => 'required',
                'foto_jam_masuk' => 'required',
                'lat_masuk' => 'required',
                'long_masuk' => 'required',
                'jarak_masuk' => 'required',
                'status' => 'required'
            ]);

            $validatedData['lokasi_id'] = auth()->user()->lokasi_id;

            Lembur::create($validatedData);

            $request->session()->flash('success', 'Berhasil Masuk Lembur');

            return redirect('/lembur');
        }

    }

    public function pulang(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');

        $lat_kantor = auth()->user()->Lokasi->lat_kantor;
        $long_kantor = auth()->user()->Lokasi->long_kantor;
        $radius = auth()->user()->Lokasi->radius;
        $nama_lokasi = auth()->user()->Lokasi->nama_lokasi;

        $request["jarak_keluar"] = $this->distance($request["lat_keluar"], $request["long_keluar"], $lat_kantor, $long_kantor, "K") * 1000;

        if($request["jarak_keluar"] > $radius) {
            Alert::error('Diluar Jangkauan', 'Lokasi Anda Diluar Radius ' . $nama_lokasi);
            return redirect('/lembur');
        } else {
            $foto_jam_keluar = $request["foto_jam_keluar"];

            $image_parts = explode(";base64,", $foto_jam_keluar);

            $image_base64 = base64_decode($image_parts[1]);
            $fileName = 'foto_jam_keluar_lembur/' . uniqid() . '.png';

            Storage::disk('public')->put($fileName, $image_base64);

            $request["foto_jam_keluar"] = $fileName;

            $lembur = Lembur::find($id);

            $jam_masuk = $lembur->jam_masuk;
            $time_masuk = strtotime($jam_masuk);
            $time_keluar = strtotime($request["jam_keluar"]);

            $diff = $time_keluar - $time_masuk;

            $request["total_lembur"] = $diff;

            $validatedData = $request->validate([
                'jam_keluar' => 'required',
                'lat_keluar' => 'required',
                'long_keluar' => 'required',
                'jarak_keluar' => 'required',
                'foto_jam_keluar' => 'required',
                'total_lembur' => 'required'
            ]);

            $kepala_cabang_lokasi = User::whereHas('roles', function ($query) {
                $query->where('name', 'kepala_cabang');
            })->where('lokasi_id', auth()->user()->lokasi_id)->get();

            if ($kepala_cabang_lokasi->count() > 0) {
                $validatedData['status_approval_1'] = 'Pending';
                $lembur->update($validatedData);

                foreach ($kepala_cabang_lokasi as $kc) {
                    $type = 'Approval';
                    $notif = 'Pengajuan Lembur Dari ' . auth()->user()->name . ' Menunggu Persetujuan Anda';
                    $url = url('/data-lembur?user_id='.$lembur->user_id.'&mulai='.$lembur->tanggal.'&akhir='.$lembur->tanggal);

                    $kc->messages = [
                        'user_id'   =>  auth()->user()->id,
                        'from'   =>  auth()->user()->name,
                        'message'   =>  $notif,
                        'action'   =>  '/data-lembur?user_id='.$lembur->user_id.'&mulai='.$lembur->tanggal.'&akhir='.$lembur->tanggal
                    ];
                    $kc->notify(new \App\Notifications\UserNotification);
                    NotifApproval::dispatch($type, $kc->id, $notif, $url);

                    $settings = settings::first();
                    if ($settings->api_url) {
                        Http::post($settings->api_url, [
                            'api_key' => $settings->api_whatsapp,
                            'sender' => $settings->whatsapp,
                            'number' => $kc->telepon,
                            'message' => $notif,
                            'footer' => $url,
                        ]);
                    }
                }
            } else {
                $validatedData['status_approval_1'] = 'Dilewati';
                $lembur->update($validatedData);

                $admins = User::whereHas('roles', function ($query) {
                    $query->where('name', 'admin')->orWhere('name', 'hrd');
                })->get();

                foreach ($admins as $adm) {
                    $type = 'Approval';
                    $notif = 'Pengajuan Lembur Dari ' . auth()->user()->name . ' Butuh Approval Anda (Level Manager Dilewati)';
                    $url = url('/data-lembur?user_id='.$lembur->user_id.'&mulai='.$lembur->tanggal.'&akhir='.$lembur->tanggal);

                    $adm->messages = [
                        'user_id'   =>  auth()->user()->id,
                        'from'   =>  auth()->user()->name,
                        'message'   =>  $notif,
                        'action'   =>  '/data-lembur?user_id='.$lembur->user_id.'&mulai='.$lembur->tanggal.'&akhir='.$lembur->tanggal
                    ];
                    $adm->notify(new \App\Notifications\UserNotification);
                    NotifApproval::dispatch($type, $adm->id, $notif, $url);

                    $settings = settings::first();
                    if ($settings->api_url) {
                        Http::post($settings->api_url, [
                            'api_key' => $settings->api_whatsapp,
                            'sender' => $settings->whatsapp,
                            'number' => $adm->telepon,
                            'message' => $notif,
                            'footer' => $url,
                        ]);
                    }
                }
            }


            return redirect('/lembur')->with('success', 'Berhasil Pulang Lembur');
        }

    }

    public function dataLembur(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $user = User::find(auth()->user()->id);
        $user->update([
            'is_admin' => 'admin'
        ]);

        $tglskrg = date('Y-m-d');

        $user_id = request()->input('user_id');
        $mulai = request()->input('mulai');
        $akhir = request()->input('akhir');

        $data_lembur = Lembur::when(auth()->user()->hasRole('kepala_cabang'), function ($query) {
            return $query->where('lokasi_id', auth()->user()->lokasi_id);
        })
        ->when($user_id, function ($query) use ($user_id) {
            return $query->where('user_id', $user_id);
        })
        ->when(!$mulai && !$akhir, function ($query) use ($tglskrg) {
            return $query->where('tanggal', $tglskrg);
        })
        ->when($mulai && $akhir, function ($query) use ($mulai, $akhir) {
            return $query->whereBetween('tanggal', [$mulai, $akhir]);
        })
        ->orderBy('tanggal', 'ASC')
        ->orderBy('id', 'DESC');

        return view('lembur.datalembur', [
            'title' => 'Data Lembur',
            'user' => User::select('id', 'name')->get(),
            'data_lembur' => $data_lembur->paginate(10)->withQueryString()
        ]);
    }

    public function myLembur(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');
        $tglskrg = date('Y-m-d');

        $user_id = request()->input('user_id');
        $mulai = request()->input('mulai');
        $akhir = request()->input('akhir');

        $data_lembur = Lembur::when(auth()->user()->hasRole('kepala_cabang'), function ($query) {
            return $query->where('lokasi_id', auth()->user()->lokasi_id);
        })
        ->when($user_id, function ($query) use ($user_id) {
            return $query->where('user_id', $user_id);
        })
        ->when(!$mulai && !$akhir, function ($query) use ($tglskrg) {
            return $query->where('tanggal', $tglskrg);
        })
        ->when($mulai && $akhir, function ($query) use ($mulai, $akhir) {
            return $query->whereBetween('tanggal', [$mulai, $akhir]);
        })
        ->when(auth()->user()->is_admin == 'user', function ($query) {
            return $query->where('user_id', auth()->user()->id);
        })
        ->orderBy('tanggal', 'ASC')
        ->orderBy('id', 'DESC');

        return view('lembur.mylemburuser', [
            'title' => 'My Lembur',
            'data_lembur' => $data_lembur->paginate(10)->withQueryString()
        ]);
    }

    public function approvalLevel1(Request $request, $id)
    {
        $lembur = Lembur::findOrFail($id);

        if ($lembur->status_approval_1 !== 'Pending') {
            return redirect('/data-lembur')->with('error', 'Approval Level 1 sudah diproses.');
        }

        $request->validate(['status' => 'required|in:Approved,Rejected']);

        $settings = settings::first();
        $karyawan = User::find($lembur->user_id);
        $url = url('/my-lembur?mulai='.$lembur->tanggal.'&akhir='.$lembur->tanggal);

        if ($request->status === 'Approved') {
            $lembur->update([
                'status_approval_1' => 'Disetujui',
                'user_approval_1'   => auth()->user()->id,
                'catatan_approval_1' => $request->notes,
            ]);

            // Notify admins & hrd for Final Approval (Level 2)
            $admins = User::whereHas('roles', function ($q) {
                $q->where('name', 'admin')->orWhere('name', 'hrd');
            })->get();

            foreach ($admins as $adm) {
                $notif = 'Pengajuan Lembur dari ' . ($karyawan->name ?? '-') . ' telah disetujui Manager, menunggu Final Approval Anda.';
                $adm->messages = ['user_id' => auth()->user()->id, 'from' => auth()->user()->name, 'message' => $notif, 'action' => '/data-lembur'];
                $adm->notify(new \App\Notifications\UserNotification);
                NotifApproval::dispatch('Approval', $adm->id, $notif, url('/data-lembur'));
                if ($settings && $settings->api_url) {
                    Http::post($settings->api_url, ['api_key' => $settings->api_whatsapp, 'sender' => $settings->whatsapp, 'number' => $adm->telepon, 'message' => $notif, 'footer' => url('/data-lembur')]);
                }
            }
            $stat = 'Approve Level 1';
        } else {
            $lembur->update([
                'status_approval_1' => 'Ditolak',
                'status'            => 'Rejected',
                'user_approval_1'   => auth()->user()->id,
                'catatan_approval_1' => $request->notes,
            ]);

            // Notify employee
            $notif = 'Lembur Anda Ditolak oleh ' . auth()->user()->name . ' (Level Manager)';
            if ($karyawan) {
                $karyawan->messages = ['user_id' => auth()->user()->id, 'from' => auth()->user()->name, 'message' => $notif, 'action' => '/my-lembur'];
                $karyawan->notify(new \App\Notifications\UserNotification);
                NotifApproval::dispatch('Rejected', $karyawan->id, $notif, $url);
                if ($settings && $settings->api_url) {
                    Http::post($settings->api_url, ['api_key' => $settings->api_whatsapp, 'sender' => $settings->whatsapp, 'number' => $karyawan->telepon, 'message' => $notif, 'footer' => $url]);
                }
            }
            $stat = 'Reject Level 1';
        }

        return redirect('/data-lembur')->with('success', 'Berhasil ' . $stat . ' Lembur');
    }

    public function approvalLevel2(Request $request, $id)
    {
        $lembur = Lembur::findOrFail($id);

        if (!in_array($lembur->status_approval_1, ['Disetujui', 'Dilewati'])) {
            return redirect('/data-lembur')->with('error', 'Approval Level 1 belum selesai.');
        }
        if (in_array($lembur->status, ['Approved', 'Rejected'])) {
            return redirect('/data-lembur')->with('error', 'Status lembur sudah final.');
        }

        $request->validate(['status' => 'required|in:Approved,Rejected']);

        $settings = settings::first();
        $karyawan = User::find($lembur->user_id);
        $url = url('/my-lembur?mulai='.$lembur->tanggal.'&akhir='.$lembur->tanggal);

        if ($request->status === 'Approved') {
            $lembur->update([
                'status'      => 'Approved',
                'approved_by' => auth()->user()->id,
                'notes'       => $request->notes,
            ]);

            // Notify employee
            $notif = 'Lembur Anda Telah Di Approve oleh ' . auth()->user()->name;
            if ($karyawan) {
                $karyawan->messages = ['user_id' => auth()->user()->id, 'from' => auth()->user()->name, 'message' => $notif, 'action' => '/my-lembur'];
                $karyawan->notify(new \App\Notifications\UserNotification);
                NotifApproval::dispatch('Approved', $karyawan->id, $notif, $url);
                if ($settings && $settings->api_url) {
                    Http::post($settings->api_url, ['api_key' => $settings->api_whatsapp, 'sender' => $settings->whatsapp, 'number' => $karyawan->telepon, 'message' => $notif, 'footer' => $url]);
                }
            }
            $stat = 'Approve Final';
        } else {
            $lembur->update([
                'status'      => 'Rejected',
                'approved_by' => auth()->user()->id,
                'notes'       => $request->notes,
            ]);

            // Notify employee
            $notif = 'Lembur Anda Ditolak oleh ' . auth()->user()->name;
            if ($karyawan) {
                $karyawan->messages = ['user_id' => auth()->user()->id, 'from' => auth()->user()->name, 'message' => $notif, 'action' => '/my-lembur'];
                $karyawan->notify(new \App\Notifications\UserNotification);
                NotifApproval::dispatch('Rejected', $karyawan->id, $notif, $url);
                if ($settings && $settings->api_url) {
                    Http::post($settings->api_url, ['api_key' => $settings->api_whatsapp, 'sender' => $settings->whatsapp, 'number' => $karyawan->telepon, 'message' => $notif, 'footer' => $url]);
                }
            }
            $stat = 'Reject Final';
        }

        return redirect('/data-lembur')->with('success', 'Berhasil ' . $stat . ' Lembur');
    }
}

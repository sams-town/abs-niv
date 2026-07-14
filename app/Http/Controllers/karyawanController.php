<?php

namespace App\Http\Controllers;

use App\Models\Sip;
use App\Models\Cuti;
use App\Models\User;
use App\Models\Shift;
use App\Models\Lembur;
use App\Models\Lokasi;
use App\Models\Jabatan;
use App\Models\Kontrak;
use App\Models\Payroll;
use App\Models\dinasLuar;
use App\Models\ResetCuti;
use App\Models\StatusPajak;
use App\Imports\UsersImport;
use App\Models\MappingShift;
use Illuminate\Http\Request;
use App\Exports\PegawaiExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;



class karyawanController extends Controller
{
    public function index()
    {
        $search = request()->input('search');

        $data = User::pegawai()
                ->when($search, function ($query) use ($search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'LIKE', '%'.$search.'%')
                          ->orWhere('email', 'LIKE', '%'.$search.'%')
                          ->orWhere('telepon', 'LIKE', '%'.$search.'%')
                          ->orWhere('username', 'LIKE', '%'.$search.'%')
                          ->orWhereHas('Jabatan', function ($q2) use ($search) {
                              $q2->where('nama_jabatan', 'LIKE', '%'.$search.'%');
                          });
                    });
                })
                ->orderBy('name', 'ASC')
                ->paginate(10)
                ->withQueryString();

        if (auth()->user()->is_admin == 'admin') {
            $total_pegawai = User::pegawai()->count();
            $aktif_pegawai = User::pegawai()->where(function ($query) {
                $query->whereNull('masa_berlaku')
                      ->orWhere('masa_berlaku', '>', date('Y-m-d'));
            })->count();
            $cuti_pegawai = Cuti::where('tanggal', date('Y-m-d'))
                                ->where('status_cuti', 'Diterima')
                                ->where('nama_cuti', 'Cuti')
                                ->count();
            $baru_bulan_ini = User::pegawai()->whereMonth('created_at', date('m'))
                                  ->whereYear('created_at', date('Y'))
                                  ->count();

            // Calculate location distribution data
            $total_lokasi = \App\Models\Lokasi::count();
            $lokasi_counts = User::pegawai()->select('lokasi_id', \DB::raw('count(*) as total'))
                ->whereNotNull('lokasi_id')
                ->groupBy('lokasi_id')
                ->orderBy('total', 'desc')
                ->get();
            
            $distribusi_lokasi = [];
            $lokasi_terbesar = 'None';
            $max_lokasi_count = 0;
            
            foreach ($lokasi_counts as $lc) {
                $lokasi = \App\Models\Lokasi::find($lc->lokasi_id);
                $nama_lokasi = $lokasi ? $lokasi->nama_lokasi : 'Unknown';
                $pct = $total_pegawai > 0 ? round(($lc->total / $total_pegawai) * 100, 1) : 0;
                $distribusi_lokasi[] = [
                    'label' => $nama_lokasi,
                    'count' => $lc->total,
                    'percentage' => $pct
                ];
                if ($lc->total > $max_lokasi_count) {
                    $max_lokasi_count = $lc->total;
                    $lokasi_terbesar = $nama_lokasi;
                }
            }

            // Calculate domicile (Domisili KTP) distribution data
            $cities = ['Jakarta', 'Bogor', 'Depok', 'Tangerang', 'Bekasi', 'Bandung', 'Surabaya', 'Tasikmalaya', 'Semarang', 'Yogyakarta', 'Sukabumi', 'Cianjur', 'Garut', 'Cirebon'];
            $domisili_raw = [];
            foreach (User::pegawai()->get() as $u) {
                $alamat = $u->alamat;
                $found_domisili = 'Lainnya';
                if ($alamat) {
                    if (preg_match('/\b\d{5}\b/', $alamat, $matches)) {
                        $found_domisili = $matches[0];
                    } else {
                        foreach ($cities as $city) {
                            if (stripos($alamat, $city) !== false) {
                                $found_domisili = $city;
                                break;
                            }
                        }
                    }
                }
                $domisili_raw[$found_domisili] = ($domisili_raw[$found_domisili] ?? 0) + 1;
            }
            
            arsort($domisili_raw);
            $total_provinsi = count(array_keys($domisili_raw));
            
            $provinsi_terbesar = 'Lainnya';
            $max_prov_count = 0;
            foreach ($domisili_raw as $k => $v) {
                if ($k !== 'Lainnya' && $v > $max_prov_count) {
                    $max_prov_count = $v;
                    $provinsi_terbesar = $k;
                }
            }
            if ($provinsi_terbesar === 'Lainnya' && !empty($domisili_raw)) {
                reset($domisili_raw);
                $provinsi_terbesar = key($domisili_raw);
            }

            $distribusi_domisili = [];
            foreach ($domisili_raw as $k => $v) {
                $pct = $total_pegawai > 0 ? round(($v / $total_pegawai) * 100, 1) : 0;
                $distribusi_domisili[] = [
                    'label' => $k,
                    'count' => $v,
                    'percentage' => $pct
                ];
            }

            return view('karyawan.index', [
                'title' => 'Pegawai',
                'data_user' => $data,
                'total_pegawai' => $total_pegawai,
                'aktif_pegawai' => $aktif_pegawai,
                'cuti_pegawai' => $cuti_pegawai,
                'baru_bulan_ini' => $baru_bulan_ini,
                'total_lokasi' => $total_lokasi,
                'lokasi_terbesar' => $lokasi_terbesar,
                'distribusi_lokasi' => $distribusi_lokasi,
                'total_provinsi' => $total_provinsi,
                'provinsi_terbesar' => $provinsi_terbesar,
                'distribusi_domisili' => $distribusi_domisili,
            ]);
        } else {
            return view('karyawan.indexUser', [
                'title' => 'Pegawai',
                'data_user' => $data
            ]);
        }
    }

    public function kontrak($id)
    {

        $user = User::find($id);
        $title = 'List Kontrak';
        $kontraks = Kontrak::where('user_id', $id)
                            ->orderBy('tanggal', 'DESC')
                            ->paginate(10)
                            ->withQueryString();

        return view('karyawan.kontrak', compact(
            'title',
            'kontraks',
            'user'
        ));
    }

    public function export()
    {
        return (new PegawaiExport($_GET))->download('List Pegawai.xlsx');
    }


    public function kartuPegawai()
    {
        $title = 'Kartu Pegawai';

        return view('karyawan.kartuPegawai', compact(
            'title',
        ));
    }

    public function qrcode($id)
    {
        $title = 'Kartu';
        $user = User::find($id);

        return view('karyawan.qrcode', compact(
            'title',
            'user',
        ));
    }

    public function print($id)
    {
        $user = User::find($id);
        $pdf = Pdf::loadView('karyawan.print', [
            'title' => 'Kartu',
            'user' => $user
        ]);

        $pdf->setPaper('A6', 'portrait');
        return $pdf->stream('kartu-pegawai.pdf');
    }

    public function euforia()
    {
        date_default_timezone_set('Asia/Jakarta');

        $data = User::where('tgl_lahir', date('Y-m-d'))
                ->orderBy('name', 'ASC')
                ->paginate(10)
                ->withQueryString();

        return view('karyawan.euforia', [
            'title' => 'Euforia',
            'data_user' => $data
        ]);

    }

    public function show($id)
    {
        $user = User::find($id);

        return view('karyawan.show', [
            'title' => 'Detail Karyawan',
            'user' => $user
        ]);
    }

    public function importUsers(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xls,xlsx,csv|max:5000'
        ]);
        $nama_file = $request->file('file_excel')->store('file_excel');

        Excel::import(new UsersImport, public_path('/storage/'.$nama_file));
        return back()->with('success', 'Data Berhasil Di Import');
    }

    public function tambahKaryawan()
    {
        return view('karyawan.tambah',[
            "title" => 'Tambah Pegawai',
            "data_jabatan" => Jabatan::all(),
            "data_lokasi" => Lokasi::where('status', 'approved')->where('keterangan', 'Office')->get(),
            "status_pajak" => StatusPajak::orderBy('id')->get(),
            "roles" => Role::orderBy('name')->get()
        ]);
    }

    public function tambahKaryawanProses(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email:dns|unique:users',
            'telepon' => 'required',
            'foto_karyawan' => 'image|file|max:10240',
            'username' => 'required|max:255|unique:users',
            'password' => 'required|min:6|max:255',
            'lokasi_id' => 'required',
            'tgl_lahir' => 'required',
            'tgl_join' => 'required',
            'gender' => 'required',
            'is_admin' => 'required',
            'status_pajak_id' => 'required',
            'jabatan_id' => 'required',
            'ktp' => 'nullable',
            'kartu_keluarga' => 'nullable',
            'bpjs_kesehatan' => 'nullable',
            'bpjs_ketenagakerjaan' => 'nullable',
            'npwp' => 'nullable',
            'sim' => 'nullable',
            'no_pkwt' => 'nullable',
            'no_kontrak' => 'nullable',
            'tanggal_mulai_pkwt' => 'nullable',
            'tanggal_berakhir_pkwt' => 'nullable',
            'rekening' => 'nullable',
            'nama_rekening' => 'nullable',
            'alamat' => 'nullable',
            'izin_cuti' => 'nullable',
            'izin_lainnya' => 'nullable',
            'izin_telat' => 'nullable',
            'izin_pulang_cepat' => 'nullable',
            'gaji_pokok' => 'nullable',
            'tunjangan_makan' => 'nullable',
            'tunjangan_transport' => 'nullable',
            'tunjangan_bpjs_kesehatan' => 'nullable',
            'tunjangan_bpjs_ketenagakerjaan' => 'nullable',
            'lembur' => 'nullable',
            'kehadiran' => 'nullable',
            'thr' => 'nullable',
            'bonus_pribadi' => 'nullable',
            'bonus_team' => 'nullable',
            'bonus_jackpot' => 'nullable',
            'izin' => 'nullable',
            'terlambat' => 'nullable',
            'mangkir' => 'nullable',
            'saldo_kasbon' => 'nullable',
            'potongan_bpjs_kesehatan' => 'nullable',
            'potongan_bpjs_ketenagakerjaan' => 'nullable',
            'masa_berlaku' => 'nullable',
            'nama_ibu_kandung' => 'nullable',
            'kontak_darurat_nama' => 'nullable',
            'kontak_darurat_hp' => 'nullable',
            'kontak_darurat_hubungan' => 'nullable',
            'alamat_domisili' => 'nullable',
            'cuti_melahirkan' => 'nullable',
            'cuti_kematian' => 'nullable',
            'batas_terlambat' => 'nullable',
            'kasbon_obat' => 'nullable',
            'potongan_koperasi' => 'nullable',
        ]);

        $validatedData["izin_cuti"] = $request->izin_cuti ?? 0;
        $validatedData["izin_lainnya"] = $request->izin_lainnya ?? 0;
        $validatedData["izin_telat"] = $request->izin_telat ?? 0;
        $validatedData["izin_pulang_cepat"] = $request->izin_pulang_cepat ?? 0;
        $validatedData["cuti_melahirkan"] = $request->cuti_melahirkan ?? 90;
        $validatedData["cuti_kematian"] = $request->cuti_kematian ?? 3;
        $validatedData["batas_terlambat"] = $request->batas_terlambat ?? 5;

        $validatedData['gaji_pokok'] = $request->gaji_pokok ? str_replace(',', '', $request->gaji_pokok) : 0;
        $validatedData['tunjangan_makan'] = $request->tunjangan_makan ? str_replace(',', '', $request->tunjangan_makan) : 0;
        $validatedData['tunjangan_transport'] = $request->tunjangan_transport ? str_replace(',', '', $request->tunjangan_transport) : 0;
        $validatedData['tunjangan_bpjs_kesehatan'] = $request->tunjangan_bpjs_kesehatan ? str_replace(',', '', $request->tunjangan_bpjs_kesehatan) : 0;
        $validatedData['tunjangan_bpjs_ketenagakerjaan'] = $request->tunjangan_bpjs_ketenagakerjaan ? str_replace(',', '', $request->tunjangan_bpjs_ketenagakerjaan) : 0;
        $validatedData['lembur'] = $request->lembur ? str_replace(',', '', $request->lembur) : 0;
        $validatedData['kehadiran'] = $request->kehadiran ? str_replace(',', '', $request->kehadiran) : 0;
        $validatedData['thr'] = $request->thr ? str_replace(',', '', $request->thr) : 0;
        $validatedData['bonus_pribadi'] = $request->bonus_pribadi ? str_replace(',', '', $request->bonus_pribadi) : 0;
        $validatedData['bonus_team'] = $request->bonus_team ? str_replace(',', '', $request->bonus_team) : 0;
        $validatedData['bonus_jackpot'] = $request->bonus_jackpot ? str_replace(',', '', $request->bonus_jackpot) : 0;
        $validatedData['izin'] = $request->izin ? str_replace(',', '', $request->izin) : 0;
        $validatedData['terlambat'] = $request->terlambat ? str_replace(',', '', $request->terlambat) : 0;
        $validatedData['mangkir'] = $request->mangkir ? str_replace(',', '', $request->mangkir) : 0;
        $validatedData['saldo_kasbon'] = $request->saldo_kasbon ? str_replace(',', '', $request->saldo_kasbon) : 0;
        $validatedData['potongan_bpjs_kesehatan'] = $request->potongan_bpjs_kesehatan ? str_replace(',', '', $request->potongan_bpjs_kesehatan) : 0;
        $validatedData['potongan_bpjs_ketenagakerjaan'] = $request->potongan_bpjs_ketenagakerjaan ? str_replace(',', '', $request->potongan_bpjs_ketenagakerjaan) : 0;
        $validatedData['kasbon_obat'] = $request->kasbon_obat ? str_replace(',', '', $request->kasbon_obat) : 0;
        $validatedData['potongan_koperasi'] = $request->potongan_koperasi ? str_replace(',', '', $request->potongan_koperasi) : 0;

        if ($request->file('foto_karyawan')) {
            $validatedData['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan');
        }

        $validatedData['password'] = Hash::make($validatedData['password']);
        $user = User::create($validatedData);

        if ($request->role) {
            foreach($request->role as $role){
                $user->assignRole($role);
            }
        }
        if ($request->hasFile('document_files')) {
            $files = $request->file('document_files');
            $names = $request->document_names;
            foreach ($files as $index => $file) {
                if ($file->isValid()) {
                    $path = $file->store('files/' . $user->username);
                    \App\Models\File::create([
                        'jenis_file' => $names[$index] ?? 'Dokumen Tambahan',
                        'user_id' => $user->id,
                        'fileUpload' => $path
                    ]);
                }
            }
        }
        return redirect('/pegawai')->with('success', 'Data Berhasil di Tambahkan');
    }

    public function detail($id)
    {
        $user = User::find($id);
        return view('karyawan.editkaryawan', [
            'title' => 'Detail Pegawai',
            'karyawan' => $user,
            'data_jabatan' => Jabatan::all(),
            "data_lokasi" => Lokasi::where('status', 'approved')->where('keterangan', 'Office')->get(),
            "status_pajak" => StatusPajak::orderBy('id')->get(),
            "roles" => Role::orderBy('name')->get(),
            'user_roles' => $user->roles->pluck('name')->toArray()
        ]);
    }

    public function editKaryawanProses(Request $request, $id)
    {
        $rules = [
            'name' => 'required|max:255',
            'telepon' => 'required',
            'foto_karyawan' => 'image|file|max:10240',
            'lokasi_id' => 'required',
            'tgl_lahir' => 'required',
            'tgl_join' => 'required',
            'gender' => 'required',
            'is_admin' => 'required',
            'status_pajak_id' => 'required',
            'jabatan_id' => 'required',
            'ktp' => 'nullable',
            'kartu_keluarga' => 'nullable',
            'bpjs_kesehatan' => 'nullable',
            'bpjs_ketenagakerjaan' => 'nullable',
            'npwp' => 'nullable',
            'sim' => 'nullable',
            'no_pkwt' => 'nullable',
            'no_kontrak' => 'nullable',
            'tanggal_mulai_pkwt' => 'nullable',
            'tanggal_berakhir_pkwt' => 'nullable',
            'rekening' => 'nullable',
            'nama_rekening' => 'nullable',
            'alamat' => 'nullable',
            'izin_cuti' => 'nullable',
            'izin_lainnya' => 'nullable',
            'izin_telat' => 'nullable',
            'izin_pulang_cepat' => 'nullable',
            'gaji_pokok' => 'nullable',
            'tunjangan_makan' => 'nullable',
            'tunjangan_transport' => 'nullable',
            'tunjangan_bpjs_kesehatan' => 'nullable',
            'tunjangan_bpjs_ketenagakerjaan' => 'nullable',
            'lembur' => 'nullable',
            'kehadiran' => 'nullable',
            'thr' => 'nullable',
            'bonus_pribadi' => 'nullable',
            'bonus_team' => 'nullable',
            'bonus_jackpot' => 'nullable',
            'izin' => 'nullable',
            'terlambat' => 'nullable',
            'mangkir' => 'nullable',
            'saldo_kasbon' => 'nullable',
            'potongan_bpjs_kesehatan' => 'nullable',
            'potongan_bpjs_ketenagakerjaan' => 'nullable',
            'masa_berlaku' => 'nullable',
        ];

        $user = User::find($id);

        foreach($user->roles as $r){
            $user->removeRole($r->name);
        }

        if ($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }

        if ($request->username != $user->username) {
            $rules['username'] = 'required|max:255|unique:users';
        }

        $validatedData = $request->validate($rules);

        $validatedData["izin_cuti"] = $request->izin_cuti ?? 0;
        $validatedData["izin_lainnya"] = $request->izin_lainnya ?? 0;
        $validatedData["izin_telat"] = $request->izin_telat ?? 0;
        $validatedData["izin_pulang_cepat"] = $request->izin_pulang_cepat ?? 0;

        $validatedData['gaji_pokok'] = $request->gaji_pokok ? str_replace(',', '', $request->gaji_pokok) : 0;
        $validatedData['tunjangan_makan'] = $request->tunjangan_makan ? str_replace(',', '', $request->tunjangan_makan) : 0;
        $validatedData['tunjangan_transport'] = $request->tunjangan_transport ? str_replace(',', '', $request->tunjangan_transport) : 0;
        $validatedData['tunjangan_bpjs_kesehatan'] = $request->tunjangan_bpjs_kesehatan ? str_replace(',', '', $request->tunjangan_bpjs_kesehatan) : 0;
        $validatedData['tunjangan_bpjs_ketenagakerjaan'] = $request->tunjangan_bpjs_ketenagakerjaan ? str_replace(',', '', $request->tunjangan_bpjs_ketenagakerjaan) : 0;
        $validatedData['makan_transport'] = $request->makan_transport ? str_replace(',', '', $request->makan_transport) : 0;
        $validatedData['lembur'] = $request->lembur ? str_replace(',', '', $request->lembur) : 0;
        $validatedData['kehadiran'] = $request->kehadiran ? str_replace(',', '', $request->kehadiran) : 0;
        $validatedData['thr'] = $request->thr ? str_replace(',', '', $request->thr) : 0;
        $validatedData['bonus_pribadi'] = $request->bonus_pribadi ? str_replace(',', '', $request->bonus_pribadi) : 0;
        $validatedData['bonus_team'] = $request->bonus_team ? str_replace(',', '', $request->bonus_team) : 0;
        $validatedData['bonus_jackpot'] = $request->bonus_jackpot ? str_replace(',', '', $request->bonus_jackpot) : 0;
        $validatedData['izin'] = $request->izin ? str_replace(',', '', $request->izin) : 0;
        $validatedData['terlambat'] = $request->terlambat ? str_replace(',', '', $request->terlambat) : 0;
        $validatedData['mangkir'] = $request->mangkir ? str_replace(',', '', $request->mangkir) : 0;
        $validatedData['saldo_kasbon'] = $request->saldo_kasbon ? str_replace(',', '', $request->saldo_kasbon) : 0;
        $validatedData['potongan_bpjs_kesehatan'] = $request->potongan_bpjs_kesehatan ? str_replace(',', '', $request->potongan_bpjs_kesehatan) : 0;
        $validatedData['potongan_bpjs_ketenagakerjaan'] = $request->potongan_bpjs_ketenagakerjaan ? str_replace(',', '', $request->potongan_bpjs_ketenagakerjaan) : 0;

        if ($request->file('foto_karyawan')) {
            if ($request->foto_karyawan_lama) {
                Storage::delete($request->foto_karyawan_lama);
            }
            $validatedData['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan');
        }

        $path = public_path('neural.json');
        $neural = File::get($path);
        $dataface = json_decode($neural, true);

        foreach ($dataface as &$item) {
            if ($item['label'] === $user->username) {
                $item['label'] = $request->username;
            }
        }

        File::put($path, json_encode($dataface, JSON_PRETTY_PRINT));

        $user->update($validatedData);
        if ($request->role) {
            foreach($request->role as $role){
                $user->assignRole($role);
            }
        }

        $request->session()->flash('success', 'Data Berhasil di Update');
        return redirect('/pegawai');
    }

    public function deleteKaryawan($id)
    {
        $delete = User::find($id);
        MappingShift::where('user_id', $id)->delete();
        Lembur::where('user_id', $id)->delete();
        Cuti::where('user_id', $id)->delete();
        Sip::where('user_id', $id)->delete();
        Payroll::where('user_id', $id)->delete();
        Storage::delete($delete->foto_karyawan);
        $path = public_path('neural.json');
        $neural = File::get($path);
        $dataface = json_decode($neural, true);

        $filterface = array_filter($dataface, function($item) use ($delete) {
            return $item['label'] !== $delete->username;
        });
        File::put($path, json_encode(array_values($filterface), JSON_PRETTY_PRINT));
        $delete->delete();
        return redirect('/pegawai')->with('success', 'Data Berhasil di Delete');
    }

    public function editpassword($id)
    {
        return view('karyawan.editpassword', [
            'title' => 'Edit Password',
            'karyawan' => User::find($id)
        ]);
    }

    public function face($id)
    {
        return view('karyawan.face', [
            'title' => 'Daftar Wajah',
            'karyawan' => User::find($id)
        ]);
    }

    public function ajaxDescrip(Request $request)
    {
        $path = public_path('neural.json');
        $neural = File::get($path);
        $dataface = json_decode($neural, true);
        $user = User::find($request->user_id);

        $filterface = array_filter($dataface, function($item) use ($user) {
            return $item['label'] !== $user->username;
        });

        File::put($path, json_encode(array_values($filterface), JSON_PRETTY_PRINT));

        $json = file_get_contents('neural.json');
        if(strlen($json) > 4){
            $string = ',' . $request["myData"];
        }
        else{
            $string = $request["myData"];
        }
        $position = strlen($json) - 1;
        $out = substr_replace( $json, $string, $position, 0 );
        file_put_contents('neural.json', $out);
    }

    public function ajaxPhoto(Request $request)
    {
        $image = $request["image"];

        $image_parts = explode(";base64,", $image);

        $image_base64 = base64_decode($image_parts[1]);
        $fileName = 'foto_face_recognition/' . $request["path"] . '.png';

        Storage::disk('public')->put($fileName, $image_base64);

        $user = User::where('username', $request['path'])->update(["foto_face_recognition" => $fileName]);
        return $user;
    }

    public function editPasswordProses(Request $request, $id)
    {
        $validatedData = $request->validate([
            'password' => 'required|min:6|max:255',
        ]);

        $validatedData['password'] = Hash::make($request->password);

        User::where('id', $id)->update($validatedData);
        $request->session()->flash('success', 'Password Berhasil Diganti');
        return redirect('/pegawai');
    }

    public function shift($id)
    {
        $tanggal = request()->input('tanggal');
        $mapping_shift = MappingShift::where('user_id', $id)
                                    ->when($tanggal, function ($query) use ($tanggal) {
                                        return $query->where('tanggal', $tanggal);
                                    })
                                    ->orderBy('tanggal', 'DESC')
                                    ->paginate(10)
                                    ->withQueryString();
        return view('karyawan.mappingshift', [
            'title' => 'Mapping Shift',
            'karyawan' => User::find($id),
            'shift_karyawan' => $mapping_shift,
            'shift' => Shift::all()
        ]);
    }

    public function dinasLuar($id)
    {
        $tanggal = request()->input('tanggal');
        $dinas_luar = dinasLuar::where('user_id', $id)
                        ->when($tanggal, function ($query) use ($tanggal) {
                            return $query->where('tanggal', $tanggal);
                        })
                        ->orderBy('id', 'desc')
                        ->paginate(10)
                        ->withQueryString();
        return view('karyawan.dinasluar', [
            'title' => 'Mapping Dinas Luar',
            'karyawan' => User::find($id),
            'dinas_luar' => $dinas_luar,
            'shift' => Shift::all()
        ]);
    }

    public function prosesTambahShift(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        $request->validate([
            'shift_id' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_akhir' => 'required',
        ]);

        if($request["tanggal_mulai"] == null) {
            $request["tanggal_mulai"] = $request["tanggal_akhir"];
        } else {
            $request["tanggal_mulai"] = $request["tanggal_mulai"];
        }

        if($request["tanggal_akhir"] == null) {
            $request["tanggal_akhir"] = $request["tanggal_mulai"];
        } else {
            $request["tanggal_akhir"] = $request["tanggal_akhir"];
        }

        $begin = new \DateTime($request["tanggal_mulai"]);
        $end = new \DateTime($request["tanggal_akhir"]);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D'); //referensi : https://en.wikipedia.org/wiki/ISO_8601#Durations
        $daterange = new \DatePeriod($begin, $interval ,$end);


        foreach ($daterange as $date) {
            $tanggal = $date->format("Y-m-d");

            $cek = MappingShift::where('user_id', $request['user_id'])->where('tanggal', $tanggal)->first();

            if (!$cek) {
                if ($request["shift_id"] == 1) {
                    $request["status_absen"] = "Libur";
                } else {
                    $request["status_absen"] = "Tidak Masuk";
                }

                $request["tanggal"] = $tanggal;

                $validatedData = $request->validate([
                    'user_id' => 'required',
                    'shift_id' => 'required',
                    'tanggal' => 'required',
                    'status_absen' => 'required',
                ]);

                $validatedData['lock_location'] = $request['lock_location'] ? $request['lock_location'] : null;
                $validatedData['telat'] = 0;
                $validatedData['pulang_cepat'] = 0;

                MappingShift::create($validatedData);
            }
        }
        return redirect('/pegawai/shift/' . $request["user_id"])->with('success', 'Data Berhasil di Tambahkan');
    }

    public function prosesTambahDinas(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        if($request["tanggal_mulai"] == null) {
            $request["tanggal_mulai"] = $request["tanggal_akhir"];
        } else {
            $request["tanggal_mulai"] = $request["tanggal_mulai"];
        }

        if($request["tanggal_akhir"] == null) {
            $request["tanggal_akhir"] = $request["tanggal_mulai"];
        } else {
            $request["tanggal_akhir"] = $request["tanggal_akhir"];
        }

        $begin = new \DateTime($request["tanggal_mulai"]);
        $end = new \DateTime($request["tanggal_akhir"]);
        $end = $end->modify('+1 day');

        $interval = new \DateInterval('P1D'); //referensi : https://en.wikipedia.org/wiki/ISO_8601#Durations
        $daterange = new \DatePeriod($begin, $interval ,$end);


        foreach ($daterange as $date) {
            $tanggal = $date->format("Y-m-d");

            if ($request["shift_id"] == 1) {
                $request["status_absen"] = "Libur";
            } else {
                $request["status_absen"] = "Tidak Masuk";
            }

            $request["tanggal"] = $tanggal;

            $validatedData = $request->validate([
                'user_id' => 'required',
                'shift_id' => 'required',
                'tanggal' => 'required',
                'status_absen' => 'required',
            ]);

            dinasLuar::create($validatedData);
        }
        return redirect('/pegawai/dinas-luar/' . $request["user_id"])->with('success', 'Data Berhasil di Tambahkan');
    }

    public function deleteShift(Request $request, $id)
    {
        $delete = MappingShift::find($id);
        $delete->delete();
        return redirect('/pegawai/shift/' . $request["user_id"])->with('success', 'Data Berhasil di Delete');
    }

    public function deleteDinas(Request $request, $id)
    {
        $delete = dinasLuar::find($id);
        $delete->delete();
        return redirect('/pegawai/dinas-luar/' . $request["user_id"])->with('success', 'Data Berhasil di Delete');
    }

    public function editShift($id)
    {
        return view('karyawan.editshift', [
            'title' => 'Edit Shift',
            'shift_karyawan' => MappingShift::find($id),
            'shift' => Shift::all()
        ]);
    }

    public function editDinas($id)
    {
        return view('karyawan.editdinas', [
            'title' => 'Edit Dinas',
            'dinas_luar' => dinasLuar::find($id),
            'shift' => Shift::all()
        ]);
    }

    public function prosesEditShift(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');


        if ($request["shift_id"] == 1) {
            $request["status_absen"] = "Libur";
        } else {
            $request["status_absen"] = "Tidak Masuk";
        }

        $validatedData = $request->validate([
            'shift_id' => 'required',
            'tanggal' => 'required',
            'status_absen' => 'required'
        ]);

        $validatedData['lock_location'] = $request['lock_location'] ? $request['lock_location'] : null;

        MappingShift::where('id', $id)->update($validatedData);
        return redirect('/pegawai/shift/' . $request["user_id"])->with('success', 'Data Berhasil di Update');
    }

    public function prosesEditDinas(Request $request, $id)
    {
        date_default_timezone_set('Asia/Jakarta');


        if ($request["shift_id"] == 1) {
            $request["status_absen"] = "Libur";
        } else {
            $request["status_absen"] = "Tidak Masuk";
        }

        $validatedData = $request->validate([
            'shift_id' => 'required',
            'tanggal' => 'required',
            'status_absen' => 'required'
        ]);

        dinasLuar::where('id', $id)->update($validatedData);
        return redirect('/pegawai/dinas-luar/' . $request["user_id"])->with('success', 'Data Berhasil di Update');
    }

    public function myProfile()
    {
        $user = User::find(auth()->user()->id);
        if (auth()->user()->is_admin == 'admin') {
            return view('karyawan.myprofile', [
                'title' => 'My Profile',
                'karyawan' => $user,
                'data_jabatan' => Jabatan::all(),
                "data_lokasi" => Lokasi::where('status', 'approved')->where('keterangan', 'Office')->get(),
                "status_pajak" => StatusPajak::orderBy('id')->get(),
                "roles" => Role::orderBy('name')->get(),
                'user_roles' => $user->roles->pluck('name')->toArray()
            ]);
        } else {
            return view('karyawan.myprofileuser', [
                'title' => 'My Profile',
                'karyawan' => $user,
                'data_jabatan' => Jabatan::all(),
                "data_lokasi" => Lokasi::where('status', 'approved')->where('keterangan', 'Office')->get(),
                "status_pajak" => StatusPajak::orderBy('id')->get(),
                "roles" => Role::orderBy('name')->get(),
                'user_roles' => $user->roles->pluck('name')->toArray()
            ]);
        }
    }

    public function myProfileUpdate(Request $request, $id)
    {
        $rules = [
            'name' => 'required|max:255',
            'telepon' => 'required',
            'foto_karyawan' => 'image|file|max:10240',
            'lokasi_id' => 'nullable',
            'tgl_lahir' => 'nullable',
            'tgl_join' => 'nullable',
            'gender' => 'nullable',
            'is_admin' => 'nullable',
            'status_pajak_id' => 'nullable',
            'jabatan_id' => 'nullable',
            'ktp' => 'nullable',
            'kartu_keluarga' => 'nullable',
            'bpjs_kesehatan' => 'nullable',
            'bpjs_ketenagakerjaan' => 'nullable',
            'npwp' => 'nullable',
            'sim' => 'nullable',
            'no_pkwt' => 'nullable',
            'no_kontrak' => 'nullable',
            'tanggal_mulai_pkwt' => 'nullable',
            'tanggal_berakhir_pkwt' => 'nullable',
            'rekening' => 'nullable',
            'nama_rekening' => 'nullable',
            'alamat' => 'nullable',
        ];

        $user = User::find($id);

        if ($request->email != $user->email) {
            $rules['email'] = 'required|email:dns|unique:users';
        }

        if ($request->username != $user->username) {
            $rules['username'] = 'required|max:255|unique:users';
        }

        $validatedData = $request->validate($rules);

        if ($request->file('foto_karyawan')) {
            if ($request->foto_karyawan_lama) {
                Storage::delete($request->foto_karyawan_lama);
            }
            $validatedData['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan');
        }

        $path = public_path('neural.json');
        $neural = File::get($path);
        $dataface = json_decode($neural, true);

        foreach ($dataface as &$item) {
            if ($item['label'] === $user->username) {
                $item['label'] = $request->username;
            }
        }

        File::put($path, json_encode($dataface, JSON_PRETTY_PRINT));

        $user->update($validatedData);

        $request->session()->flash('success', 'Data Berhasil di Update');
        return redirect('/my-profile');
    }

    public function editPassMyProfile()
    {
        if (auth()->user()->is_admin == 'admin') {
            return view('karyawan.editpassmyprofile', [
                'title' => 'Ganti Password'
            ]);
        } else {
            return view('karyawan.editpassworduser', [
                'title' => 'Ganti Password'
            ]);
        }
    }

    public function editPassMyProfileProses(Request $request, $id)
    {
        $validatedData = $request->validate([
            'password' => 'required|min:6|max:255|confirmed',
        ]);

        $validatedData['password'] = Hash::make($request->password);

        User::where('id', $id)->update($validatedData);
        $request->session()->flash('success', 'Password Berhasil di Update');
        return redirect('/dashboard');
    }

    public function resetCuti()
    {
        return view('karyawan.masterreset', [
            'title' => 'Master Data Reset Cuti',
            'data_cuti' => ResetCuti::first()
        ]);
    }

    public function resetCutiProses(Request $request, $id)
    {
        $validatedData = $request->validate([
            'izin_cuti' => 'required',
            'izin_dinas_luar' => 'required',
            'izin_sakit' => 'required',
            'izin_cek_kesehatan' => 'required',
            'izin_keperluan_pribadi' => 'required',
            'izin_lainnya' => 'required',
            'izin_telat' => 'required',
            'izin_pulang_cepat' => 'required'
        ]);

        ResetCuti::where('id', $id)->update($validatedData);
        return redirect('/reset-cuti')->with('success', 'Master Cuti Berhasil Diupdate');
    }

    public function switchUser()
    {
        $user = User::find(auth()->user()->id);
        $user->update([
            'is_admin' => 'user'
        ]);

        return redirect('/dashboard')->with('success', 'Berhasil Pindah Dashboard User');
    }

    public function switchAdmin()
    {
        $user = User::find(auth()->user()->id);
        $user->update([
            'is_admin' => 'admin'
        ]);

        return redirect('/dashboard')->with('success', 'Berhasil Pindah Dashboard Admin');
    }
}

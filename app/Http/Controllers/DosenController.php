<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jabatan;
use App\Models\Lokasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;

class DosenController extends Controller
{
    public function index()
    {
        $search = request('search');
        $data = User::dosen()
            ->when($search, fn($q) => $q->where('name', 'LIKE', "%{$search}%")
                ->orWhere('nidn', 'LIKE', "%{$search}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $total_pegawai = User::dosen()->count();
        $aktif_pegawai = User::dosen()->where('status_aktif', true)->count();
        $cuti_pegawai = \App\Models\Cuti::whereHas('User', function($q){ $q->where('tipe_user', 'dosen'); })
                            ->where('tanggal', date('Y-m-d'))
                            ->where('status_cuti', 'Diterima')
                            ->where('nama_cuti', 'Cuti')
                            ->count();
        $baru_bulan_ini = User::dosen()->whereMonth('created_at', date('m'))
                              ->whereYear('created_at', date('Y'))
                              ->count();

        // Calculate location distribution data
        $total_lokasi = \App\Models\Lokasi::count();
        $lokasi_counts = User::dosen()->select('lokasi_id', \DB::raw('count(*) as total'))
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
        foreach (User::dosen()->get() as $u) {
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

        return view('dosen.index', [
            'title'     => 'Data Dosen',
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
    }

    public function create()
    {
        return view('dosen.tambah', [
            'title'     => 'Tambah Dosen',
            'jabatan'   => Jabatan::orderBy('nama_jabatan')->get(),
            'lokasi'    => Lokasi::where('status', 'approved')->orderBy('nama_lokasi')->get(),
            'roles'     => \Spatie\Permission\Models\Role::orderBy('name')->get(),
            'status_pajak' => \App\Models\StatusPajak::orderBy('id')->get(),
            'skemas'    => \App\Models\MasterSkemaHonorarium::orderBy('nama_skema')->get(),
            'mata_kuliah' => \App\Models\MataKuliah::orderBy('nama_mk')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|max:255',
            'email'           => 'required|email|unique:users,email',
            'nidn'            => ['required', 'string', Rule::unique('users', 'nidn')->where('status_aktif', true)],
            'nip'             => 'nullable|string|max:255',
            'gelar_depan'     => 'nullable|string|max:255',
            'gelar_belakang'  => 'nullable|string|max:255',
            'program_studi'   => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'status_kepegawaian' => 'nullable|string|max:255',
            'tipe_honorarium' => 'required|string|max:255',
            'nominal_honor'   => 'required',
            'jabatan_akademik' => 'nullable|string|max:255',
            'mata_kuliah'     => 'required|array',
            'telepon'         => 'required|string|max:20',
            'password'        => 'required|min:6',
            'lokasi_id'       => 'required',
            'username'        => 'required|max:255|unique:users,username',
            'tgl_lahir'       => 'required|date',
            'tgl_join'        => 'required|date',
            'gender'          => 'required',
            'jabatan_id'      => 'required',
            'is_admin'        => 'required',
            'nama_ibu_kandung'=> 'required',
            'status_pajak_id' => 'required',
            'alamat'          => 'nullable',
            'alamat_domisili' => 'nullable',
            'master_skema_honorarium_id' => 'nullable|exists:master_skema_honorariums,id'
        ], [
            'nidn.unique' => 'NIDN sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        $data = $request->except(['password', 'role', 'nominal_honor', 'mata_kuliah', 'foto_karyawan']);

        $nominal_honor = $request->nominal_honor ? str_replace('.', '', $request->nominal_honor) : 0;
        $nominal_honor = str_replace(',', '', $nominal_honor);
        $data['nominal_honor'] = $nominal_honor;

        $mata_kuliah_str = $request->mata_kuliah ? implode(', ', $request->mata_kuliah) : null;
        $data['mata_kuliah'] = $mata_kuliah_str;

        // Clean Rupiah inputs from Penjumlahan Gaji & Potongan
        $rupiahFields = [
            'gaji_pokok', 'tunjangan_makan', 'tunjangan_transport', 
            'tunjangan_bpjs_kesehatan', 'tunjangan_bpjs_ketenagakerjaan', 
            'lembur', 'kehadiran', 'thr', 'bonus_pribadi', 'bonus_team', 
            'bonus_jackpot', 'terlambat', 'mangkir', 'saldo_kasbon', 
            'potongan_bpjs_kesehatan', 'potongan_bpjs_ketenagakerjaan', 
            'kasbon_obat', 'potongan_koperasi'
        ];
        foreach ($rupiahFields as $field) {
            if ($request->has($field)) {
                $val = str_replace('.', '', $request->get($field));
                $data[$field] = str_replace(',', '', $val);
            } else {
                $data[$field] = 0;
            }
        }

        // Keep standard inputs
        $data['izin_cuti'] = $request->izin_cuti ?? 12;
        $data['izin_lainnya'] = $request->izin_lainnya ?? 3;
        $data['izin_telat'] = $request->izin_telat ?? 3;
        $data['izin_pulang_cepat'] = $request->izin_pulang_cepat ?? 3;
        $data['cuti_melahirkan'] = $request->cuti_melahirkan ?? 90;
        $data['cuti_kematian'] = $request->cuti_kematian ?? 3;
        $data['batas_terlambat'] = $request->batas_terlambat ?? 5;

        if ($request->hasFile('foto_karyawan')) {
            $data['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan', 'public');
        }

        $data['password'] = Hash::make($request->password);
        $data['tipe_user'] = 'dosen';
        $data['status_aktif'] = true;

        $user = User::create($data);

        // Assign Roles
        if ($request->role) {
            foreach ($request->role as $roleName) {
                $user->assignRole($roleName);
            }
        } else {
            if (!\Spatie\Permission\Models\Role::where('name', 'dosen')->exists()) {
                \Spatie\Permission\Models\Role::create(['name' => 'dosen']);
            }
            $user->assignRole('dosen');
        }
        // Save dynamic documents
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
        return redirect('/dosen')->with('success', 'Data Dosen Berhasil Ditambahkan');
    }

    public function edit($id)
    {
        $user = User::dosen()->findOrFail($id);
        
        // Split current mata kuliah string to array
        $current_subjects = $user->mata_kuliah ? array_map('trim', explode(',', $user->mata_kuliah)) : [];

        return view('dosen.edit', [
            'title'   => 'Edit Dosen',
            'dosen'   => $user,
            'jabatan' => Jabatan::orderBy('nama_jabatan')->get(),
            'lokasi'  => Lokasi::where('status', 'approved')->orderBy('nama_lokasi')->get(),
            'roles'     => \Spatie\Permission\Models\Role::orderBy('name')->get(),
            'status_pajak' => \App\Models\StatusPajak::orderBy('id')->get(),
            'current_subjects' => $current_subjects,
            'skemas'    => \App\Models\MasterSkemaHonorarium::orderBy('nama_skema')->get(),
            'mata_kuliah' => \App\Models\MataKuliah::orderBy('nama_mk')->get()
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = User::dosen()->findOrFail($id);

        $request->validate([
            'name'            => 'required|max:255',
            'email'           => ['required', 'email', Rule::unique('users', 'email')->ignore($id)],
            'nidn'            => ['required', 'string', Rule::unique('users', 'nidn')->where('status_aktif', true)->ignore($id)],
            'nip'             => 'nullable|string|max:255',
            'gelar_depan'     => 'nullable|string|max:255',
            'gelar_belakang'  => 'nullable|string|max:255',
            'program_studi'   => 'nullable|string|max:255',
            'pendidikan_terakhir' => 'nullable|string|max:255',
            'status_kepegawaian' => 'nullable|string|max:255',
            'tipe_honorarium' => 'required|string|max:255',
            'nominal_honor'   => 'required',
            'jabatan_akademik' => 'nullable|string|max:255',
            'mata_kuliah'     => 'required|array',
            'telepon'         => 'required|string|max:20',
            'lokasi_id'       => 'required',
            'username'        => ['required', 'max:255', Rule::unique('users', 'username')->ignore($id)],
            'tgl_lahir'       => 'required|date',
            'tgl_join'        => 'required|date',
            'gender'          => 'required',
            'jabatan_id'      => 'required',
            'is_admin'        => 'required',
            'nama_ibu_kandung'=> 'required',
            'status_pajak_id' => 'required',
            'alamat'          => 'nullable',
            'alamat_domisili' => 'nullable',
            'master_skema_honorarium_id' => 'nullable|exists:master_skema_honorariums,id'
        ], [
            'nidn.unique' => 'NIDN sudah terdaftar.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        $data = $request->except(['password', 'role', 'nominal_honor', 'mata_kuliah', 'foto_karyawan']);

        $nominal_honor = $request->nominal_honor ? str_replace('.', '', $request->nominal_honor) : 0;
        $nominal_honor = str_replace(',', '', $nominal_honor);
        $data['nominal_honor'] = $nominal_honor;

        $mata_kuliah_str = $request->mata_kuliah ? implode(', ', $request->mata_kuliah) : null;
        $data['mata_kuliah'] = $mata_kuliah_str;

        // Clean Rupiah inputs from Penjumlahan Gaji & Potongan
        $rupiahFields = [
            'gaji_pokok', 'tunjangan_makan', 'tunjangan_transport', 
            'tunjangan_bpjs_kesehatan', 'tunjangan_bpjs_ketenagakerjaan', 
            'lembur', 'kehadiran', 'thr', 'bonus_pribadi', 'bonus_team', 
            'bonus_jackpot', 'terlambat', 'mangkir', 'saldo_kasbon', 
            'potongan_bpjs_kesehatan', 'potongan_bpjs_ketenagakerjaan', 
            'kasbon_obat', 'potongan_koperasi'
        ];
        foreach ($rupiahFields as $field) {
            if ($request->has($field)) {
                $val = str_replace('.', '', $request->get($field));
                $data[$field] = str_replace(',', '', $val);
            } else {
                $data[$field] = 0;
            }
        }

        if ($request->hasFile('foto_karyawan')) {
            $data['foto_karyawan'] = $request->file('foto_karyawan')->store('foto_karyawan', 'public');
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        // Update Roles
        if ($request->role) {
            $user->syncRoles($request->role);
        }

        return redirect('/dosen')->with('success', 'Data Dosen Berhasil Diupdate');
    }

    public function deactivate($id)
    {
        $user = User::dosen()->findOrFail($id);
        $user->update(['status_aktif' => false]);
        return redirect('/dosen')->with('success', 'Dosen Berhasil Dinonaktifkan');
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            // Matikan pengecekan foreign key sementara agar database tidak menolak penghapusan
            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // Hapus relasi data umum
            $relatedUserModels = [
                \App\Models\MappingShift::class,
                \App\Models\Lembur::class,
                \App\Models\Cuti::class,
                \App\Models\Sip::class,
                \App\Models\Payroll::class,
                \App\Models\File::class,
                \App\Models\dinasLuar::class,
                \App\Models\Kontrak::class,
                \App\Models\Kasbon::class,
                \App\Models\Kunjungan::class,
                \App\Models\Reimbursement::class,
                \App\Models\ReimbursementsItem::class,
                \App\Models\LaporanKinerja::class,
                \App\Models\LaporanKerja::class,
                \App\Models\Patroli::class,
                \App\Models\PegawaiKeluar::class,
                \App\Models\PengajuanKeuangan::class,
                \App\Models\Penugasan::class,
                \App\Models\RapatPegawai::class,
                \App\Models\TargetKinerjaTeam::class,
                \App\Models\KpiSubmission::class,
                \App\Models\KpiAssignment::class,
            ];

            foreach ($relatedUserModels as $model) {
                if (class_exists($model)) {
                    try {
                        $model::where('user_id', $id)->delete();
                    } catch (\Throwable $th) {
                        // Abaikan jika tabel/kolom belum ada
                    }
                }
            }

            // Hapus relasi khusus dosen
            $relatedDosenModels = [
                \App\Models\LogMengajar::class,
                \App\Models\Jadwal::class,
                \App\Models\LaporanMengajar::class,
                \App\Models\TransaksiMengajar::class,
                \App\Models\SesiDaring::class,
            ];

            foreach ($relatedDosenModels as $model) {
                if (class_exists($model)) {
                    try {
                        $model::where('dosen_id', $id)->delete();
                    } catch (\Throwable $th) {
                        // Abaikan
                    }
                }
            }

            if ($user->foto_karyawan && !empty($user->foto_karyawan)) {
                try {
                    Storage::delete($user->foto_karyawan);
                } catch (\Throwable $th) {}
            }

            $path = public_path('neural.json');
            if (\Illuminate\Support\Facades\File::exists($path)) {
                try {
                    $neural = \Illuminate\Support\Facades\File::get($path);
                    $dataface = json_decode($neural, true);
                    if (is_array($dataface)) {
                        $filterface = array_filter($dataface, function($item) use ($user) {
                            return isset($item['label']) && $item['label'] !== $user->username;
                        });
                        \Illuminate\Support\Facades\File::put($path, json_encode(array_values($filterface), JSON_PRETTY_PRINT));
                    }
                } catch (\Throwable $th) {}
            }

            $user->delete();

            \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;');
            return redirect('/dosen')->with('success', 'Data Dosen Berhasil Dihapus Permanen');
        } catch (\Throwable $e) {
            try { \Illuminate\Support\Facades\DB::statement('SET FOREIGN_KEY_CHECKS=1;'); } catch (\Throwable $th) {}
            return redirect('/dosen')->with('error', 'Gagal menghapus data dosen: ' . $e->getMessage());
        }
    }

    public function importDosen(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file_excel' => 'required|file|max:20480'
        ]);

        if ($validator->fails()) {
            Alert::error('Validasi Gagal', $validator->errors()->first());
            return back()->with('error', $validator->errors()->first());
        }

        try {
            $import = new UsersImport('dosen');
            Excel::import($import, $request->file('file_excel'));

            if ($import->getImportedCount() == 0) {
                Alert::error('Gagal', 'Tidak ada data yang diimport. Pastikan format kolom sesuai dengan template.');
                return back()->with('error', 'Tidak ada data yang diimport. Pastikan format kolom sesuai dengan template.');
            }

            Alert::success('Berhasil', $import->getImportedCount() . ' Data Dosen Berhasil Di Import');
            return back()->with('success', $import->getImportedCount() . ' Data Dosen Berhasil Di Import');
        } catch (\Throwable $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat mengimpor data dosen: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengimpor data dosen: ' . $e->getMessage());
        }
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\TemplateDosenExport, 'Template_Import_Dosen.xlsx');
    }

    public function exportDosen()
    {
        return Excel::download(new \App\Exports\DosenExport, 'Data_Dosen.xlsx');
    }
}

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

        return view('dosen.index', [
            'title'     => 'Data Dosen',
            'data_user' => $data,
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

    public function importDosen(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'file_excel' => 'required|file|max:20480'
        ]);

        if ($validator->fails()) {
            Alert::error('Validasi Gagal', $validator->errors()->first());
            return back()->with('error', $validator->errors()->first());
        }

        $filePath = null;
        try {
            $file = $request->file('file_excel');
            $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file->getClientOriginalName());
            $filePath = $file->storeAs('temp_imports', $fileName);
            $fullPath = storage_path('app/' . $filePath);

            Excel::import(new UsersImport('dosen'), $fullPath);

            Alert::success('Berhasil', 'Data Dosen Berhasil Di Import');
            return back()->with('success', 'Data Dosen Berhasil Di Import');
        } catch (\Throwable $e) {
            Alert::error('Gagal', 'Terjadi kesalahan saat mengimpor data dosen: ' . $e->getMessage());
            return back()->with('error', 'Gagal mengimpor data dosen: ' . $e->getMessage());
        } finally {
            if ($filePath && Storage::exists($filePath)) {
                Storage::delete($filePath);
            }
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=Template_Import_Dosen.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = [
            'Nama*', 'NIDN*', 'NIP', 'Email', 'Username*', 'Password*', 'Telepon', 'Divisi', 'Lokasi', 'Role',
            'Jabatan Akademik', 'Mata Kuliah', 'Gelar Depan', 'Gelar Belakang', 'Program Studi',
            'Pendidikan Terakhir', 'Status Kepegawaian', 'Tipe Honorarium', 'Nominal Honor',
            'Tanggal Lahir (YYYY-MM-DD)', 'Gender (L/P)', 'Tanggal Masuk (YYYY-MM-DD)'
        ];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, [
                'Dr. Ahmad Fauzi, M.T.', '0012345601', '198001012005011001', 'ahmad@univ.ac.id', 'ahmad_dosen', '12345678', '08198765432', 'Teknik Informatika', 'Kampus Utama', 'dosen',
                'Lektor', 'Pemrograman Web, Algoritma', 'Dr.', 'M.T.', 'Teknik Informatika',
                'S3', 'Dosen Tetap', 'Per Sesi', '150000',
                '1980-01-01', 'L', '2015-08-01'
            ]);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

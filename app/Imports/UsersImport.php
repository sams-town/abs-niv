<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Lokasi;
use App\Models\Jabatan;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class UsersImport implements ToModel, WithHeadingRow
{
    protected $defaultTipeUser;

    public function __construct($defaultTipeUser = 'pegawai')
    {
        $this->defaultTipeUser = $defaultTipeUser;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // 1. Resolve Name
        $name = $this->getValue($row, ['nama', 'name', 'nama_lengkap']);
        if (empty($name)) {
            return null;
        }

        // 2. Resolve Email, Username, & NIDN
        $email = $this->getValue($row, ['email']);
        $username = $this->getValue($row, ['username']);
        $nidn = $this->getValue($row, ['nidn']);

        if (empty($username)) {
            $username = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $name)) . rand(100, 999);
        }

        if (empty($email)) {
            $email = $username . '@absensi.local';
        }

        // 3. Resolve Role & Tipe User
        $roleName = $this->getValue($row, ['role', 'peran']);
        if (empty($roleName)) {
            $roleName = ($this->defaultTipeUser === 'dosen') ? 'dosen' : 'pegawai';
        }

        $tipeUser = $this->getValue($row, ['tipe_user', 'tipe', 'jenis_pegawai']);
        if (empty($tipeUser)) {
            if (!empty($nidn) || strtolower($roleName) === 'dosen' || $this->defaultTipeUser === 'dosen') {
                $tipeUser = 'dosen';
            } else {
                $tipeUser = 'pegawai';
            }
        } else {
            $tipeUser = strtolower($tipeUser);
            if ($tipeUser === 'karyawan') {
                $tipeUser = 'pegawai';
            }
        }

        $isAdmin = (in_array(strtolower($roleName), ['admin', 'super admin', 'superadmin'])) ? 'admin' : 'user';

        // 4. Resolve Jabatan & Lokasi
        $jabatanName = $this->getValue($row, ['divisi', 'jabatan', 'nama_jabatan', 'prodi', 'program_studi'], 'Umum');
        $jabatan = Jabatan::where('nama_jabatan', $jabatanName)->first();
        if (!$jabatan) {
            $jabatan = Jabatan::create([
                'nama_jabatan' => $jabatanName
            ]);
        }
        $jabatan_id = $jabatan->id;

        $lokasiName = $this->getValue($row, ['lokasi', 'nama_lokasi', 'cabang']);
        if (!empty($lokasiName)) {
            $lokasi = Lokasi::where('nama_lokasi', $lokasiName)->first();
            if (!$lokasi) {
                $lokasi = Lokasi::create([
                    'nama_lokasi' => $lokasiName,
                    'created_by' => auth()->id() ?? 1,
                    'status' => 'approved',
                    'keterangan' => 'Office'
                ]);
            }
            $lokasi_id = $lokasi->id;
        } else {
            $lokasiFirst = Lokasi::first();
            $lokasi_id = $lokasiFirst ? $lokasiFirst->id : null;
        }

        // 5. Check existing User
        $user = null;
        if (!empty($nidn)) {
            $user = User::where('nidn', $nidn)->first();
        }
        if (!$user && !empty($email)) {
            $user = User::where('email', $email)->first();
        }
        if (!$user && !empty($username)) {
            $user = User::where('username', $username)->first();
        }

        // 6. Build Data Payload
        $rawPassword = $this->getValue($row, ['password', 'pass']);

        $data = [
            'name' => $name,
            'email' => $email,
            'telepon' => (string) $this->getValue($row, ['telepon', 'phone', 'no_hp', 'hp', 'no_telepon'], ''),
            'username' => $username,
            'tgl_lahir' => $this->transformDate($this->getValue($row, ['tanggal_lahir', 'tgl_lahir', 'birth_date'])),
            'gender' => $this->getValue($row, ['gender', 'jenis_kelamin', 'jk']),
            'tgl_join' => $this->transformDate($this->getValue($row, ['tanggal_masuk_perusahaan', 'tgl_join', 'tanggal_join', 'join_date'])),
            'status_nikah' => $this->getValue($row, ['status_pernikahan', 'status_nikah', 'status']),
            'alamat' => $this->getValue($row, ['alamat', 'address']),
            'izin_cuti' => $this->transformNumber($this->getValue($row, ['cuti', 'izin_cuti']), 12),
            'izin_lainnya' => $this->transformNumber($this->getValue($row, ['izin_masuk', 'izin_lainnya']), 3),
            'izin_telat' => $this->transformNumber($this->getValue($row, ['izin_telat']), 3),
            'izin_pulang_cepat' => $this->transformNumber($this->getValue($row, ['izin_pulang_cepat']), 3),
            'is_admin' => $isAdmin,
            'tipe_user' => $tipeUser,
            'status_aktif' => true,
            'masa_berlaku' => $this->transformDate($this->getValue($row, ['masa_berlaku'])),
            'jabatan_id' => $jabatan_id,
            'lokasi_id' => $lokasi_id,
            'ktp' => (string) $this->getValue($row, ['ktp', 'no_ktp', 'nik'], ''),
            'kartu_keluarga' => (string) $this->getValue($row, ['kartu_keluarga', 'no_kk'], ''),
            'bpjs_kesehatan' => (string) $this->getValue($row, ['bpjs_kesehatan'], ''),
            'bpjs_ketenagakerjaan' => (string) $this->getValue($row, ['bpjs_ketenagakerjaan'], ''),
            'npwp' => (string) $this->getValue($row, ['npwp'], ''),
            'no_pkwt' => (string) $this->getValue($row, ['nomor_pkwt', 'no_pkwt'], ''),
            'no_kontrak' => (string) $this->getValue($row, ['nomor_kontrak', 'no_kontrak'], ''),
            'tanggal_mulai_pkwt' => $this->transformDate($this->getValue($row, ['tanggal_mulai_pkwt'])),
            'tanggal_berakhir_pkwt' => $this->transformDate($this->getValue($row, ['tanggal_berakhir_pkwt'])),
            'sim' => (string) $this->getValue($row, ['sim'], ''),
            'nama_rekening' => $this->getValue($row, ['nama_rekening']),
            'rekening' => (string) $this->getValue($row, ['rekening', 'no_rekening'], ''),
            'gaji_pokok' => $this->transformNumber($this->getValue($row, ['gaji_pokok']), 0),
            'tunjangan_makan' => $this->transformNumber($this->getValue($row, ['makan_dan_transport', 'tunjangan_makan']), 0),
            'lembur' => $this->transformNumber($this->getValue($row, ['lembur']), 0),
            'kehadiran' => $this->transformNumber($this->getValue($row, ['kehadiran']), 0),
            'thr' => $this->transformNumber($this->getValue($row, ['thr']), 0),
            'bonus_pribadi' => $this->transformNumber($this->getValue($row, ['bonus_pribadi']), 0),
            'bonus_team' => $this->transformNumber($this->getValue($row, ['bonus_team']), 0),
            'bonus_jackpot' => $this->transformNumber($this->getValue($row, ['bonus_jackpot']), 0),
            'izin' => $this->transformNumber($this->getValue($row, ['izin']), 0),
            'terlambat' => $this->transformNumber($this->getValue($row, ['terlambat']), 0),
            'mangkir' => $this->transformNumber($this->getValue($row, ['mangkir']), 0),
            'saldo_kasbon' => $this->transformNumber($this->getValue($row, ['saldo_kasbon']), 0),
            // Dosen specific fields
            'nidn' => (string) $this->getValue($row, ['nidn'], ''),
            'nip' => (string) $this->getValue($row, ['nip'], ''),
            'gelar_depan' => $this->getValue($row, ['gelar_depan']),
            'gelar_belakang' => $this->getValue($row, ['gelar_belakang']),
            'program_studi' => $this->getValue($row, ['program_studi', 'prodi']),
            'pendidikan_terakhir' => $this->getValue($row, ['pendidikan_terakhir', 'pendidikan']),
            'status_kepegawaian' => $this->getValue($row, ['status_kepegawaian']),
            'tipe_honorarium' => $this->getValue($row, ['tipe_honorarium'], 'Per Sesi'),
            'nominal_honor' => $this->transformNumber($this->getValue($row, ['nominal_honor']), 0),
            'jabatan_akademik' => $this->getValue($row, ['jabatan_akademik']),
            'mata_kuliah' => $this->getValue($row, ['mata_kuliah', 'matkul']),
        ];

        if (!empty($rawPassword)) {
            $data['password'] = Hash::make($rawPassword);
        }

        if ($user) {
            $user->update(array_filter($data, function($val) {
                return $val !== null;
            }));
        } else {
            if (empty($data['password'])) {
                $data['password'] = Hash::make('12345678');
            }
            $user = User::create($data);
        }

        // 7. Assign Role
        $role = Role::where('name', $roleName)->first();
        if (!$role) {
            $role = Role::create([
                'name' => $roleName,
                'guard_name' => 'web',
            ]);
        }
        $user->syncRoles([$roleName]);

        return $user;
    }

    private function getValue(array $row, array $keys, $default = null)
    {
        foreach ($keys as $key) {
            if (isset($row[$key]) && $row[$key] !== '' && $row[$key] !== null) {
                return trim((string)$row[$key]);
            }
        }
        return $default;
    }

    private function transformDate($value)
    {
        if (empty($value)) {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Date::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function transformNumber($value, $default = 0)
    {
        if ($value === null || $value === '') {
            return $default;
        }
        if (is_numeric($value)) {
            return $value;
        }
        $cleaned = preg_replace('/[^0-9.]/', '', str_replace(',', '', (string)$value));
        return is_numeric($cleaned) ? $cleaned : $default;
    }
}

